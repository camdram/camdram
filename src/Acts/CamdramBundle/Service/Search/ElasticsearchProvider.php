<?php
namespace Acts\CamdramBundle\Service\Search;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\PagerfantaInterface;

class  ElasticsearchProvider implements ProviderInterface
{
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
    public function executeAutocomplete($repository, $q, $limit)
    {
        if (empty($q)) return array();

        $index = $this->container->get('foq_elastica.index.camdram.'.$repository);

        $query = new \Elastica_Query();
        $query->setQuery(new \Elastica_Query_Field('name', $q));
        $query->setHighlight(array('fields' => array('name' => new \stdClass())))->setLimit($limit);

        $results = $index->search($query);

        $data = array();
        foreach ($results as $result) {
            $item = $result->getSource();
            $item['id'] = $result->getId();
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return \Pagerfanta\PagerfantaInterface;
     */
    public function executeTextSearch($repository, $q)
    {
        $finder = $this->container->get('foq_elastica.finder.camdram.'.$repository);
        $query = new \Elastica_Query_QueryString($q);
        return $finder->findPaginated($query);
    }
}