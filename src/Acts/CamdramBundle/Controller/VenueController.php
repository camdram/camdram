<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Form\Type\VenueType;
use Ivory\GoogleMap\Event\MouseEvent;
use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Base\Coordinate;

/**
 * Class VenueController
 *
 * Controller for REST actions for venues. Inherits from AbstractRestController.
 *
 * @Rest\RouteResource("Venue")
 */
class VenueController extends OrganisationController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\Venue';

    protected $type = 'venue';

    protected $type_plural = 'venues';

    protected $search_index = 'venue';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
    }

    protected function getForm($venue = null)
    {
        return $this->createForm(VenueType::class, $venue);
    }
    
    public function getAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $venue);
        
        $can_contact = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User')
            ->getContactableEntityOwners($venue) > 0;
        
        $view = $this->view($venue, 200)
            ->setTemplate('ActsCamdramBundle:Venue:show.html.twig')
            ->setTemplateData(['venue' => $venue, 'can_contact' => $can_contact])
        ;
        
        return $view;
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     * 
     * @Rest\Get("/venues/by-id/{id}")
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->checkAuthenticated();
        $venue = $this->getRepository()->findOneById($id);
        
        if (!$venue)
        {
            throw $this->createNotFoundException('That venue id does not exist');
        }

        return $this->redirectToRoute('get_venue', ['identifier' => $venue->getSlug(), '_format' => $request->getRequestFormat()]);
    }

    /**
     * We don't want the default behaviour of paginated results - just output all of them unless there's a query
     * parameter specified.
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return parent::cgetAction($request);
        }

        $venues = $this->getRepository()->findAllOrderedByName();

        $view = $this->view($venues, 200)
            ->setTemplateVar('venues')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
        ;

        return $view;
    }

    public function getVacanciesAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition');
        $techie_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
        $applications_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application');
        $now = new \DateTime();

        $data = array(
            'venue' => $venue,
            'auditions' => $auditions_repo->findUpcomingByVenue($venue, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledByVenue($venue, 10, $now),
            'techie_ads' => $techie_repo->findLatestByVenue($venue, 10, $now),
            'app_ads' => $applications_repo->findLatestByVenue($venue, 10, $now),
        );

        return $this->view($data, 200)
            ->setTemplateVar('vacancies')
            ->setTemplate('ActsCamdramBundle:Venue:vacancies.html.twig')
        ;
    }

    /**
     * Render a Google Map in an iframe. If $identifier is specified then a small map will be output with a single
     * marker. Otherwise a large map will be output with a marker for each venue.
     *
     * @param null|string $identifier
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mapAction($identifier = null)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        if ($identifier) {
            $venues = array($repo->findOneBySlug($identifier));
        } else {
            $venues = $repo->findAllOrderedByName();
        }
        $map = new Map;

        $map->setHtmlId('map_canvas');
        $map->setStylesheetOptions(array('width' => '100%', 'height' => '100%'));

        $one_venue = count($venues) == 1;

        if ($one_venue) {
            $map->setMapOption('zoom', 16);
            $map->setCenter(new Coordinate($venues[0]->getLatitude(), $venues[0]->getLongitude()));
        } else {
            $map->setMapOption('zoom', 14);
            $map->setCenter(new Coordinate(52.20531, 0.12179));
        }

        $letter = ord('A');
        $info_boxes = array();

        foreach ($venues as $venue) {
            if ($venue->getLatitude() && $venue->getLongitude()) {
                $content = $this->render('ActsCamdramBundle:Venue:infobox.html.twig', array(
                    'venue' => $venue,
                    'one_venue' => $one_venue,
                ))->getContent();

                $infoWindow = new InfoWindow($content);
                $infoWindow->setPosition(new Coordinate($venue->getLatitude(), $venue->getLongitude()));
                $infoWindow->setAutoOpen(true);
                $infoWindow->setOpenEvent(MouseEvent::CLICK);
                $infoWindow->setAutoClose(true);
                $infoWindow->setOption('zIndex', 10);
                $map->getOverlayManager()->addInfoWindow($infoWindow);

                $marker = new Marker(new Coordinate($venue->getLatitude(), $venue->getLongitude()));
                if ($one_venue) {
                    $marker->setIcon(new Icon($this->getMarkerUrl('')));
                } else {
                    $marker->setIcon(new Icon($this->getMarkerUrl(chr($letter))));
                }
                $marker->setInfoWindow($infoWindow);
                $marker->setOption('clickable', true);
                $map->getOverlayManager()->addMarker($marker);

                $info_boxes[] = array(
                    'image' => $this->getMarkerUrl(chr($letter)),
                    'box_id' => $infoWindow->getVariable(),
                    'marker_id' => $marker->getVariable(),
                    'map_id' => $map->getVariable(),
                    'slug' => $venue->getSlug(),
                );

                $letter++;
                if ($letter == ord('Z') + 1) {
                    $letter = ord('A');
                }
            }
        }

        return $this->render('ActsCamdramBundle:Venue:map.html.twig', array('map' => $map, 'info_boxes' => $info_boxes));
    }

    /**
     * Utility function used by mapAction to generate the URL of a marker image.
     *
     * @param $letter letter of the alphabet used in the marker
     *
     * @return string
     */
    private function getMarkerUrl($letter)
    {
        return 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='.$letter.'|4499DD|000000';
    }

    /**
     * Finds all performances in the selected venue (used by OrgansiationController).
     *
     * @param $slug
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    protected function getPerformances($slug, \DateTime $from, \DateTime $to)
    {
        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');

        return $performance_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

    /**
     * Finds all shows in the selected venue (used by OrgansiationController).
     *
     * @param $slug
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    protected function getShows($slug, \DateTime $from, \DateTime $to)
    {
        $show_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');

        return $show_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

    public function removeImageAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $venue);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($venue->getImage());
        $venue->setImage(null);
        $em->flush();
        
        return $this->redirectToRoute('get_venue', ['identifier' => $identifier]);
    }
}
