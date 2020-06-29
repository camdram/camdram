<?php

namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EntityCollectionTransformer
 *
 * Transforms an collection of linked entities into their names for when they are displayed in a form.
 * @phpstan-template T of \Acts\CamdramBundle\Entity\BaseEntity
 */
class EntityCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @phpstan-var class-string<T>
     */
    private $repository_name;


    /**
     * @phpstan-param class-string<T> $repository_name
     */
    public function __construct(EntityManager $em, $repository_name)
    {
        $this->em = $em;
        $this->repository_name = $repository_name;
    }

    /**
     * @phpstan-param Collection<T> $value
     * @return array[]
     */
    public function transform($value)
    {
        if ($value instanceof Collection) {
            $ids = array();
            foreach ($value as $item) {
                $ids[] = array('id' => $item->getId(), 'name' => $item->getName());
            }

            return $ids;
        } else {
            return $value;
        }
    }

    /**
     * @param int[] $value
     * @phpstan-return Collection<T>
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            $entities = new ArrayCollection();
            $repo = $this->em->getRepository($this->repository_name);

            foreach ($value as $item) {
                if (is_numeric($item)) {
                    $entity = $repo->find($item);
                    if ($entity) {
                        $entities[] = $entity;
                    }
                }
            }

            return $entities;
        }

        return $value;
    }
}
