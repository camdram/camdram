<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuditionController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository(Show::class)->findOneBy(array('slug' => $identifier));
    }

    private function getAuditionsForm(Show $show)
    {
        return $this->createForm(ShowAuditionsType::class, $show, ['method' => 'PUT']);
    }

    /**
     * @Route("/shows/{identifier}/auditions/edit", methods={"GET"}, name="edit_show_auditions")
     */
    public function editAuditionsAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);

        return $this->render('show/auditions-edit.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * @Route("/shows/{identifier}/auditions", methods={"PUT"}, name="put_show_auditions")
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

            return $this->redirectToRoute('get_show', array('identifier' => $show->getSlug()));
        } else {
        return $this->render('show/auditions-edit.html.twig',
            ['show' => $show, 'form' => $form->createView()])->setStatusCode(400);
        }
    }
}
