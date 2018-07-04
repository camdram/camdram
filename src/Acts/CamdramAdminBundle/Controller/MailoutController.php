<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 *
 * @Security("has_role('ROLE_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class MailoutController extends Controller
{
    const SIGNATURE = "


--
Sent by the Camdram administration team.
For any enquiries, please contact websupport@camdram.net.";
    
    const FROM_NAME = "Camdram";
    
    const FROM_EMAIL = "websupport@camdram.net";
    
    const RETURN_EMAIL = "support-bounces@camdram.net";
    
    /**
     * @Route("/mailout", name="acts_camdram_mailout")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User');
        $numActiveUsers = count($repo->findActiveUsersForMailOut());
        $numAdmins = count($repo->findOrganisationAdmins());
        
        $form = $this->createFormBuilder()
            ->add('subject', 'text')
            ->add('recipients', 'choice', [
                'choices' => [
                    "All Active Users ($numActiveUsers)" => 'active',
                    "Society and venue admins ($numAdmins)" => 'admins',
                    "Just me (1)" => 'me'
                ],
                'choices_as_values' => true,
                'expanded' => true
            ])
            ->add('message', 'textarea', ['data' => self::SIGNATURE])
            ->getForm();
            
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->sendEmails($form->getData());
        }
        
        return $this->render(
        
            'ActsCamdramAdminBundle:Mailout:index.html.twig',
            ['form' => $form->createView()]
        
        );
    }
    
    private function sendEmails($data)
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User');
        
        switch ($data['recipients']) {
            case 'active':
                $users = $repo->findActiveUsersForMailOut();
                break;
            case 'admins':
                $users = $repo->findOrganisationAdmins();
                break;
            case 'me':
                $users = [$this->getUser()];
                break;
        }
        
        $mailer = $this->get('mailer');
        $output = ['sent' => [], 'not_active' => [], 'not_verified' => []];
        $loginThreshold = new \DateTime('-2 years');
        
        foreach ($users as $user) {
            if ($user->getLastLoginAt() < $loginThreshold) {
                $output['not_verified'][] = $user;
            } elseif (!$user->getIsEmailVerified()) {
                $output['not_active'][] = $user;
            } else {
                $message = \Swift_Message::newInstance()
                ->setSubject($data['subject'])
                ->setFrom(self::FROM_EMAIL, self::FROM_NAME)
                ->setReturnPath(self::RETURN_EMAIL)
                ->setTo($user->getFullEmail(), $user->getName())
                ->setBody($data['message']);
                $mailer->send($message);
                $output['sent'][] = $user;
            }
        }
        
        return $this->render(
        
            'ActsCamdramAdminBundle:Mailout:sent.html.twig',
                ['data' => $data, 'output' => $output]
        
        );
    }
}
