<?php
namespace Acts\CamdramBundle\Search;

use Acts\SphinxRealTimeBundle\Manager\IndexManager;
use Acts\SphinxRealTimeBundle\Paginator\FantaPaginatorAdapter;
use Acts\SphinxRealTimeBundle\Paginator\RawPartialResults;
use Acts\SphinxRealTimeBundle\Paginator\SphinxQLAdapter;
use Acts\SphinxRealTimeBundle\Service\Client;
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
    private $client;

    private $manager;

    public function __construct(Client $client, IndexManager $manager)
    {
        $this->client = $client;
        $this->manager = $manager;
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

        $query = SphinxQL::create($this->client->getConnection())->select('id', 'name', 'slug',
                    new Expression("EXIST('num_shows', 0) as show_count"), 'index_date', 'entity_type')
            ->from($indexes)->match(array('name','short_name'), $q.'*', true)->limit($limit);

        foreach ($orderBy as $field => $direction) {

            $query->orderBy($field, $direction);
        }

        $results = $this->client->query($query);

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

        $query = SphinxQL::create($this->client->getConnection())->select()->from($indexes)
            ->limit($limit)->offset($offset)
            ->match(array('name','short_name','description'), $q);


        foreach ($orderBy as $field => $direction) {
            $query->orderBy($field, $direction);
        }
        $paginator = new Pagerfanta(new FantaPaginatorAdapter(new SphinxQLAdapter($this->client, $query)));
        return $paginator;
    }

    public function executeUserSearch($q, $limit)
    {
        if (empty($q)) return array();

        $query = SphinxQL::forge()->select('*')
            ->from('user')->match(array('name'), $q.'*')->limit($limit);

        $results = $this->client->query($query);
        foreach ($results as &$result) {
            $result['id'] = floor($result['id'] / count($this->manager->getIndexes()));
        }

        return $results;
    }

}
