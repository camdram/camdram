<?php
namespace Acts\CamdramBundle\Service\Search;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\PagerfantaInterface;
use Foolz\SphinxQL\SphinxQL;

/**
 * Class SphinxProvider
 *
 * An implementation of the Search\ProviderInterface that calls the Sphinx backend
 *
 * @package Acts\CamdramBundle\Service\Search
 */
class  SphinxProvider implements ProviderInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return array
     */
    public function executeAutocomplete($repository, $q, $limit, array $filters = array(), array $orderBy = array())
    {
        if (empty($q)) return array();

        $finder = $this->container->get('acts.sphinx_realtime.finder.'.$repository);

        $query = SphinxQL::forge()->select()->match('name', $q.'*');

        foreach ($filters as $key => $value) {
            $query->match($key, $value);
        }

        foreach ($orderBy as $field => $direction) {

            $query->orderBy($field, $direction);
        }

        $results = $finder->find($query, $limit);

        return $results;
    }

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return \Pagerfanta\PagerfantaInterface;
     */
    public function executeTextSearch($repository, $q, array $filters = array(), array $orderBy = array())
    {
        $finder = $this->container->get('acts.sphinx_realtime.finder.'.$repository);
        $query = SphinxQL::forge()->select()->match('(name,description)', $q);

        foreach ($filters as $key => $value) {
            $query->match($key, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $finder->findPaginated($query);
    }
}