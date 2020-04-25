<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{

    /**
     * @Route("/contact/{type}/{identifier}", name="contact_entity")
     *
     * @param Request $request
     * @param string $identifier
     */
    public function indexAction(Request $request, $type, $identifier)
    {
        $entity = $this->getEntity($type, $identifier);
        if (is_null($entity)) {
            throw $this->createNotFoundException("There is no $type called $identifier.");
        }
        $this->denyAccessUnlessGranted('VIEW', $entity);

        $form = $this->createForm(ContactUsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->get('acts.camdram.contact_entity_service')->emailEntity(
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

    private function getEntity($type, $identifier)
    {
        switch ($type) {
            case 'show':
                return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')
                    ->findOneBySlug($identifier);
            case 'society':
                return $this->getDoctrine()->getRepository('ActsCamdramBundle:Society')
                ->findOneBySlug($identifier);
            case 'venue':
                return $this->getDoctrine()->getRepository('ActsCamdramBundle:Venue')
                ->findOneBySlug($identifier);
            default:
                throw $this->createNotFoundException("No such entity type: $type.");
        }
    }
}
