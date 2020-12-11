<?php
namespace Acts\CamdramBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\PositionTag;

class AdvertListener
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function prePersist(Advert $advert, LifecycleEventArgs $event)
    {
        $this->updatePositions($advert);
    }

    public function preUpdate(Advert $advert, LifecycleEventArgs $event)
    {
        $this->updatePositions($advert);
    }

    public function updatePositions(Advert $advert)
    {
        foreach ($advert->getPositions() as $position) {
            $advert->removePosition($position);
        }

        if ($advert->getType() != Advert::TYPE_ACTORS) {

            $tagRepository = $this->entityManager->getRepository(PositionTag::class);
            /*
            Ensure we match "Technical Director" before "Director" by searching for tags
            in reverse length order
            */
            $tags = $tagRepository->findAllOrderedByLengthDesc();
            $searchString = $advert->getSummary();

            foreach ($tags as $tag) {
                if (\strpos($searchString, $tag->getName()) !== false) {
                    $advert->addPosition($tag->getPosition());
                    $searchString = str_replace($tag->getName(), "", $searchString);
                }
            }
        }
    }
}