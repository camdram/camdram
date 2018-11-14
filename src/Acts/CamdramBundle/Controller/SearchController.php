<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Elastica\Query;
use Elastica\Query\MultiMatch;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 *
 * @RouteResource("Entity")
 */
class SearchController extends FOSRestController
{
    /**
     * @Rest\Get("/search")
     */
    public function searchAction(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $searchText = $request->get('q', '');

        $match = new MultiMatch;
        $match->setQuery($searchText);
        $match->setFields(['name', 'short_name']);

        $query = new Query($match);
        $query->setFrom(($page-1)*$limit)->setSize($limit);
        //PHP_INT_MAX used because '_first' triggers an integer overflow in json_decode on 32 bit...
        $query->setSort([
            'rank' => ['order' => 'desc', 'missing' => PHP_INT_MAX-1]
        ]);

        $search = $this->get('fos_elastica.index.autocomplete')->createSearch();
        $resultSet = $search->search($query);

        $data = [];
        foreach ($resultSet as $result) {
            $row = $result->getSource();
            $row['id'] = $result->getId();
            $row['entity_type'] = $result->getType();
            $data[] = $row;
        }

        $view = $this->view($data, 200)
            ->setTemplateVar('results')
            ->setTemplate('search/index.html.twig')
        ;

        return $view;
    }
}
