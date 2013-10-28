<?php
namespace Acts\CamdramBundle\Rest;

use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Rest\ResponseQueryParams;
use Acts\CamdramBundle\Rest\ResponseUrls;

/**
 * Class PaginatedCollection
 *
 * An object returned by controllers that encapsulates a collection of results. This object contains a pointer to the
 * results collection (in the form of a Pagerfanta interface) and the desired number of results per page and
 * page number. The ViewPaginatorListener detects when one of these objects is returned, and uses the information
 * contained in this class to render a template or populate a JSON/XML object as appropriate.
 *
 * @package Acts\CamdramBundle\Rest
 */
class PaginatedCollection
{
    public $data;

    public $page;

    public $total_count;

    public $count;

    /**
     * @var array
     * @Serializer\XmlKeyValuePairs
     */
    public $urls;

    /**
     * @var array
     * @Serializer\XmlKeyValuePairs
     */
    public $query;

    public function __construct(Pagerfanta $paginator, Request $request, $base_url)
    {
        $this->data = $paginator->getCurrentPageResults();
        $this->total_count = $paginator->getNbResults();
        $this->count = count($this->data);

        $query = array(
            'q' => $request->get('q'),
            'limit' => $request->get('limit'),
            'page' => $request->get('page'),
        );
        $this->query = $query;

        $this->urls['current'] = $base_url.'?'.http_build_query($query);
        if ($paginator->hasPreviousPage()) {
            $query['page'] = $paginator->getPreviousPage();
            $this->urls['previous'] = $base_url.'?'.http_build_query($query);
            if ($paginator->getCurrentPage() > 2) {
                $query['page'] = 1;
                $this->urls['start'] = $base_url.'?'.http_build_query($query);
            }
        }
        if ($paginator->hasNextPage()) {
            $query['page'] = $paginator->getNextPage();
            $this->urls['next'] = $base_url.'?'.http_build_query($query);
            if ($paginator->getCurrentPage() < $paginator->getNbPages() - 1) {
                $query['page'] = $paginator->getNbPages();;
                $this->urls['end'] = $base_url.'?'.http_build_query($query);
            }
        }
    }
}