<?php
namespace Acts\CamdramBundle\Search;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Entity\Show;
use Doctrine\Common\Collections\ArrayCollection;
use Pagerfanta\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class DoctrineProvider
 *
 * An implementation of the Search\ProviderInterface that calls the Doctrine backend. It isn't very efficient (and
 * it doesn't rank the results very sensibly), but it works straight out of the box without having to install Sphinx.
 *
 * @package Acts\CamdramBundle\Service\Search
 */
class DoctrineProvider implements ProviderInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function executeAutocomplete($indexes, $search_query, $limit, array $filters = array(), array $orderBy = array())
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $results = array();
        foreach ($indexes as $index) {
            $repo = $em->getRepository('ActsCamdramBundle:'.ucfirst($index));

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
        $em = $this->container->get('doctrine.orm.entity_manager');
        $entities = array();
        foreach ($indexes as $index) {
            if ($index == 'user') {
                $namespace = 'ActsCamdramSecurityBundle:';
            } else {
                $namespace = 'ActsCamdramBundle:';
            }

            /** @var $repo \Doctrine\ORM\EntityRepository */
            $repo = $em->getRepository($namespace.ucfirst($index));

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

    private function createResultFromEntity(SearchableInterface $entity) {
        $result = array(
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'slug' => $entity->getSlug(),
            'short_name' => $entity->getShortName(),
            'entity_type' => $entity->getEntityType(),
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
