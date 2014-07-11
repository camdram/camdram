<?php
namespace Acts\CamdramBundle\Search;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class DoctrineProvider
 *
 * An implementation of the Search\ProviderInterface that calls the Doctrine backend. It isn't very efficient but
 * it works straight out of the box without having to install Sphinx.
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
                ->select('partial e.{id, name, description, slug}')
                ->where('e.name LIKE :input')
                ->setParameter('input', '%'.$search_query.'%')
                ->setMaxResults($limit)
                ->getQuery();
            foreach ($query->getResult() as $result) {
                $results[] = $result;
            }
        }

        return array_slice($results, 0, $limit);
    }


    public function executeTextSearch($repository, $query, $offset, $limit, array $orderBy = array())
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        if ($repository == 'user') {
            $namespace = 'ActsCamdramSecurityBundle:';
        } else {
            $namespace = 'ActsCamdramBundle:';
        }
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository($namespace.ucfirst($repository));

        $qb = $repo->createQueryBuilder('e')
            ->where('e.name LIKE :input')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter(':input', '%'.$query.'%');
        if ($repository != 'user') {
            $qb->orWhere('e.description LIKE :input');
        }

        $adapter = new DoctrineORMAdapter($qb);
        return new Pagerfanta($adapter);
    }
}
