<?php
namespace Acts\CamdramBundle\Search;

use Acts\SphinxRealTimeBundle\Paginator\FantaPaginatorAdapter;
use Acts\SphinxRealTimeBundle\Paginator\RawPartialResults;
use Acts\SphinxRealTimeBundle\Paginator\SphinxQLAdapter;
use Foolz\SphinxQL\Expression;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
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

        $query = SphinxQL::forge()->select('id', 'name', new Expression("EXIST('start_at', 0) as date"), 'slug', 'entity_type')
            ->from($indexes)->match(array('name','short_name'), $q.'*', true)->limit($limit);

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
    public function executeTextSearch($indexes, $q, $offset, $limit, array $orderBy = array())
    {
        if (trim($q) == '') return new Pagerfanta(new ArrayAdapter(array()));

        $client = $this->container->get('acts.sphinx_realtime.client.default');
        $query = SphinxQL::forge()->select()->from($indexes)
            ->limit($limit)->offset($offset)
            ->match(array('name','short_name','description'), $q);


        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }
        $paginator = new Pagerfanta(new FantaPaginatorAdapter(new SphinxQLAdapter($client, $query)));
        return $paginator;
    }

}
