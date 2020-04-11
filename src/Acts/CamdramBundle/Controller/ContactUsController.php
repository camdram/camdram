<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Acts\CamdramBundle\Service\EmailDispatcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactUsController
 *
 * Very basic controller used by the Contact Us page
 */
class ContactUsController extends AbstractController
{

    /**
     * @Route("/contact-us", methods={"GET"}, name="acts_camdram_contact_us")
     */
    public function indexAction()
    {
        $form = $this->createForm(ContactUsType::class);

        return $this->render('contact_us/index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/contact-us", methods={"POST"}, name="acts_camdram_contact_us_send")
     */
    public function sendAction(Request $request, EmailDispatcher $emailDispatcher)
    {
        $form = $this->createForm(ContactUsType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $emailDispatcher->sendContactUsEmail($data['email'], $data['name'], $data['subject'], $data['message']);

            return $this->redirect($this->generateUrl('acts_camdram_contact_us_sent'));
        } else {
            return $this->render('contact_us/index.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @Route("/contact-us/sent", name="acts_camdram_contact_us_sent")
     */
    public function sentAction()
    {
        return $this->render('contact_us/sent.html.twig');
    }
}
