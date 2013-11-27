<?php

namespace Acts\CamdramBundle\Controller;


use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("Entity")
 */
class SearchController extends FOSRestController
{
    /**
     * @Rest\Get("/search/autocomplete")
     */
    public function autocompleteAction(Request $request)
    {
        $search_provider = $this->get('acts.camdram.search_provider');

        $limit = $request->get('limit', 10);

        $data = $search_provider->executeAutocomplete(array('show', 'society', 'venue', 'person'),
            $request->get('q'), $limit, array('rank' => 'DESC'));

        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:Search:index.html.twig')
        ;

        return $view;
    }

    /**
     * @Rest\Get("/search")
     */
    public function searchAction(Request $request)
    {
        $search_provider = $this->get('acts.camdram.search_provider');

        $limit = $request->get('limit', 10);

        $data = $search_provider->executeTextSearch(array('show', 'society', 'venue', 'person'),
            $request->get('q'), $limit, array('rank' => 'DESC'));

        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:Search:index.html.twig')
        ;

        return $view;
    }

}
