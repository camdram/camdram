<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        $numPasswordOnlyCam = count($repo->findActiveCamUsersWithoutExternalUser());
        $numPasswordOnlyOther = count($repo->findActiveNonCamUsersWithoutExternalUser());
        $numAdmins = count($repo->findOrganisationAdmins());
        
        $form = $this->createFormBuilder()
            ->add('subject', TextType::class)
            ->add('recipients', ChoiceType::class, [
                'choices' => [
                    "All Active Users ($numActiveUsers)" => 'active',
                    "Active cam.ac.uk users without an external login ($numPasswordOnlyCam)" => 'password_only_cam',
                    "Other active users without an external login ($numPasswordOnlyOther)" => 'password_only_other',
                    "Society and venue admins ($numAdmins)" => 'admins',
                    "Just me (1)" => 'me'
                ],
                'expanded' => true
            ])
            ->add('message', TextareaType::class, ['data' => self::SIGNATURE])
            ->getForm();
            
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->sendEmails($form->getData());
        }
        
        return $this->render('admin/mailout/index.html.twig',
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
            case 'password_only_cam':
                $users = $repo->findActiveCamUsersWithoutExternalUser();
                break;
            case 'password_only_other':
                $users = $repo->findActiveNonCamUsersWithoutExternalUser();
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
            if ($user->getLastLoginAt() < $loginThreshold && $user->getRegisteredAt() < $loginThreshold) {
                $output['not_active'][] = $user;
            } elseif (!$user->getIsEmailVerified()) {
                $output['not_verified'][] = $user;
            } else {
                $message = (new \Swift_Message($data['subject']))
                ->setFrom(self::FROM_EMAIL, self::FROM_NAME)
                ->setReturnPath(self::RETURN_EMAIL)
                ->setTo($user->getEmail(), $user->getName())
                ->setBody($data['message']);
                $mailer->send($message);
                $output['sent'][] = $user;
            }
        }
        
        return $this->render('admin/mailout/sent.html.twig',
                ['data' => $data, 'output' => $output]
        );
    }
}
