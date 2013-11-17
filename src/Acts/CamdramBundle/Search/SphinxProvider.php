<?php
namespace Acts\CamdramBundle\Search;

use Foolz\SphinxQL\Expression;
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
    public function executeAutocomplete($indexes, $q, $limit, array $orderBy = array())
    {
        if (empty($q)) return array();

        $client = $this->container->get('acts.sphinx_realtime.client.default');

        $query = SphinxQL::forge()->select('id', 'name', new Expression("EXIST('start_at', 0) as date"), 'slug', 'type')
            ->from($indexes)->match('@relaxed','')->match('(name,short_name)', $q.'*')->limit($limit);

        foreach ($orderBy as $field => $direction) {

            $query->orderBy($field, $direction);
        }

        $results = $client->query($query);

        return $results;
    }

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return \Pagerfanta\PagerfantaInterface;
     */
    public function executeTextSearch($indexes, $q, array $filters = array(), array $orderBy = array())
    {
        $client = $this->container->get('acts.sphinx_realtime.client.default');

        $query = SphinxQL::forge()->select()->from($indexes)->match('@relaxed','')
            ->match('(name,short_name,description)', $q);

        foreach ($filters as $key => $value) {
            $query->match($key, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        //return $finder->findPaginated($query);
    }

}