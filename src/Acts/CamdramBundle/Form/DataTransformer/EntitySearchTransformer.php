<?php
namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class EntitySearchTransformer implements DataTransformerInterface
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
        if (is_object($value)) {
            return array(
                'id' => $value->getId(),
                'name' => $value->getName(),
            );
        }
        return $value;
    }

    public function reverseTransform($value) {
        if (is_array($value) && isset($value['id'])) {
            $repo = $this->em->getRepository($this->repository_name);

            $entity = $repo->findOneById($value['id']);
            if ($entity) {
                $entities[] = $entity;
            }
            return $entity;
        }
        return $value;
    }

}