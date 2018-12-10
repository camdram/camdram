<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\ShowType;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 *
 * @RouteResource("Show")
 */
class ShowController extends AbstractRestController
{
    protected $class = Show::class;

    protected $type = 'show';

    protected $type_plural = 'shows';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
    }

    protected function getForm($show = null, $method = 'POST')
    {
        if (is_null($show)) {
            $show = new Show();
            $show->addPerformance(new Performance());
        }

        return $this->createForm(ShowType::class, $show, ['method' => $method]);
    }

    public function cgetAction(Request $request)
    {
        if ($request->getRequestFormat() == 'rss') {
            $now = new \DateTime;
            $next_week = clone $now;
            $next_week->modify('+10 days');
            $shows = $this->getRepository()->findInDateRange($now, $next_week);

            return $this->view($shows);
        } else {
            return parent::cgetAction($request);
        }
    }

    public function getAction($identifier)
    {
        $show = $this->getRepository()->findOneBySlug($identifier);
        if (!$show) {
            $slugEntity = $this->getDoctrine()->getRepository('ActsCamdramBundle:ShowSlug')
                ->findOneBySlug($identifier);
            if (!$slugEntity) {
                throw $this->createNotFoundException('That '.$this->type.' does not exist');
            } else {
                return $this->redirectToRoute('get_show', ['identifier' => $slugEntity->getShow()->getSlug()]);
            }
        }

        $this->denyAccessUnlessGranted('VIEW', $show);

        $can_contact = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User')
            ->getContactableEntityOwners($show) > 0;
        
        $view = $this->view($show, 200)
            ->setTemplate('show/show.html.twig')
            ->setTemplateData(['show' => $show, 'can_contact' => $can_contact]);
        ;
        return $view;
    }
    
    /**
     * Action that allows querying by id. Redirects to slug URL
     * 
     * @Rest\Get("/shows/by-id/{id}")
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->checkAuthenticated();
        $show = $this->getRepository()->findOneById($id);
        
        if (!$show)
        {
            throw $this->createNotFoundException('That show id does not exist');
        }

        return $this->redirectToRoute('get_show', ['identifier' => $show->getSlug(), '_format' => $request->getRequestFormat()]);
    }

    public function postAction(Request $request)
    {
        return parent::postAction($request);
    }

    /**
     * Called by AbstractRestController before form goes to user.
     */
    public function modifyEditForm($form, $identifier) {
        // List of societies is public knowledge, no ACL checks here.
        $em = $this->getDoctrine()->getManager();
        $show = $this->getEntity($identifier);
        $socs = $show->getPrettySocData();
        foreach ($socs as &$soc) {
            $soc = $soc instanceof Society ? $soc->getName() : $soc["name"];
        }
        $form->get('societies')->setData($socs);
    }

    /**
     * Called by AbstractRestController after form sent by user.
     */
    public function afterEditFormSubmitted($form, $identifier) {
        $em   = $this->getDoctrine()->getManager();
        $show = $this->getEntity($identifier);

        $socRepo = $em->getRepository('ActsCamdramBundle:Society');
        $newSocs = [];   // Array of [string, Society]
        $newSocIds = [];
        $liveSocs = $show->getSocieties();
        $oldSocs = $liveSocs->toArray();
        $displayList = [];
        foreach ($form->get('societies')->getData() as $newSocName) {
            $newSoc = $socRepo->findOneByName($newSocName);
            $newSocs[] = [$newSocName, $newSoc];
            if ($newSoc) $newSocIds[] = $newSoc->getId();
        }
        // Erase societies from show.societies
        foreach ($oldSocs as $oldSoc) {
            if (!in_array($oldSoc->getId(), $newSocIds, true)) {
                $liveSocs->removeElement($oldSoc);
            }
        }
        foreach ($newSocs as $newSocData) {
            // Add societies to show.societies
            $newSociety = $newSocData[1];
            if ($newSociety && !$liveSocs->exists(function($key, $value) use ($newSociety) {
                return $value->getId() == $newSociety->getId();
            })) {
                $liveSocs->add($newSociety);
            }

            // Generate JSON representation
            $jsonRep = $newSociety ? $newSociety->getId() : $newSocData[0];
            if (!in_array($jsonRep, $displayList, true)) {
                $displayList[] = $jsonRep;
            }
        }
        $show->setSocietiesDisplayList($displayList);
    }

    private function getTechieAdvertForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new TechieAdvert();
            $obj->setShow($show);
        }
        $form = $this->createForm(TechieAdvertType::class, $obj);
        return $form;
    }

    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Show $show)
    {
        $em = $this->getDoctrine()->getManager();
        $admins = $this->get('camdram.security.acl.provider')->getOwners($show);
        $requested_admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getRequestedShowAdmins($show);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($show);
        $admins = array_merge($admins, $show->getSocieties()->toArray());

        if ($show->getVenue()) {
            $admins[] = $show->getVenue();
        }

        return $this->render(
            'show/admin-panel.html.twig',
            array('show' => $show,
                  'admins' => $admins,
                  'requested_admins' => $requested_admins,
                  'pending_admins' => $pending_admins)
            );
    }

    /**
     * Render the Search Result Panel. This view is used when a show is listed
     * in the search results.
     */
    public function searchResultPanelAction($slug)
    {
        $show = $this->getRepository()->findOneBySlug($slug);

        return $this->render(
            'show/search-result-panel.html.twig',
            array('show' => $show)
            );
    }

    public function editInlineAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        return $this->redirectToRoute('get_show', ['identifier' => $identifier]);
    }
    
    public function deleteImageAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('delete_show_image', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($show->getImage());
        $show->setImage(null);
        $em->flush();
        
        return $this->redirectToRoute('get_show', ['identifier' => $identifier]);
    }

    public function approveAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('APPROVE', $show);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('approve_show', $token)) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $this->get('acts.camdram.moderation_manager')->approveEntity($show);
        $this->get('doctrine.orm.entity_manager')->flush();

        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    public function unapproveAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('APPROVE', $show);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('unapprove_show', $token)) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $show->setAuthorised(false);
        $em->flush();

        return $this->redirectToRoute('get_show', ['identifier' => $identifier]);
    }

    public function getRolesAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $role_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role');
        $roles = $role_repo->findByShow($show);

        return $this->view($roles);
    }

    public function getPeopleAction($identifier)
    {
        return $this->getRolesAction($identifier);
    }
}
