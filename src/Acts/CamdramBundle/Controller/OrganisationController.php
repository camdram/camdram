<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramBundle\Form\Type\OrganisationAdvertType;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use Acts\DiaryBundle\Diary\Diary;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrganisationController
 *
 * Abstract controller for REST actions for organisations. Inherits from AbstractRestController.
 * @template T of \Acts\CamdramBundle\Entity\Organisation
 * @extends AbstractRestController<T>
 */
abstract class OrganisationController extends AbstractRestController
{
    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Organisation $org)
    {
        $em = $this->getDoctrine()->getManager();
        $admins = $this->get('camdram.security.acl.provider')->getOwners($org);
        $pending_admins = $em->getRepository(PendingAccess::class)->findByResource($org);

        return $this->render(
            $this->type.'/admin-panel.html.twig',
            array('org' => $org,
                'admins' => $admins,
                'pending_admins' => $pending_admins)
        );
    }

    /**
     * @Route("/{identifier}/news.{_format}", format="html", methods={"GET"})
     */
    public function getNewsAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $news_repo = $this->getDoctrine()->getRepository(Entity\News::class);

        return $this->show('organisation/news.html.twig', 'news', $news_repo->getRecentByOrganisation($org, 30));
    }

    abstract protected function getPerformances($slug, \DateTime $from, \DateTime $to);

    abstract protected function getShows($slug, \DateTime $from, \DateTime $to);

    /**
     * Render a diary of the shows put on by this society.
     * @Route("/{identifier}/shows.{_format}", format="html", methods={"GET"})
     */
    public function getShowsAction(Request $request, $identifier)
    {
        if ($request->getRequestFormat() == 'html') {
            throw new NotFoundHttpException("This is part of our API, add a .json or .xml suffix.");
        }
        try {
            if ($request->query->has('from')) {
                $from = new \DateTime($request->query->get('from'));
            } else {
                $from = new \DateTime;
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad from parameter, try YYYY-MM-DD format.");
        }

        try {
            if ($request->query->has('to')) {
                $to = new \DateTime($request->query->get('to'));
            } else {
                $to = clone $from;
                $to->modify('+1 year');
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad to parameter, try YYYY-MM-DD format.");
        }

        $shows = $this->getShows($identifier, $from, $to);

        return $this->view($shows, 200);
    }

    /**
     * Render a diary of the shows put on by this society.
     *
     * @Route("/{identifier}/diary.{_format}", format="html")
     */
    public function getDiaryAction(Request $request, $identifier)
    {
        $diary = new Diary;

        try {
            if ($request->query->has('from')) {
                $from = new \DateTime($request->query->get('from'));
            } else {
                $from = new \DateTime;
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad from parameter, try YYYY-MM-DD format.");
        }

        try {
            if ($request->query->has('to')) {
                $to = new \DateTime($request->query->get('to'));
            } else {
                $to = clone $from;
                $to->modify('+1 year');
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad to parameter, try YYYY-MM-DD format.");
        }

        $performances = $this->getPerformances($identifier, $from, $to);
        $diary->addEvents($performances);

        return $this->show('organisation/diary.html.twig', 'diary', $diary);
    }

    /**
     * Redirect from /events -> /diary for backwards compatibility
     * @Route("/{identifier}/events.{_format}", format="html")
     */
    public function getEventsAction(Request $request, $identifier)
    {
        return $this->redirect($this->generateUrl('get_'.$this->type.'_diary',
            ['identifier' => $identifier, '_format' => $request->getRequestFormat()]));
    }

    private function getAdvertForm(Organisation $org, $obj = null, $method = 'POST')
    {
        if (!$obj) {
            $obj = new Advert();
            $obj->setType(Advert::TYPE_APPLICATION);
            if ($org instanceof Society) {
                $obj->setSociety($org);
            } else if ($org instanceof Venue) {
                $obj->setVenue($org);
            } else throw new \LogicException();
        }
        $form = $this->createForm(OrganisationAdvertType::class, $obj, ['method' => $method]);

        return $form;
    }

    /**
     * @Route("/{identifier}/adverts", methods={"GET"})
     */
    public function advertsAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        return $this->render($this->type.'/adverts.html.twig', [
            'org' => $org,
        ]);
    }

    /**
     * @Route("/{identifier}/adverts/new", methods={"GET"})
     */
    public function newAdvertAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getAdvertForm($org);

        return $this->render($this->type.'/application-new.html.twig',
            ['org' => $org, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{identifier}/adverts", methods={"POST"})
     */
    public function postAdvertAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getAdvertForm($org);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('acts_camdram_'.$this->type.'_adverts', array('identifier' => $org->getSlug()));
        } else {
            return $this->render($this->type.'/application-new.html.twig',
                ['org' => $org, 'form' => $form->createView()]);
        }
    }

    /**
     * View a list of the organisation's last shows.
     * @Route("/{identifier}/history.{_format}", format="html")
     */
    public function getHistoryAction(Request $request, $identifier) {
        $showsPerPage = 36;

        $org = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $org);
        // Casting string→int in PHP always succeeds so no try/catch needed.
        $page = $request->query->has("p") ? max(1, (int) $request->query->get("p")) : 1;

        $qb = $this->getDoctrine()->getRepository(Entity\Show::class)
              ->queryByOrganisation($org, new \DateTime('1970-01-01'), new \DateTime('yesterday'))
              ->select('s, perf')->leftJoin('s.performances', 'perf')
              ->orderBy('p.start_at', 'DESC')->addOrderBy('s.id') // Make deterministic
              ->setFirstResult($showsPerPage * ($page - 1))
              ->setMaxResults($showsPerPage);
        $paginator = new Paginator($qb->getQuery());
        $route = explode('?', $request->getRequestUri())[0] . '?p=';

        return $this->show('organisation/past-shows.html.twig', 'data', [
            'org' => $org,
            'paginator' => $paginator,
            'page_num' => $page,
            'page_urlprefix' => $route
        ]);
    }
}
