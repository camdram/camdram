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
     * Generate the Email from the current settings
     *
     * @Rest\Get("/emailbuilders/{identifier}/preview")
     * @param $identifier
     */
    public function getPreviewAction($identifier)
    {
        $emailbuilder = $this->getEntity($identifier);
               
        $rolesSearching = array();
        $shows = array();
        
        $data = array('emailbuilder' => $emailbuilder);
        
        
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

                    if(! array_key_exists($show->getSlug(), $shows))
                    {
                        $shows[$show->getSlug()] = array('show' => $show);
                    }
                    
                    $shows[$show->getSlug()]['techieAdvert'] =  $techieAdvert;
                }
            }
            
            uasort($rolesSearching, array($this, 'PositionCmp'));
            
            foreach($rolesSearching as $roleSearching)
            {
                uasort($roleSearching['shows'], array($this, 'ShowDateCmp'));
            }
    
            
            if(count($rolesSearching) > 0)
            {
                $data['techieAdvertRoles'] = $$rolesSearching;            
            }
        }            
        
        $data['shows'] = $shows;
        
        
        return $this->view($data, 200)
            ->setTemplate('ActsCamdramBundle:Emailbuilder:emailtemplate.html.twig');
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
