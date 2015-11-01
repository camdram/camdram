<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Symfony\Component\HttpFoundation\Request;

trait ContactTrait
{
    abstract protected function redirectToRoute($route, array $parameters = array(), $status = 302);

    abstract public function get($service);

    abstract protected function getEntity($identifier);

    abstract protected function getController();

    public function getContactAction(Request $request, $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $entity, false);
        $form = $this->createForm(new ContactUsType($this->get('security.token_storage')));

        return $this->render('@ActsCamdram/' . $this->getController() .'/contact.html.twig', [
            'entity' => $entity,
            'form' => $form->createView()
        ]);
    }

    public function postContactAction(Request $request, $identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $entity, false);
        $form = $this->createForm(new ContactUsType($this->get('security.token_storage')));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->get('acts.camdram.contact_entity_service')->emailEntity($entity, $data['name'],
                $data['email'], $data['subject'], $data['message']);

            return $this->redirectToRoute('get_'.strtolower($this->getController()).'_sent', ['identifier' => $identifier]);
        } else {
            return $this->render('@ActsCamdram/' . $this->getController() . '/contact.html.twig', [
                'entity' => $entity,
                'form' => $form->createView()
            ]);
        }
    }

    public function getSentAction($identifier)
    {
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $entity, false);

        return $this->render('@ActsCamdram/' . $this->getController() . '/contact-sent.html.twig', [
            'entity' => $entity
        ]);
    }
}
