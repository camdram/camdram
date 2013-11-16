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
class TechieAdvertController extends AbstractRestController
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
        $startDate = 
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findCurrentOrderedByDateName();

        $view = $this->view($techieAdverts, 200)
            ->setTemplate("ActsCamdramBundle:TechieAdvert:index.html.twig")
            ->setTemplateVar('techieadverts')
        ;
        return $view;
    }

    protected function getForm($advert = null)
    {
        return $this->createForm(new TechieAdvertType(), $advert);
    }
}
