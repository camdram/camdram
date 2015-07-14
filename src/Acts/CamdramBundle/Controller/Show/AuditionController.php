<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class AuditionController extends FOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
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
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:auditions-edit.html.twig');
        }
    }
}
