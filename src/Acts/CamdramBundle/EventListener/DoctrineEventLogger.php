<?php

namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Search\SearchableInterface;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DoctrineEventLogger implements EventSubscriber
{
    private $logger;

    public function getSubscribedEvents()
    {
        return array(
          'postUpdate', 'postPersist', 'postRemove'
        );
    }

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function getContext($object)
    {
        if ($object instanceof SearchableInterface) {
            return array(
                'id' => $object->getId(),
                'name' => $object->getName()
            );
        } elseif ($object instanceof AccessControlEntry) {
            return array(
                'type' => $object->getType(),
                'id' => $object->getEntityId(),
                'user' => $object->getUser()->getId().'/'.$object->getUser()->getEmail()
            );
        } else {
            $accessor = PropertyAccess::createPropertyAccessor();
            $data = array();
            try {
                $id = $accessor->getValue($object, 'id');
                $data['id'] = $id;
            } catch (NoSuchPropertyException $e) {
            }
            try {
                $name = $accessor->getValue($object, 'name');
                $data['name'] = $name;
            } catch (NoSuchPropertyException $e) {
            }

            return $data;
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $reflection = new \ReflectionClass($object);
        $message = sprintf('%s updated', $reflection->getShortName());
        $this->logger->info($message, $this->getContext($object));
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $reflection = new \ReflectionClass($object);
        $message = sprintf('%s created', $reflection->getShortName());
        $this->logger->info($message, $this->getContext($object));
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $reflection = new \ReflectionClass($object);
        $message = sprintf('%s deleted', $reflection->getShortName());
        $this->logger->notice($message, $this->getContext($object));
    }
}
