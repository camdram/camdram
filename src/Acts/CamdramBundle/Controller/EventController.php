<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Event;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\EventType;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramBundle\Service\Time;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/events")
 * @extends AbstractRestController<Event>
 */
class EventController extends AbstractRestController
{
    protected $class = Event::class;

    protected $type = 'event';

    protected $type_plural = 'events';

    protected function getForm($event = null, $method = 'POST')
    {
        return $this->createForm(EventType::class, $event ?: new Event(), ['method' => $method]);
    }

    protected function getEntity($identifier): object
    {
        $entity = $this->getRepository()->find($identifier);
        if (!$entity) {
            throw $this->createNotFoundException('That '.$this->type.' does not exist');
        }
        return $entity;
    }


    /**
     * @Route(".{_format}", format="html", methods={"GET"}, name="get_events")
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return $this->entitySearch($request);
        }
        return $this->eventListAction($request,
            'SELECT COUNT(DISTINCT COALESCE(IDENTITY(e.link_id), e.id)) FROM ActsCamdramBundle:Event e
            WHERE e.start_at > CURRENT_TIMESTAMP()',
            'SELECT MIN(start_at) AS tstamp, COALESCE(linkid, id) AS ident FROM acts_events
            WHERE start_at > :now GROUP BY ident ORDER BY tstamp',
            'event/index.html.twig');
    }

    private function eventListAction(Request $request, string $countQuery, string $retrieveQuery,
        string $template)
    {
        $page = (int)$request->query->get('p', '1');
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1)*$limit;

        $count = (int)$this->em->createQuery($countQuery)->getSingleScalarResult();

        if ($offset > $count) {
            $offset = 0;
            $page = 1;
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('ident', 'id');
        $eventIds = $this->em->createNativeQuery(
            $retrieveQuery." LIMIT $limit OFFSET $offset;", $rsm)
            ->setParameter('now', Time::now())->getResult();
        # Flatten array
        $eventIds = array_map('reset', $eventIds);

        $eventsById = $this->em->createQuery(
            'SELECT e FROM ActsCamdramBundle:Event e INDEX BY e.id WHERE e.id IN (:ids)'
        )->setParameter('ids', $eventIds)->getResult();
        $events = [];
        foreach ($eventIds as $id) {
            $events[] = $eventsById[$id];
        }
        if ($request->getRequestFormat() != 'html') {
            return $this->view($events);
        }
        return $this->render($template, [
            'page_num' => $page,
            'page_urlprefix' => $request->getBaseUrl().$request->getPathInfo().
                '?p=',
            'resultset' => [
                'totalhits' => $count,
                'limit' => $limit,
                'data' => $events
            ]
        ]);
    }

    /**
     * @Route("/historic", methods={"GET"})
     */
    public function historicAction(Request $request)
    {
        return $this->eventListAction($request,
            'SELECT COUNT(e.id) FROM ActsCamdramBundle:Event e
            WHERE e.start_at < CURRENT_TIMESTAMP() AND e.link_id IS NULL AND NOT EXISTS
                (SELECT sub FROM ActsCamdramBundle:Event sub WHERE sub.link_id = e.id AND e.start_at > CURRENT_TIMESTAMP())',
            'SELECT MAX(start_at) AS tstamp, COALESCE(linkid, id) AS ident FROM acts_events
            GROUP BY ident HAVING tstamp < :now ORDER BY tstamp DESC',
            'event/historic.html.twig');
    }

    /**
     * @Route("/by-society/{slug}", methods={"GET"})
     */
    public function bySocietyAction(Request $request, Society $society)
    {
        $page = (int)$request->query->get('p', '1');
        if ($page < 1) $page = 1;

        $query = $this->em->createQuery(
            'SELECT e FROM ActsCamdramBundle:Event e
             WHERE :society MEMBER OF e.societies ORDER BY e.start_at DESC');
        $query->setMaxResults(10);
        $query->setFirstResult(10 * ($page - 1));
        $query->setParameter('society', $society);

        return $this->render('event/by-society.html.twig', [
            'paginator' => new Paginator($query),
            'page_num' => $page,
            'page_urlprefix' => $request->getBaseUrl().$request->getPathInfo().
                '?p=',
            'society' => $society
            ]);
    }

    /**
     * @Route("/{identifier<\d+>}.{_format}", format="html", methods={"GET"}, name="get_event")
     */
    public function getAction(Request $request, int $identifier)
    {
        $event = $this->getEntity($identifier);
        if ($root_evt = $event->getLinkId()) {
            return $this->redirectToRoute('get_event',
                ['identifier' => $root_evt->getId(), '_format' => $request->getRequestFormat()]);
        }

        $can_contact = !empty($this->getDoctrine()->getRepository(User::class)
            ->getContactableEntityOwners($event));

        return $this->doGetAction($event, ['can_contact' => $can_contact]);
    }

    /**
     * Called by AbstractRestController before form goes to user.
     */
    public function modifyEditForm($form, $identifier): void {
        // List of societies is public knowledge, no ACL checks here.
        $show = $this->getEntity($identifier);
        $socs = $show->getPrettySocData();
        foreach ($socs as &$soc) {
            $soc = $soc instanceof Society ? $soc->getName() : $soc["name"];
        }
        $form->get('societies')->setData($socs);
    }

    /**
     * Called by AbstractRestController after form sent by user.
     */
    public function afterEditFormSubmitted($form, $identifier): void {
        $event = $this->getEntity($identifier);

        // Societies
        $socRepo = $this->em->getRepository(Society::class);
        $newSocs = [];   // Array of [string, Society]
        $newSocIds = [];
        $liveSocs = $event->getSocieties();
        $oldSocs = $liveSocs->toArray();
        $displayList = [];
        foreach ($form->get('societies')->getData() as $newSocName) {
            $newSoc = $socRepo->findOneByName($newSocName);
            $newSocs[] = [$newSocName, $newSoc];
            if ($newSoc) $newSocIds[] = $newSoc->getId();
        }
        // Erase societies from event.societies
        foreach ($oldSocs as $oldSoc) {
            if (!in_array($oldSoc->getId(), $newSocIds, true)) {
                $liveSocs->removeElement($oldSoc);
            }
        }
        foreach ($newSocs as $newSocData) {
            // Add societies to event.societies
            $newSociety = $newSocData[1];
            if ($newSociety && !$liveSocs->exists(function($key, $value) use ($newSociety) {
                return $value->getId() == $newSociety->getId();
            })) {
                $liveSocs->add($newSociety);
            }

            // Generate JSON representation
            $jsonRep = $newSociety ? $newSociety->getId() : $newSocData[0];
            if (!in_array($jsonRep, $displayList, true)) {
                $displayList[] = $jsonRep;
            }
        }
        $event->setSocietiesDisplayList($displayList);

        // Sub-events
        foreach ($event->getDeletedDates() as $toDelete) {
            $this->em->remove($toDelete);
        }
    }

    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Event $event)
    {
        $admins = $this->get('camdram.security.acl.provider')->getOwners($event);
        $admins = array_merge($admins, $event->getSocieties()->toArray());
        $pending_admins = $this->em->getRepository(PendingAccess::class)->findByResource($event);

        return $this->render('event/admin-panel.html.twig', [
            'event' => $event,
            'admins' => $admins,
            'pending_admins' => $pending_admins
        ]);
    }

    /**
     * @Route("/{identifier}/image", methods={"DELETE"}, name="delete_event_image")
     */
    public function deleteImageAction(Request $request, $identifier)
    {
        $event = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $event);

        if (!$this->isCsrfTokenValid('delete_event_image', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $this->em->remove($event->getImage());
        $event->setImage(null);
        $this->em->flush();

        return $this->redirectToRoute('get_event', ['identifier' => $identifier]);
    }
}
