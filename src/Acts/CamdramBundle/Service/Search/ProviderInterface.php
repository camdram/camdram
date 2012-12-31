<?php
namespace Acts\CamdramBundle\Service\Search;

use Pagerfanta\PagerfantaInterface;

interface ProviderInterface
{

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return array
     */
    public function executeAutocomplete($repository, $query, $limit);

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return \Pagerfanta\PagerfantaInterface;
     */
    public function executeTextSearch($repository, $query);
}