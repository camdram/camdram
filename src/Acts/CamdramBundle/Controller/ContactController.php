<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Acts\CamdramBundle\Service\ContactEntityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact/{type}/{identifier}", name="contact_entity")
     */
    public function indexAction(Request $request, ContactEntityService $ces, string $type, string $identifier)
    {
        $entity = $this->getEntity($type, $identifier);
        if (is_null($entity)) {
            throw $this->createNotFoundException("There is no $type called $identifier. It may have been deleted since you followed this link.");
        }
        $this->denyAccessUnlessGranted('VIEW', $entity);

        $form = $this->createForm(ContactUsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $ces->emailEntity(
                $entity,
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            );

            return $this->render('contact/sent.html.twig', [
                'entity' => $entity,
                'type' => $type
            ]);
        }

        return $this->render('contact/index.html.twig', [
            'entity' => $entity,
            'type' => $type,
            'form' => $form->createView()
        ]);
    }

    /**
     * @return \Acts\CamdramBundle\Entity\Show|\Acts\CamdramBundle\Entity\Society|\Acts\CamdramBundle\Entity\Venue|null
     */
    private function getEntity(string $type, string $identifier)
    {
        if ($type === 'show') {
            $show = $this->getDoctrine()->getRepository('\Acts\CamdramBundle\Entity\Show')
                         ->findOneBySlug($identifier);
            if (!is_null($show)) return $show;

            $slug = $this->getDoctrine()->getRepository('\Acts\CamdramBundle\Entity\ShowSlug')
                                        ->findOneBySlug($identifier);
            return is_null($slug) ? null : $slug->getShow();
        }
        switch ($type) {
            case 'society':
                return $this->getDoctrine()->getRepository('\Acts\CamdramBundle\Entity\Society')
                ->findOneBySlug($identifier);
            case 'venue':
                return $this->getDoctrine()->getRepository('\Acts\CamdramBundle\Entity\Venue')
                ->findOneBySlug($identifier);
            default:
                throw $this->createNotFoundException("No such entity type: $type.");
        }
    }
}
