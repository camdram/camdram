<?php
namespace Acts\CamdramBundle\Search;

use Pagerfanta\PagerfantaInterface;

/**
 * Class ProviderInterface
 *
 * The search/autocomplete features have been engineered so that they can be serviced by a choice of backends - the
 * default is Doctrine as it doesn't require any other software, but more sophisticated backends can be swapped in
 * (e.g. Sphinx). This is the common interface that the 'search providers' much follow. The choice of search provider
 * to use is defined in parameters.yml
 *
 * @package Acts\CamdramBundle\Service\Search
 */
interface ProviderInterface
{

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return array
     */
    public function executeAutocomplete($repository, $query, $limit, array $orderBy = array());

    /**
     * @param $repository
     * @param $query
     * @param $limit
     * @param $offset
     * @return \Pagerfanta\PagerfantaInterface;
     */
    public function executeTextSearch($repository, $query, array $filters = array(), array $orderBy = array());

}