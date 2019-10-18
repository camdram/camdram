<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Entity\Application;
use Doctrine\Common\Collections\Criteria;

/**
 * @RouteResource("Application")
 */
class ApplicationController extends AbstractFOSRestController
{
    /**
     * cgetAction
     *
     * Display application deadlines from now until the end of (camdram) time
     */
    public function cgetAction(Request $request)
    {
        $applications = array_reverse($this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findLatest(-1, Time::now()));

        $view = $this->view($applications, 200)
                  ->setTemplate('application/index.'.$request->getRequestFormat().'.twig')
                   ->setTemplateVar('applications')
               ;
        return $view;
    }

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
