<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Service\WeekManager;
use Acts\CamdramBundle\Entity\TechieAdvert;
use Doctrine\Common\Collections\Criteria;

/**
 * @Rest\RouteResource("Techie")
 */
class TechieAdvertController extends AbstractFOSRestController
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
     * @Rest\Get("/vacancies/techies.{_format}", name="get_techies")
     */
    public function cget(Request $request, string $_format = 'html')
    {
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findNotExpiredOrderedByDateName(Time::now());

        $weeks = array();
        foreach ($techieAdverts as $advert) {
            $weeks[$advert->getShow()->getId()] = $advert->getShow()->getWeeks();
        }

        $view = $this->view($techieAdverts)
            ->setTemplate('techie_advert/index.'.$request->getRequestFormat().'.twig')
            ->setTemplateVar('techieadverts')
            ->setTemplateData(['weeks' => $weeks]);

        return $view;
    }

    public function getAction($identifier, Request $request)
    {
        $techieAdvert = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findOneByShowSlug($identifier, Time::now());
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
