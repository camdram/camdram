<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application,
    Acts\CamdramBundle\Entity\Person,
    Acts\CamdramBundle\Entity\Role,
    Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Event\CamdramEvents;
use Acts\CamdramBundle\Event\TechieAdvertEvent;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\RoleType;
use Acts\CamdramBundle\Form\Type\ShowType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

use Gedmo\Sluggable\Util as Sluggable;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("Show")
 */
class ShowController extends AbstractRestController
{

    protected $class = 'Acts\\CamdramBundle\\Entity\\Show';

    protected $type = 'show';

    protected $type_plural = 'shows';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
    }

    /**
     * Perform some data sanity checks before saving
     *
     * @param mixed $entity
     * @param null $oldEntity
     */
    protected function preSave($entity, $oldEntity=null)
    {
        /** @var $entity \Acts\CamdramBundle\Entity\Show */

        //ensure all the associated performances are also saved
        foreach ($entity->getPerformances() as $performance) {
            $performance->setShow($entity);
            $this->getDoctrine()->getManager()->persist($performance);
        }

        //ensure the venue attached to the show and to the performances are consistent
        $entity->updateVenues();
        //ensure the start_at and end_at fields are equal to the start and end of the first and last performances
        $entity->updateTimes();
    }

    protected function getEntity($identifier)
    {
        $show = parent::getEntity($identifier);
        //In order to simplify the interface, phasing out the 'excluding' field in performance date ranges. The method
        //below replaces any performance range with an 'excluding' field with two performance ranges.
        $show->fixPerformanceExcludes();
        return $show;
    }

    protected function getForm($show = null)
    {
        if (is_null($show)) {
            $show = new Show();
            $show->addPerformance(new Performance());
        }
        return $this->createForm(new ShowType(), $show);
    }

    private function getTechieAdvertForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new TechieAdvert();
            $obj->setShow($show);
        }
        $form = $this->createForm(new TechieAdvertType(), $obj);
        return $form;
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/new")
     */
    public function newTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/techie-advert")
     */
    public function postTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('event_dispatcher')->dispatch(CamdramEvents::TECHIE_ADVERT_CREATED, new TechieAdvertEvent($form->getData()));
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/edit")
     */
    public function editTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/techie-advert")
     */
    public function putTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('event_dispatcher')->dispatch(CamdramEvents::TECHIE_ADVERT_EDITED, new TechieAdvertEvent($form->getData()));
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
        }
    }

    private function getApplicationForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new Application();
            $obj->setShow($show);
        }
        $form = $this->createForm(new ApplicationType(), $obj);
        return $form;
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/new")
     */
    public function newApplicationAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:application-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/application")
     */
    public function postApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:application-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/edit")
     */
    public function editApplicationAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:application-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/techie-advert")
     */
    public function putApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:application-edit.html.twig');
        }
    }

    private function getAuditionsForm(Show $show)
    {
        return $this->createForm(new ShowAuditionsType(), $show);
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/auditions/edit")
     */
    public function editAuditionsAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:auditions-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/auditions")
     */
    public function putAuditionsAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:auditions-edit.html.twig');
        }
    }

    /**
     * Get a form for adding a single role to a show.
     *
     * @param $identifier
     */
    public function newRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $role = new Role();
        $role->setType($request->query->get('type'));
        $form = $this->createForm(new RoleType(), $role, array(
            'action' => $this->generateUrl('post_show_role', array('identifier' => $identifier))));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:role-new.html.twig');
    }

    /**
     * Create a new role associated with this show.
     *
     * Creates a new person if they're not already part of Camdram.
     * @param $identifier
     */
    public function postRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $base_role = new Role();
        $form = $this->createForm(new RoleType(), $base_role);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* Try and find the person. TODO slug will be unique, but not the best
             * way for searching. E.g. john-smith, john-smith1 may both be slugs.
             * A better approach may be to do a case-insensitive search by name and
             * choose the most recent record. Such behaviour is more than what the
             * existing codebase does.
             */
            $names = explode(",", $form->get('name')->getData());
            foreach ($names as $name) {
                $role = clone $base_role;
                $name = trim($name);
                $slug = Sluggable\Urlizer::urlize($name, '-');
                $person_repo = $em->getRepository('ActsCamdramBundle:Person');
                $person = $person_repo->findOneBySlug($slug);
                if ($person == null) {
                    $person = New Person();
                    $person->setName($name);
                    $person->setSlug($slug);
                    $em->persist($person);
                }
                $role->setPerson($person);
                $order = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')
                            ->getMaxOrderByShowType($show, $role->getType());
                $role->setOrder(++$order);
                $role->setShow($show);
                $em->persist($role);

                $person->addRole($role);
                $show->addRole($role);
                $em->flush();
            }
        }
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Remove a role from a show.
     */
    public function removeRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('role');
        $role_repo = $em->getRepository('ActsCamdramBundle:Role');
        $role = $role_repo->findOneById($id);
        if ($role != null) {
            $person = $role->getPerson();
            $show->removeRole($role);
            $role_repo->removeRoleFromOrder($role);
            $em->remove($role);
            $em->flush();
            // Ensure the person is not an orphan.
            if ($person->getRoles()->isEmpty()) {
                $em->remove($person);
                $em->flush();
            }
        }
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }
}

