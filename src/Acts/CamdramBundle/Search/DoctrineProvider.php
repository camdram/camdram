<?php

namespace Acts\CamdramBundle\Search;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Show;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class DoctrineProvider
 *
 * An implementation of the Search\ProviderInterface that calls the Doctrine backend. It isn't very efficient (and
 * it doesn't rank the results very sensibly), but it works straight out of the box without having to install Sphinx.
 */
class DoctrineProvider implements ProviderInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function executeAutocomplete($indexes, $search_query, $limit, array $filters = array(), array $orderBy = array())
    {
        /* @var $repo \Doctrine\ORM\EntityRepository */
        $results = array();
        foreach ($indexes as $index) {
            $repo = $this->entityManager->getRepository('ActsCamdramBundle:'.ucfirst($index));

            $query = $repo->createQueryBuilder('e')
                ->where('e.name LIKE :input')
                ->setParameter('input', '%'.$search_query.'%')
                ->setMaxResults($limit)
                ->getQuery();
            foreach ($query->getResult() as $result) {
                $results[] = $this->createResultFromEntity($result);
            }
        }

        return array_slice($results, 0, $limit);
    }

    public function executeTextSearch($indexes, $q, $offset, $limit, array $orderBy = array())
    {
        $entities = array();
        foreach ($indexes as $index) {
            if ($index == 'user') {
                $namespace = 'ActsCamdramSecurityBundle:';
            } else {
                $namespace = 'ActsCamdramBundle:';
            }

            /** @var $repo \Doctrine\ORM\EntityRepository */
            $repo = $this->entityManager->getRepository($namespace.ucfirst($index));

            $qb = $repo->createQueryBuilder('e')
                ->where('e.name LIKE :input')
                ->setParameter(':input', '%'.$q.'%');
            if ($index != 'user') {
                $qb->orWhere('e.description LIKE :input');
            }
            foreach ($qb->getQuery()->getResult() as $result) {
                $entities[] = $this->createResultFromEntity($result);
            }
        }

        $adapter = new ArrayAdapter($entities);

        return new Pagerfanta($adapter);
    }

    public function executeUserSearch($q, $limit)
    {
        $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');

        $query = $repo->createQueryBuilder('u')
            ->where('u.name LIKE :input')
            ->setParameter('input', '%'.$q.'%')
            ->setMaxResults($limit)
            ->getQuery();

        $results = array();
        foreach ($query->getResult() as $result) {
            $results[] = array(
                'id' => $result->getId(),
                'name' => $result->getName(),
                'email' => $result->getEmail()
            );
        }

        return $results;
    }

    private function createResultFromEntity(SearchableInterface $entity)
    {
        $result = array(
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'slug' => $entity->getSlug(),
            'short_name' => $entity->getShortName(),
            'entity_type' => strtolower((new \ReflectionClass($entity))->getShortName()),
        );
        if ($entity instanceof Show || $entity instanceof Person) {
            $index_date = $entity->getIndexDate();
            $result['index_date'] = $index_date ? $index_date->format('U') : 0;
        }
        if ($entity instanceof Person) {
            $result['show_count'] = $entity->getNumShows();
        }

        return $result;
    }
}
