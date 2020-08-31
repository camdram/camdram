<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Event;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\EventType;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
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

        // TODO use more reasonable query
        $events = $this->getRepository()->createQueryBuilder('s')
                      ->where('s.link_id IS NULL')
                      ->orderBy('s.id', 'DESC')->setMaxResults(10)
                      ->getQuery()->getResult();
        return $this->show('event/index.html.twig', '', ['events' => $events]);
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
    public function modifyEditForm($form, $identifier) {
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
    public function afterEditFormSubmitted($form, $identifier) {
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
     * @Route("/{identifier}/admin/edit", methods={"GET"}, name="edit_event_admin")
     */
    public function editAdminAction(Request $request, $identifier)
    {
        throw $this->createNotFoundException('Not implemented');
    }
}
