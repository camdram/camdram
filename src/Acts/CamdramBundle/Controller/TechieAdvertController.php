<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Entity\TechieAdvert;

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
        $view = $this->view($techieAdverts, 200)
            ->setTemplate('ActsCamdramBundle:TechieAdvert:index.'.$request->getRequestFormat().'.twig')
            ->setTemplateVar('techieadverts')
        ;

        return $view;
    }

    public function getAction($identifier)
    {
        $techieAdvert = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findOneByShowSlug($identifier, new \DateTime());
        if ($techieAdvert) {
            return $this->redirect($this->generateUrl('get_techies').'#'.$techieAdvert->getShow()->getSlug());
        } else {
            throw $this->createNotFoundException('No techie advert exists with that identifier');
        }
    }
}
