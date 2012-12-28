<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Venue,
    Acts\CamdramBundle\Form\Type\VenueType;

use Ivory\GoogleMapBundle\Model\Events\MouseEvent,
    Ivory\GoogleMapBundle\Model\Overlays\Animation;

/**
 * @RouteResource("Venue")
 */
class VenueController extends FOSRestController
{
    public function newAction()
    {
        $form = $this->getForm();
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:Venue:new.html.twig');
    }

    public function postAction(Request $request)
    {
        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_venue', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Venue:new.html.twig');
        }
        return $this->render('ActsCamdramBundle:Venue:new.html.twig', array('form' => $form));
    }

    public function editAction($slug)
    {
        $venue = $this->getVenue($slug);
        $form = $this->getForm($venue);
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:Venue:edit.html.twig');
    }

    public function putAction(Request $request, $slug)
    {
        $venue = $this->getVenue($slug);
        $form = $this->getForm($venue);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView('get_venue', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Venue:edit.html.twig');
        }
        return $this->render('ActsCamdramBundle:Venue:edit.html.twig', array('form' => $form));
    }

    public function removeAction($slug)
    {
        $venue = $this->getVenue($slug);
        $em = $this->getDoctrine()->getManager();
        $em->remove($venue);
        $em->flush();
        return $this->routeRedirectView('get_venues');
    }

    public function cgetAction()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        $venues = $repo->findAllOrderedByName();

        $view = $this->view($venues, 200)
            ->setTemplate("ActsCamdramBundle:Venue:index.html.twig")
            ->setTemplateVar('venues')
        ;

        return $view;
    }

    public function getAction($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        $venue = $repo->findOneBySlug($slug);
        if (!$venue) {
        throw $this->createNotFoundException(
            'No venue found with the name '.$slug);
        }
        $view = $this->view($venue, 200)
            ->setTemplate("ActsCamdramBundle:Venue:show.html.twig")
            ->setTemplateVar('venue')
        ;
        
        return $view;
    }

    protected function getVenue($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        $venue = $repo->findOneBySlug($slug);

        if (!$venue) {
            throw $this->createNotFoundException('That venue does not exist');
        }

        return $venue;
    }

    protected function getForm($venue = null)
    {
        return $this->createForm(new VenueType(), $venue);
    }

    public function mapAction($slug = null)
    {

        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        if ($slug) {
            $venues = array($repo->findOneBySlug($slug));
        }
        else {
            $venues = $repo->findAllOrderedByName();
        }
        $map = $this->get('ivory_google_map.map');

        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');
        $map->setStylesheetOptions(array('width' => '100%', 'height' => '100%'));

        $one_venue = count($venues) == 1;

        if ($one_venue) {
            $map->setMapOption('zoom', 16);
            $map->setCenter($venues[0]->getLatitude(), $venues[0]->getLongitude(), true);
        }
        else {
            $map->setMapOption('zoom', 14);
            $map->setCenter(52.20531, 0.12179, true);
        }

        $letter = ord('A');
        $info_boxes = array();

        foreach ($venues as $venue) {

            if ($venue->getLatitude() && $venue->getLongitude()) {
                $content = $this->render('ActsCamdramBundle:Venue:infobox.html.twig', array(
                    'venue' => $venue,
                    'one_venue' => $one_venue,
                ))->getContent();

                $infoWindow = $this->get('ivory_google_map.info_window');
                $infoWindow->setPrefixJavascriptVariable('info_window_');
                $infoWindow->setPosition($venue->getLatitude(), $venue->getLongitude(), true);
                $infoWindow->setContent($content);
                $infoWindow->setAutoOpen(true);
                $infoWindow->setOpenEvent(MouseEvent::CLICK);
                $infoWindow->setAutoClose(true);
                $infoWindow->setOption('zIndex', 10);
                $map->addInfoWindow($infoWindow);

                $marker = $this->get('ivory_google_map.marker');
                $marker->setPrefixJavascriptVariable('marker_');
                $marker->setPosition($venue->getLatitude(), $venue->getLongitude(), true);
                if ($one_venue) {
                    $marker->setIcon($this->getMarkerUrl(''));
                }
                else {
                    $marker->setIcon($this->getMarkerUrl(chr($letter)));
                }
                $marker->setInfoWindow($infoWindow);
                $marker->setOption('clickable', true);
                $map->addMarker($marker);

                $info_boxes[] = array(
                    'image' => $this->getMarkerUrl(chr($letter)),
                    'box_id' => $infoWindow->getJavascriptVariable(),
                    'marker_id' => $marker->getJavascriptVariable(),
                    'map_id' => $map->getJavascriptVariable(),
                    'slug' => $venue->getSlug(),
                );

                $letter++;
                if ($letter == ord('Z') + 1) $letter = ord('A');
            }
        }

        return $this->render('ActsCamdramBundle:Venue:map.html.twig', array('map' => $map, 'info_boxes' => $info_boxes));
    }

    private function getMarkerUrl($letter)
    {
        return 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='.$letter.'|4499DD|000000';
    }
}
