<?php
namespace Acts\CamdramLegacyBundle\EventListener;

use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

class RedirectHandler {

    private $router;

    private $entityManager;

    public function __construct(RouterInterface $router, EntityManager $entityManager) {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if($exception->getStatusCode() == 404)
        {
            if ($response = $this->handleRedirect($event->getRequest())) {
                $event->setResponse($response);
            }
        }
    }

    public function handleRedirect(Request $request) {
        $path = $request->getPathInfo();
        $query = $request->query;

        if (($resp = $this->handleStatic($path))) {
            return $resp;
        }
        elseif ($path == '/micro.php') {
            return $this->handleMicro($query);
        }
        elseif (substr_count($path, '/societies') > 0) {
            return $this->handleSocieties($path);
        }
        elseif (substr_count($path, '/shows') > 0) {
            return $this->handleShows($path, $query);
        }
    }

    private function handleMicro(ParameterBag $query) {
        switch ($query->get('id')) {
            //e.g. /micro.php?id=104&showid=2465
            case 104: return $this->createShowResponseFromId($query->get('showid'));
            //e.g. /micro.php?id=105&person=6391
            case 105: return $this->createPersonResponseFromId($query->get('person'));
        }
    }

    private function handleShows($path, ParameterBag $query) {
        if ($path == '/shows' && $query->has('showid')) {
            return $this->createShowResponseFromId($query->get('showid'));
        }
        elseif ($path == '/shows/view/person') {
            return $this->createPersonResponseFromId($query->get('person'));
        }
        else {
            $ref = substr($path, 7);
            return $this->createShowResponseFromRef($ref);
        }
    }

    private function handleSocieties($path) {
        $short_name = substr($path, 11);
        $short_name = str_replace('_', ' ', $short_name);
        return $this->createOrganisationResponseFromShortName($short_name);
    }

    private function handleStatic($path) {
        $map = array(
            '/shows/diary' => 'acts_camdram_diary',
            '/shows/archive' => 'acts_camdram_homepage',
            '/positions/actors'  => 'get_auditions',
            '/positions/production'  => 'get_techies',
            '/positions/directors_producers'  => 'get_applications',
            '/privacy' => 'acts_camdram_privacy',
            '/signup' => 'acts_camdram_security_create_account',
        );

        if (isset($map[$path])) {
            return new RedirectResponse($this->router->generate($map[$path]), 301);
        }
    }

    private function createShowResponseFromId($show_id) {
        $show_repo = $this->entityManager->getRepository('ActsCamdramBundle:Show');
        if (($show = $show_repo->findOneById($show_id))) {
            return new RedirectResponse($this->router->generate('get_show', array('identifier' => $show->getSlug()), 301));
        }
    }

    private function createShowResponseFromRef($ref_name) {
        $ref_repo = $this->entityManager->getRepository('ActsCamdramLegacyBundle:ShowRef');
        if (($ref = $ref_repo->findOneByRef($ref_name))) {
            if ($ref->getShow()) {
                return new RedirectResponse($this->router->generate('get_show', array('identifier' => $ref->getShow()->getSlug()), 301));
            }
        }
    }

    private function createPersonResponseFromId($person_id) {
        $person_repo = $this->entityManager->getRepository('ActsCamdramBundle:Person');
        if (($person = $person_repo->findOneById($person_id))) {
            return new RedirectResponse($this->router->generate('get_person', array('identifier' => $person->getSlug()), 301));
        }
    }

    private function createOrganisationResponseFromShortName($short_name) {
        $org_repo = $this->entityManager->getRepository('ActsCamdramBundle:Organisation');
        if (($org = $org_repo->findOneBy(array('short_name' => $short_name)))) {
            if ($org instanceof Society) {
                return new RedirectResponse($this->router->generate('get_society', array('identifier' => $org->getSlug()), 301));
            }
            elseif ($org instanceof Venue) {
                return new RedirectResponse($this->router->generate('get_venue', array('identifier' => $org->getSlug()), 301));
            }
        }
    }
}