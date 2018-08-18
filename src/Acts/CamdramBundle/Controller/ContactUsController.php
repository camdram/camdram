<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Acts\CamdramBundle\Service\EmailDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactUsController
 *
 * Very basic controller used by the Contact Us page
 */
class ContactUsController extends Controller
{
    public function indexAction()
    {
        $form = $this->createForm(ContactUsType::class);

        return $this->render('contact_us/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sendAction(Request $request, EmailDispatcher $emailDispatcher)
    {
        $form = $this->createForm(ContactUsType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $emailDispatcher->sendContactUsEmail($data['email'], $data['subject'], $data['message']);

            return $this->redirect($this->generateUrl('acts_camdram_contact_us_sent'));
        } else {
            return $this->render('contact_us/index.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    public function sentAction()
    {
        return $this->render('contact_us/sent.html.twig');
    }
}
