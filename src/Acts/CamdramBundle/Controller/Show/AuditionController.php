<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;

class AuditionController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
    }

    private function getAuditionsForm(Show $show)
    {
        return $this->createForm(ShowAuditionsType::class, $show, ['method' => 'PUT']);
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/auditions/edit")
     */
    public function editAuditionsAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/auditions-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/auditions")
     */
    public function putAuditionsAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $formData = $form->getData();
            foreach ($formData->getAllAuditions() as $aud) {
                if ($aud->getNonScheduled()) {
                    $aud->setEndAt(clone ($aud->getStartAt()));
                }
            }
            $em->persist($formData);
            $em->flush();

            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('show/auditions-edit.html.twig');
        }
    }
}
