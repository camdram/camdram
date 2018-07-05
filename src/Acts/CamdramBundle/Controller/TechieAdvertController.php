<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Doctrine\Common\Collections\Criteria;

/**
 * @RouteResource("Techie")
 */
class TechieAdvertController extends FOSRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\TechieAdvert';

    protected $type = 'techie';

    protected $type_plural = 'techies';

    protected function getController()
    {
        return 'TechieAdvert';
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
    }

    /**
     * cgetAction
     *
     * Display technician adverts from now until the end of (camdram) time
     */
    public function cgetAction(Request $request)
    {
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findNotExpiredOrderedByDateName(new \DateTime());
        
        $week_manager = $this->get('acts.camdram.week_manager');
        $weeks = array();
        foreach ($techieAdverts as $advert) {
            $weeks[$advert->getShow()->getId()] = $week_manager->getPerformancesWeeksAsString($advert->getShow()->getPerformances());
        }
        
        $view = $this->view($techieAdverts)
            ->setTemplate('ActsCamdramBundle:TechieAdvert:index.'.$request->getRequestFormat().'.twig')
            ->setTemplateVar('techieadverts')
            ->setTemplateData(['weeks' => $weeks]);

        return $view;
    }

    public function getAction($identifier, Request $request)
    {
        $techieAdvert = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findOneByShowSlug($identifier, new \DateTime);
        if ($techieAdvert) {
            return $this->redirect($this->generateUrl('get_techies').'#'.$techieAdvert->getShow()->getSlug());
        } else {
            throw $this->createNotFoundException('No techie advert exists with that identifier');
        }

        if ($request->getRequestFormat() == 'html') {
            return $this->redirect($this->generateUrl('get_techie').'#'.$identifier);
        } else {
            return $this->view($data);
        }
    }
}
