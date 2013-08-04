<?php
namespace Acts\CamdramBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EntitySearchTransformer
 *
 * Transforms a linked entity into its name for when it's displayed in a form.
 *
 * @package Acts\CamdramBundle\Form\DataTransformer
 */
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

    private $property;

    public function __construct(EntityManager $em, $repository_name, $property)
    {
        $this->em = $em;
        $this->repository_name = $repository_name;
        $this->property = $property;
    }

    public function transform($value) {
        if (is_object($value)) {
            return array(
                'id' => $value->getId(),
                'name' => $value->getName(),
            );
        }
        return array('id' => null, 'name' => null);
    }

    public function reverseTransform($value) {
        if (is_numeric($value)) {
            $repo = $this->em->getRepository($this->repository_name);

            $entity = $repo->findOneById($value);
            return $entity;
        }
        return $value;
    }

}