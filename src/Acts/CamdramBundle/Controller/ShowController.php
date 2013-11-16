<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Event\CamdramEvents;
use Acts\CamdramBundle\Event\TechieAdvertEvent;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\ShowType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("Show")
 */
class ShowController extends AbstractRestController
{

    protected $class = 'Acts\\CamdramBundle\\Entity\\Show';

    protected $type = 'show';

    protected $type_plural = 'shows';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
    }

    /**
     * Perform some data sanity checks before saving
     *
     * @param mixed $entity
     * @param null $oldEntity
     */
    protected function preSave($entity, $oldEntity=null)
    {
        /** @var $entity \Acts\CamdramBundle\Entity\Show */

        //ensure all the associated performances are also saved
        foreach ($entity->getPerformances() as $performance) {
            $performance->setShow($entity);
            $this->getDoctrine()->getManager()->persist($performance);
        }

        //ensure the venue attached to the show and to the performances are consistent
        $entity->updateVenues();
        //ensure the start_at and end_at fields are equal to the start and end of the first and last performances
        $entity->updateTimes();
    }

    protected function getEntity($identifier)
    {
        $show = parent::getEntity($identifier);
        //In order to simplify the interface, phasing out the 'excluding' field in performance date ranges. The method
        //below replaces any performance range with an 'excluding' field with two performance ranges.
        $show->fixPerformanceExcludes();
        return $show;
    }

    protected function getForm($show = null)
    {
        if (is_null($show)) {
            $show = new Show();
            $show->addPerformance(new Performance());
        }
        return $this->createForm(new ShowType(), $show);
    }

    private function getTechieAdvertForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new TechieAdvert();
            $obj->setShow($show);
        }
        $form = $this->createForm(new TechieAdvertType(), $obj);
        return $form;
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/new")
     */
    public function newTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/techie-advert")
     */
    public function postTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('event_dispatcher')->dispatch(CamdramEvents::TECHIE_ADVERT_CREATED, new TechieAdvertEvent($form->getData()));
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/edit")
     */
    public function editTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/techie-advert")
     */
    public function putTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('event_dispatcher')->dispatch(CamdramEvents::TECHIE_ADVERT_EDITED, new TechieAdvertEvent($form->getData()));
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
        }
    }
}
