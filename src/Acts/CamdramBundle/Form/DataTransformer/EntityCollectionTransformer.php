<?php
namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class EntityCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $repository_name;

    public function __construct(EntityManager $em, $repository_name)
    {
        $this->em = $em;
        $this->repository_name = $repository_name;
    }

    public function transform($value) {
        if ($value instanceof Collection) {
            $ids = array();
            foreach ($value as $item) {
                $ids[] = array('id' => $item->getId(), 'name' => $item->getName());
            }
            return $ids;
        }
        else return $value;
    }

    public function reverseTransform($value) {
        if (is_array($value)) {
            $entities = new ArrayCollection;
            $repo = $this->em->getRepository($this->repository_name);

            foreach ($value as $item) {
                if (is_numeric($item)) {
                    $entity = $repo->findOneById($item);
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