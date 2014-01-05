<?php
 
namespace Acts\CamdramBundle\Controller;
 
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Post;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Criteria;
 
use Acts\CamdramBundle\Form\Type\SupportType;
use Acts\CamdramBundle\Entity\Support;

/**
 * Controller for accessing support tickets created by emails to 
 * websupport@camdram.net. Information about how emails result in entries in
 * the database can be found at 
 * https://github.com/camdram/camdram/wiki/Inbound-Email 
 *
 * @RouteResource("Issue")
 */
class SupportController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\Support';

    protected $type = 'support';

    protected $type_plural = 'issues';

    protected $search_index = 'issue';

    protected function getRouteParams($issue)
    {
        return array('identifier' => $issue->getId());
    }

    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBy(array('id' => $identifier));

        if (!$entity) {
            throw $this->createNotFoundException('That issue does not exist.');
        }

        return $entity;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Support');
    }

    protected function getForm($society = null)
    {
        return Null;
    }

    /**
     * Ensure that the user's authenticated and authorised to view issues.
     */
    protected function checkAuthorised()
    {
        $this->get('camdram.security.utils')->ensureRole('IS_AUTHENTICATED_FULLY');
        $this->get('camdram.security.utils')->ensureRole('ROLE_ADMIN');
    }

    /**
     * Action which returns a collection of issues.
     *
     * Issues are grouped into those that are assigned to the logged in user,
     * unassigned issues, and issues assigned to other users.
     *
     */
    public function cgetAction(Request $request)
    {
        $this->checkAuthorised();
        $mine = $this->getDoctrine()->getRepository('ActsCamdramBundle:Support')->findBy(
                    array('state' => 'assigned', 'support_id' => 0, 'owner' => $this->getUser()->getId())
                    );
        $unassigned = $this->getDoctrine()->getRepository('ActsCamdramBundle:Support')->findBy(
                    array('state' => 'unassigned', 'support_id' => 0)
                    );
        $others = $this->getDoctrine()->getRepository('ActsCamdramBundle:Support')->findBy(
                    array('state' => 'assigned', 'support_id' => 0)
                    );            

        $view = $this->view(array('my_issues' => $mine, 
                                  'unassigned_issues' => $unassigned, 
                                  'other_peoples_issues' => $others),  200)
                  ->setTemplate("ActsCamdramBundle:Support:index.html.twig")
                  ->setTemplateVar('issues')
        ;
        return $view;
    }

    /**
     * @param $identifier
     * @Post("/issues/{identifier}/reply")
     */
    public function postReplyAction(Request $request, $identifier)
    {
        $reply = new Support();
        $form = $this->createForm(new SupportType(), $reply);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $from = addslashes(htmlspecialchars($this->getUser()->getName() . " <support-$identifier@camdram.net>"));
            $reply->setFrom($from);
            $reply->setTo(htmlspecialchars($reply->getTo()));
            $reply->setCc(htmlspecialchars($reply->getCc()));
            $reply->setSubject(htmlspecialchars($reply->getSubject()));
            $reply->setBody(str_replace(chr(13), "", $reply->getBody()));
            $reply->setSupportId(intval($identifier));
            $em->persist($reply);
            $em->flush();
            // TODO send the actual email.
        }
        return $this->redirect($this->generateUrl('get_issue',
                    array('identifier' => $identifier)));
    }

    /**
     * Action for pages that represent a single issue.
     *
     */
    public function getAction($identifier)
    {
        $this->checkAuthorised();
        $issue = $this->getEntity($identifier);
        if ($this->getRequest()->query->has('action')) {
            $action = $this->getRequest()->query->get('action');
            $user_is_owner =  $issue->getOwner()->getId() == $this->getUser()->getId() ? true : false;
            // Assign or reassign an issue.
            if ($action == 'assign') {
                $issue->setOwner($this->getUser());
                $issue->setState('assigned');
            }
            // Issue owners may reject issues that are assigned to them.
            else if (($action == 'rejectassign') && 
                     ($issue->getState() == 'assigned') && 
                     ($user_is_owner == true)) {
                $issue->setOwner(null);
                $issue->setState('unassigned');
            }
            // Admins may delete unassigned issues. Owners may resolve them. 
            else if ((($action == 'delete') && ($issue->getState() == 'unassigned')) || 
                     (($action == 'resolve') && ($issue->getState() == 'assigned') && ($user_is_owner == true))) {
                $issue->setState('closed');
            }
            else if ($action == 'reopen') {
                $issue->setOwner(null);
                $issue->setState('unassigned');
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();
        }

        $reply = new Support();
        $reply->setTo(htmlspecialchars_decode($issue->getFrom()));
        $reply->setSubject('Re: ' . $issue->getSubject());
        $reply->setBody("\n\n\n--\nSent by " . $this->getUser()->getName() . " on behalf of camdram.net websupport\nFor further correspondence relating to this email, contact support-" . $issue->getId() . "@camdram.net\nFor new enquries, contact websupport@camdram.net."
            );
        $form = $this->createForm(new SupportType(), $reply, array(
            'action' => $this->generateUrl('post_issue_reply', array('identifier' => $identifier))));

        $this->get('camdram.security.acl.helper')->ensureGranted('VIEW', $issue, false);
        $view = $this->view(array('issue' => $issue, 
                                  'form' => $form->createView()), 200)
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':show.html.twig')
        ;

        return $view;
    }
    
}

