<?php
namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\ContactUsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactUsController
 *
 * Very basic controller used by the Contact Us page
 *
 * @package Acts\CamdramBundle\Controller
 */

class ContactUsController extends Controller
{

    public function indexAction()
    {
        $form = $this->createForm(new ContactUsType());

        return $this->render('ActsCamdramBundle:ContactUs:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sendAction(Request $request)
    {
        $form = $this->createForm(new ContactUsType());
        $form->submit($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $this->get('acts.camdram.email_dispatcher')->sendContactUsEmail($data['email'], $data['subject'], $data['message']);
            return $this->redirect($this->generateUrl('acts_camdram_contact_us_sent'));
        }
        else {
            return $this->render('ActsCamdramBundle:ContactUs:index.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    public function sentAction()
    {
        return $this->render('ActsCamdramBundle:ContactUs:sent.html.twig');
    }

}
