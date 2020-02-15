<?php

namespace Acts\CamdramBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function makeArrayRSM(array $fields): ResultSetMappingBuilder {
        $rsm = new ResultSetMappingBuilder($this->em);
        foreach ($fields as $i) {
            $rsm->addScalarResult($i, $i);
        }
        return $rsm;
    }

    public function countResults(string $search, array $entities): int
    {
        $query = $this->em->createNativeQuery(<<<'ENDSQL'
            SELECT
            (SELECT COUNT(*) FROM acts_shows s
                WHERE MATCH (`title`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_people_data p
                WHERE MATCH (`name`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_societies soc
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_venues ven
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE))
            AS n_results
ENDSQL
        , $this->makeArrayRSM(['n_results']));

        $query->setParameter(1, $search);
        $query->setParameter(2, $search);
        $query->setParameter(3, $search);
        $query->setParameter(4, $search);
        return $query->getResult()[0]['n_results'];
    }

    public function doSearch(string $search, array $entities, int $start_at, int $limit): array
    {
        $query = $this->em->createNativeQuery(<<<'ENDSQL'
            (SELECT 'show' AS entity_type, s.id, s.slug, s.title as name,
                    MIN(acts_performances.start_at) AS start_at, NULL AS last_active, NULL as show_count
                FROM acts_shows s
                LEFT JOIN acts_performances ON s.id = acts_performances.sid
                WHERE MATCH (`title`) AGAINST (? IN BOOLEAN MODE)
                GROUP BY s.id) UNION
            (SELECT 'person', p.id, p.slug, p.name,
                    MIN(acts_performances.start_at), MAX(acts_performances.repeat_until),
                    COUNT(DISTINCT acts_shows_people_link.sid)
                FROM acts_people_data p
                LEFT JOIN acts_shows_people_link ON acts_shows_people_link.pid = p.id
                LEFT JOIN acts_performances ON acts_performances.sid = acts_shows_people_link.sid
                WHERE MATCH (`name`) AGAINST (? IN BOOLEAN MODE)
                GROUP BY p.id) UNION
            (SELECT 'society', soc.id, soc.slug, soc.name, NULL, NULL, NULL
                FROM acts_societies soc
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE)) UNION
            (SELECT 'venue', ven.id, ven.slug, ven.name, NULL, NULL, NULL
                FROM acts_venues ven
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE))
            ORDER BY (entity_type IN ('society', 'venue')) DESC,
                IF(entity_type = 'show', start_at, last_active) DESC,
                id, entity_type
            LIMIT ?,?
ENDSQL
        , $this->makeArrayRSM(['name', 'slug', 'start_at', 'id', 'entity_type', 'last_active', 'show_count']));
        $query->setParameter(1, $search);
        $query->setParameter(2, $search);
        $query->setParameter(3, $search);
        $query->setParameter(4, $search);
        $query->setParameter(5, $start_at);
        $query->setParameter(6, $limit);

        $rawResult = $query->getResult();
        foreach ($rawResult as &$result) {
            if ($result['entity_type'] === 'person') {
                $result['rank'] = (int)(str_replace('-', '',
                    substr($result['last_active'] ?? "0", 0, 10)));
                $result['first_active'] = $result['start_at'];

                unset($result['start_at']);
            } else if ($result['entity_type'] === 'show') {
                $result['rank'] = (int)(str_replace('-', '',
                    substr($result['start_at'] ?? "0", 0, 10)));

                unset($result['last_active']);
                unset($result['show_count']);
            } else {
                unset($result['last_active']);
                unset($result['show_count']);
                unset($result['start_at']);
                unset($result['rank']);
            }
        }

        return $rawResult;
    }

    /**
     * @Route("/search.{_format}", methods={"GET"}, name="search_entity")
     */
    public function search(Request $request, $_format = 'html')
    {
        $limit = (int) $request->get('limit', 10);
        $page = (int) $request->get('page', 1);
        $searchText = $request->get('q', '');

        $data = $this->doSearch($searchText, [], ($page-1)*$limit, $limit);
        if ($_format == 'json') {
            return new JsonResponse($data);
        } else if ($_format == 'xml') {
            return new Response(<<<'END'
<err>XML search results have been removed as they were offered in an odd,
hard-to-parse format, which had previously been accidentally changed without
us receiving complaints. Consider a switch to JSON or contact us if needed.</err>
END
            , 410);
        }

        return $this->render('search/index.html.twig', [
            'page_num' => $page,
            'page_urlprefix' => "search?limit={$limit}&q=".urlencode($searchText).'&page=',
            'query' => $searchText,
            'resultset' => [
                'totalhits' => $this->countResults($searchText, []),
                'limit' => $limit,
                'data' => $data
            ]
        ]);
    }
}
