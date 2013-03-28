<?php
namespace Acts\CamdramBundle\Service\Search;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class DoctrineProvider implements ProviderInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function executeAutocomplete($repository, $query, $limit, array $filters = array())
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


    public function executeTextSearch($repository, $query)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var $repo \Doctrine\ORM\EntityRepository */
        $repo = $em->getRepository('ActsCamdramBundle:'.ucfirst($repository));

        $qb = $repo->createQueryBuilder('e')
            ->where('e.name LIKE :input')
            ->orWhere('e.description LIKE :input')
            ->setParameter(':input', '%'.$query.'%');

        $adapter = new DoctrineORMAdapter($qb);
        return new Pagerfanta($adapter);
    }
}