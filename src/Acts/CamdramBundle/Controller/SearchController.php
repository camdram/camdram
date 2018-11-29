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
        $query->setSort([
            'rank' => ['order' => 'desc', 'unmapped_type' => 'long', 'missing' => '_first']
        ]);

        $indexManager = $this->get('fos_elastica.index_manager');
        $search = $indexManager->getIndex('autocomplete_show')->createSearch();
        $search->addIndex($indexManager->getIndex('autocomplete_person')->getName());
        $search->addIndex($indexManager->getIndex('autocomplete_society')->getName());
        $search->addIndex($indexManager->getIndex('autocomplete_venue')->getName());
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
