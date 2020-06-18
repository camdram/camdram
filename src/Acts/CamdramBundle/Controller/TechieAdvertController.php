<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Service\WeekManager;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/vacancies/techies.{_format}", format="html", methods={"GET"}, name="get_techies")
     */
    public function cget(Request $request, string $_format = 'html')
    {
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findNotExpiredOrderedByDateName(Time::now());

        $weeks = array();
        foreach ($techieAdverts as $advert) {
            $weeks[$advert->getShow()->getId()] = $advert->getShow()->getWeeks();
        }

        switch ($request->getRequestFormat()) {
        case 'html':
        case 'txt':
            return $this->render("techie_advert/index.{$request->getRequestFormat()}.twig",
                ['techieadverts' => $techieAdverts, 'weeks' => $weeks]);
        default:
            return $this->view($techieAdverts);
        }
    }

    /**
     * @Route("/vacancies/techies/{identifier}.{_format}", format="html", methods={"GET"}, name="get_techie")
     */
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
