<?php

namespace Acts\CamdramBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\EmailBuilder;
use Acts\CamdramBundle\Form\Type\EmailBuilderType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Acts\CamdramSecurityBundle\Entity\PendingAccess,
    Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class EmailBuilderController
 *
 * Controller for REST actions for email builder. Inherits from AbstractRestController.
 *
 * @package Acts\CamdramBundle\Controller

 * @RouteResource("EmailBuilder")
 */
class EmailBuilderController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\EmailBuilder';

    protected $type = 'emailbuilder';

    protected $type_plural = 'emailbuilders';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:EmailBuilder');
    }

    protected function getForm($emailBuilder = null)
    {
        return $this->createForm(new EmailBuilderType(), $emailBuilder, array('showRepository' => $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show')));
    }
    
    
    /**
     * We never show a read only version of the Email Builder
     */
    public function getAction($identifier)
    {
        return $this->editAction($identifier);
    }
    
    /**
     * Render the Admin Panel
     */
    public function adminPanelAction($identifier)
    {
        $emailBuilder = $this->getEntity($identifier);
        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($emailBuilder);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($emailBuilder);

        return $this->render(
            'ActsCamdramBundle:Emailbuilder:admin-panel.html.twig',
            array('emailbuilder' => $emailBuilder,
                  'admins' => $admins,
                  'pending_admins' => $pending_admins
                  )
            );
    }
    
    public function cgetAction(Request $request){
        $repo = $this->getRepository();
        if ($this->get('camdram.security.acl.helper')->isGranted('ROLE_ADMIN'))
        {
            $qb = $repo->selectAll()->getQuery();
        }
        else
        {
            $ids = $this->get('camdram.security.acl.provider')->getEmailBuilderIdsByUser($this->getUser());      
            
            $qb = $repo->queryByIds($ids);
        }

        $adapter = new DoctrineORMAdapter($qb);
        $data = new Pagerfanta($adapter);
        
        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
        ;

        return $view;
    }
    
    /**
     * Get a form for adding an admin to a show.
     *
     * @param $identifier
     */
    public function newAdminAction($identifier)
    {
        $emailBuilder = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $emailBuilder, false);

        $ace = new PendingAccess();
        $ace->setRid($emailBuilder->getId());
        $ace->setType('emailBuilder');
        $ace->setIssuer($this->getUser());
        $form = $this->createForm(new PendingAccessType(), $ace, array(
            'action' => $this->generateUrl('post_emailbuilder_admin', array('identifier' => $identifier))));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramSecurityBundle:PendingAccess:new.html.twig');
    }
    
    /**
     * Create a new admin associated with this emailBuilder.
     *
     * If the given email address isn't associated with an existing user, then
     * they will be given a pending access token, and invited via email to 
     * create an account.
     * 
     * @param $identifier
     */
    public function postAdminAction(Request $request, $identifier)
    {
        $emailBuilder = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $emailBuilder);

        $pending_ace = new PendingAccess();
        $form = $this->createForm(new PendingAccessType(), $pending_ace);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /* Check if the ACE doesn't need to be created for various reasons. */
            /* Is this person already an admin? */
            $already_admin = False;
            $admins = $this->get('acts.camdram.moderation_manager')
                        ->getModeratorsForEntity($emailBuilder);
            foreach ($admins as $admin) {
                if ($admin->getEmail() == $pending_ace->getEmail()) {
                    $already_admin = True;
                    break;
                }
            }
            if ($already_admin == False) {
                /* If this person is already a Camdram user then grant access immediately. */
                $em = $this->getDoctrine()->getManager();
                $existing_user = $em->getRepository('ActsCamdramSecurityBundle:User')
                                    ->findOneByEmail($pending_ace->getEmail());
                if ($existing_user != null) {
                    $this->get('camdram.security.acl.provider')
                        ->grantAccess($emailBuilder, $existing_user, $this->getUser());
                }
                else {
                    /* This is an unknown email address. Check if they've already
                     * got a pending access token for this resource, otherwise
                     * create the pending access token.
                     */
                    $pending_repo = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess');
                    if ($pending_repo->isDuplicate($pending_ace) == False) {
                        $pending_ace->setIssuer($this->getUser());
                        $em->persist($pending_ace);
                        $em->flush();
                    }
                }
            }
        }
        return $this->routeRedirectView('get_emailbuilder', array('identifier' => $emailBuilder->getSlug()));
    }


    
    public function putAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);

        $form->bind($request);
        if ($form->isValid()) {
        
            $oldRet = parent::putAction($request, $identifier);
        
            if ($form->get('SaveAndSend')->isClicked()) {
                $emailDispatcher = $this->get('acts.camdram.email_dispatcher');
                
                $data = $this->getTemplateData($identifier);
                
                $emailHtmlBody = $this->renderView(
                    'ActsCamdramBundle:Emailbuilder:emailtemplate.html.twig',
                    $data);
                    
                $emailTextBody = $this->renderView(
                    'ActsCamdramBundle:Emailbuilder:emailtemplate.txt.twig',
                    $data);                    
                
                $emailDispatcher->sendBuilderEmail($entity, $emailTextBody, $emailHtmlBody);
            }
            return $oldRet;
        }
        else
        {
            return parent::putAction($request, $identifier);
        }        
    }
  
    
     /**
     * Preview the current template - essentially generating the email template with wrapping <html> tags
     *
     * @Rest\Get("/emailbuilders/{identifier}/preview")
     * @param $identifier
     */
    public function getPreviewAction($identifier)
    {
        $data = $this->getTemplateData($identifier);
        return $this->view($data, 200)
            ->setTemplate('ActsCamdramBundle:Emailbuilder:preview.html.twig');
    }
    
    private function includeShow($emailBuilder, $show)
    {
        if($emailBuilder->getShowFilterMode() == EmailBuilder::FILTERMODEINCLUDE)
        {
            return $emailBuilder->getShowFilter()->contains($show);
        }
        if($emailBuilder->getShowFilterMode() == EmailBuilder::FILTERMODEEXCLUDE)
        {
            return ! ($emailBuilder->getShowFilter()->contains($show));
        }
        return true;
    }

    private function getTemplateData($identifier)
    {
        $emailbuilder = $this->getEntity($identifier);
               
        $rolesSearching = array();
        $shows = array();
        
        $data = array(
            'emailbuilder' => $emailbuilder,
            'showauditionsheader' => false,
            'showswithapplications' => false,
             );
             
        // Array with keys representing SoicetyIds needed.
        $organisationIdsToLoad = array();
        
        if($emailbuilder->getIncludeShowApplications() || $emailbuilder->getIncludeSocietyApplications()){
            $applications = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
                ->findScheduledOrderedByDeadline(new \DateTime(), new \DateTime("2034/1/1"));
            $nonShowApplications = array();
            
            foreach($applications as $application){

                $show = $application->getShow();

                if($show != null){

                    if(!$emailbuilder->getIncludeShowApplications()){
                        continue;
                    }
                    
                    if(!$this->includeShow($emailbuilder, $show)){
                        continue;
                    }                   

                    if(! array_key_exists($show->getId(), $shows))
                    {
                        $shows[$show->getId()] = array('show' => $show);
                    }
                    $shows[$show->getId()]['applications'] = $application;
                    $data['showswithapplications'] = true;
                }else{
                    if(!$emailbuilder->getIncludeSocietyApplications()){
                        continue;
                    }
                    $nonShowApplications[] = $application;                  
                    $organisationIdsToLoad[$application->getSociety()->getId()] = 1;
                }
            }
            
            if(count($nonShowApplications)>0){
                $data['nonShowApplications'] = $nonShowApplications;
            }
        }
        
        if($emailbuilder->getIncludeAuditions()){
            $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate();
            foreach($auditions as $audition){
                $show = $audition->getShow();
                
                if(!$this->includeShow($emailbuilder, $show)){
                    continue;
                }
                
                if(! array_key_exists($show->getId(), $shows))
                {
                    $shows[$show->getId()] = array('show' => $show);
                }
                if(! array_key_exists('auditions', $shows[$show->getId()]))
                {
                    $shows[$show->getId()]['auditions'] = array();
                }
                
                $shows[$show->getId()]['auditions'][] = $audition;
                $data['showauditionsheader'] = true;
            }            
        }
        
        
        if($emailbuilder->getIncludeTechieAdverts()){
            $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')->findCurrentOrderedByDateName();

            
            foreach($techieAdverts as $techieAdvert){
                $show = $techieAdvert->getShow();            
            
                $positions = $techieAdvert->getPositionsArray();
                
                if(!$this->includeShow($emailbuilder, $show)){
                    continue;
                }
                
                if(count($positions)>0){
                
                    foreach($positions as $position){
                        if(! array_key_exists($position, $rolesSearching)){
                            $rolesSearching[$position] = array('position' => $position, 'shows' => array());
                        }
                        $rolesSearching[$position]['shows'][] = $show;
                    }

                    if(! array_key_exists($show->getId(), $shows))
                    {
                        $shows[$show->getId()] = array('show' => $show);
                    }
                    
                    $shows[$show->getId()]['techieAdvert'] =  $techieAdvert;
                }
            }
    
            // Load all shows and their performances                
            $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->GetShowsWithAllPerformances(array_keys($shows));
            
            foreach(array_keys($rolesSearching) as $role)
            {
                uasort($rolesSearching[$role]['shows'], array($this, 'ShowDateCmp'));
            }
            
            uasort($rolesSearching, array($this, 'PositionCmp'));
                        
            if(count($rolesSearching) > 0)
            {
                $data['techieAdvertRoles'] = $rolesSearching;
            }
        }
        else
        {
            // Load all shows and their performances        
           $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->GetShowsWithAllPerformances(array_keys($shows));
        }
        
      
        foreach($shows as $showdata)
        {
            $society = $showdata['show']->getSociety();
            if(! is_null($society)){
                $organisationIdsToLoad[$society->getId()] = 1;
            }
            $venue = $showdata['show']->getVenue();
            if(! is_null($venue)){
                $organisationIdsToLoad[$venue->getId()] = 1;            
            }
        }
        
        $this->getDoctrine()->getRepository('ActsCamdramBundle:Organisation')->findById(array_keys($organisationIdsToLoad));
        
        uasort($shows, array($this, 'ShowDataDateCmp'));
        
        $data['shows'] = $shows;       
        
        return $data;
    }
    
    private function ShowDataDateCmp($a, $b){
        return $this->ShowDateCmp($a['show'],$b['show']);
    }
    
    private function ShowDateCmp($a, $b){
        $allPerformancesA = $a->getAllPerformances();
        $allPerformancesB = $b->getAllPerformances();
        
        if(count($allPerformancesA) == 0){
            if(count($allPerformancesB) == 0){
                return strcmp($a->getName(), $b ->getName());
            }
            return 1; // Shows with performances come before shows without
        }
        
        if($allPerformancesA[0]['datetime'] == $allPerformancesB[0]['datetime']){
                return strcmp($a->getName(), $b ->getName());
        }
        
        if($allPerformancesA[0]['datetime'] < $allPerformancesB[0]['datetime']){
            return -1;
        }
        return 1;
    }
    
    private function PositionCmp($a, $b){
        return strcmp( $a['position'], $b['position']);
    }


}
