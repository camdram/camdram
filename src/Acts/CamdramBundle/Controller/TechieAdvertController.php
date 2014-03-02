<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\TechieAdvert;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\BrowserKit\Request;


/**
 * @RouteResource("Techie")
 */
class TechieAdvertController extends FOSRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\TechieAdvert';

    protected $type = 'techie';

    protected $type_plural = 'techies';

    public function getController()
    {
        return 'TechieAdvert';
    }

    public function getRepository()
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
    }

    /**
     * cgetAction
     *
     * Display technician adverts from now until the end of (camdram) time
     */
    public function cgetAction()
    {
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findCurrentOrderedByDateName();

        $view = $this->view($techieAdverts, 200)
            ->setTemplate("ActsCamdramBundle:TechieAdvert:index.html.twig")
            ->setTemplateVar('techieadverts')
        ;
        return $view;
    }

    public function getAction($identifier)
    {
        $techieAdvert = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findOneByShowSlug($identifier);
        if ($techieAdvert) {
            return $this->redirect($this->generateUrl('get_techies').'#'.$techieAdvert->getShow()->getSlug());
        } else {
            return $this->createNotFoundException('No techie advert exists with that identifier');
        }
    }

}
