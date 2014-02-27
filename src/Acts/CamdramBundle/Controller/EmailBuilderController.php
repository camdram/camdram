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
        return $this->createForm(new EmailBuilderType(), $emailBuilder);
    }
    
    
    /**
     * We never show a read only version of the Email Builder
     */
    public function getAction($identifier)
    {
        return $this->editAction($identifier);
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
        
        if($emailbuilder->getIncludeApplications()){
            $applications = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
                ->findScheduledOrderedByDeadline(new \DateTime(), new \DateTime("2034/1/1"));
            $nonShowApplications = array();
            
            foreach($applications as $application){
                if($application->getShow() != null){
                    $show = $application->getShow();
                    if(! array_key_exists($show->getId(), $shows))
                    {
                        $shows[$show->getId()] = array('show' => $show);
                    }
                    $shows[$show->getId()]['applications'] = $application;
                    $data['showswithapplications'] = true;
                }else{
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
                
                // todo: filter
                
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
        
        // Load all shows and their performances
        
        $allShows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->GetShowsWithAllPerformances(array_keys($shows));
       
        foreach($allShows as $show)
        {
            $society = $show->getSociety();
            if(! is_null($society)){
                $organisationIdsToLoad[$society->getId()] = 1;
            }
            $venue = $show->getVenue();
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
