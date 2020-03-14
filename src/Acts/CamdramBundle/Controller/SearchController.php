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
    private static $entityTypes = ['show', 'person', 'society', 'venue'];

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
                WHERE ? AND MATCH (`title`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_people_data p
                WHERE ? AND MATCH (`name`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_societies soc
                WHERE ? AND MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE)) +
            (SELECT COUNT(*) FROM acts_venues ven
                WHERE ? AND MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE))
            AS n_results
ENDSQL
        , $this->makeArrayRSM(['n_results']));

        $i = 0;
        foreach (self::$entityTypes as $type) {
            $query->setParameter($i++, in_array($type, $entities));
            $query->setParameter($i++, $search);
        }
        return $query->getResult()[0]['n_results'];
    }

    /**
     * This function carries out a native SQL query of a natural language
     * search on up to all four entity types and paginates it. Because of this
     * pagination it must use a UNION query to count the different types of
     * result in turn, which precludes it from being a DQL query.
     */
    public function doSearch(string $search, array $entities, int $start_at, int $limit): array
    {
        if ($start_at < 0) throw new \InvalidArgumentException('start_at < 0');
        if ($limit <= 0)   throw new \InvalidArgumentException('limit <= 0');

        // Name columns irrespective of what entities are chosen
        $prefixSQL = <<<'ENDSQL'
            (SELECT NULL AS entity_type, NULL AS id, NULL AS slug, NULL AS name,
                NULL AS start_at, NULL AS last_active, NULL AS show_count FROM DUAL WHERE FALSE) UNION
ENDSQL;

        $fragments = [];
        $fragments['show'] = <<<'ENDSQL'
            (SELECT 'show', s.id, s.slug, s.title, MIN(acts_performances.start_at), NULL, NULL
                FROM acts_shows s
                LEFT JOIN acts_performances ON s.id = acts_performances.sid
                WHERE MATCH (`title`) AGAINST (? IN BOOLEAN MODE)
                GROUP BY s.id)
ENDSQL;
        $fragments['person'] = <<<'ENDSQL'
            (SELECT 'person', p.id, p.slug, p.name,
                    MIN(acts_performances.start_at), MAX(acts_performances.repeat_until),
                    COUNT(DISTINCT acts_shows_people_link.sid)
                FROM acts_people_data p
                LEFT JOIN acts_shows_people_link ON acts_shows_people_link.pid = p.id
                LEFT JOIN acts_performances ON acts_performances.sid = acts_shows_people_link.sid
                WHERE MATCH (`name`) AGAINST (? IN BOOLEAN MODE)
                GROUP BY p.id)
ENDSQL;
        $fragments['society'] = <<<'ENDSQL'
            (SELECT 'society', soc.id, soc.slug, soc.name, NULL, NULL, NULL
                FROM acts_societies soc
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE))
ENDSQL;
        $fragments['venue'] = <<<'ENDSQL'
            (SELECT 'venue', ven.id, ven.slug, ven.name, NULL, NULL, NULL
                FROM acts_venues ven
                WHERE MATCH (`name`, `shortname`) AGAINST (? IN BOOLEAN MODE))
ENDSQL;
        $suffixSQL = sprintf(<<<ENDSQL
            ORDER BY (entity_type IN ('society', 'venue')) DESC,
                IF(entity_type = 'show', start_at, last_active) DESC,
                id, entity_type
            LIMIT %d,%d
ENDSQL
        , $start_at, $limit);

        $parts = [];
        foreach ($entities as $idx) $parts[] = $fragments[$idx];
        if (empty($parts)) throw new \InvalidArgumentException("No entity types specified");

        $query = $this->em->createNativeQuery($prefixSQL . implode(' UNION ', $parts) . $suffixSQL,
            $this->makeArrayRSM(['name', 'slug', 'start_at', 'id', 'entity_type', 'last_active', 'show_count']));
        for ($i = 0; $i < count($parts); $i++) {
            $query->setParameter($i, $search);
        }

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

    private static function clampParam($param, int $min, int $default, int $max = PHP_INT_MAX): int
    {
        if (is_array($param) || is_null($param) || !ctype_digit($param)) return $default;
        else return max($min, min($max, (int)$param));
    }

    /**
     * @Route("/search.{_format}", methods={"GET"}, name="search_entity")
     */
    public function search(Request $request, $_format = 'html')
    {
        // Since this can be forwarded to from AbstractRestController, have to
        // re-parse the query string ourselves.
        parse_str($request->server->get('QUERY_STRING'), $queryParams);
        $limit = static::clampParam($queryParams['limit'] ?? 10, 1, 10, 100);
        $page  = static::clampParam($queryParams['page'] ?? 1, 1, 1);
        $searchText = ($queryParams['q'] ?? '');
        if (is_array($searchText)) $searchText = '';
        $types = $request->get('types', self::$entityTypes);
        if (!is_array($types)) $types = self::$entityTypes;

        // Intended behaviour for default search is all terms required, final
        // term may be an incomplete word, syntax errors not possible.
        // So 'john smi' -> '+john +smi*', '@@~~blah' -> '+blah*'
        $enhancedSearchText = trim(preg_replace('/[-+@<>()~*" ]+/', ' ',
            $searchText)) . '*';
        $enhancedSearchText = '+'.implode(' +',explode(' ', $enhancedSearchText));
        error_log($enhancedSearchText);

        if ($enhancedSearchText == '+*') {
            $data = [];
            $hits = 0;
        } else {
            $data = $this->doSearch($enhancedSearchText, $types, ($page-1)*$limit, $limit);
            $hits = $this->countResults($enhancedSearchText, $types);
        }
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

        // Bring a blank page= to the end of the URL
        unset($queryParams['page']);
        $queryParams['page'] = '';
        return $this->render('search/index.html.twig', [
            'page_num' => $page,
            'page_urlprefix' => $request->getBaseUrl().$request->getPathInfo().
                '?'.http_build_query($queryParams),
            'query' => $searchText,
            'resultset' => [
                'totalhits' => $hits,
                'limit' => $limit,
                'data' => $data
            ]
        ]);
    }
}
