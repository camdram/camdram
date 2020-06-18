<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Service\Time;
use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractFOSRestController
{
    /**
     * Display application deadlines from now until the end of (camdram) time
     * @Route("/vacancies/applications.{_format}", format="html", methods={"GET"}, name="get_applications")
     */
    public function cgetAction(Request $request)
    {
        $applications = array_reverse($this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findLatest(-1, Time::now()));

        switch ($request->getRequestFormat()) {
        case 'html':
        case 'txt':
            return $this->render("application/index.{$request->getRequestFormat()}.twig",
                ['applications' => $applications]);
        default:
            return $this->view($applications);
        }
    }

    /**
     * @Route("/vacancies/applications/{identifier}.{_format}", format="html", methods={"GET"}, name="get_application")
     */
    public function getAction($identifier, Request $request)
    {
        $data = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findOneBySlug($identifier, Time::now());

        if (!$data) {
            throw $this->createNotFoundException('No application exists with that identifier');
        }

        if ($request->getRequestFormat() == 'html') {
            return $this->redirect($this->generateUrl('get_applications').'#'.$identifier);
        } else {
            return $this->view($data);
        }
    }
}
