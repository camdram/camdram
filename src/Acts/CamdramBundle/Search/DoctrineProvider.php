<?php
namespace Acts\CamdramBundle\Search;

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


    public function executeAutocomplete($indexes, $query, $limit, array $filters = array(), array $orderBy = array())
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('ActsCamdramBundle:'.ucfirst($repository));

        $query = $repo->createQueryBuilder('e')
            ->select('partial e.{id, name, description}')
            ->where('e.name LIKE :input')
            ->setParameter('input', '%'.$query.'%')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }


    public function executeTextSearch($repository, $query, $offset, $limit, array $orderBy = array())
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('ActsCamdramBundle:'.ucfirst($repository));

        $qb = $repo->createQueryBuilder('e')
            ->where('e.name LIKE :input')
            ->orWhere('e.description LIKE :input')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter(':input', '%'.$query.'%');

        $adapter = new DoctrineORMAdapter($qb);
        return new Pagerfanta($adapter);
    }
}