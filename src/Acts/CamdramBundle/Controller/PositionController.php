<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Position;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class PositionController extends AbstractFOSRestController
{
    const WIKI_URL = 'https://wiki.cuadc.org';

    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @Route("/positions.{_format}", format="html", methods={"GET"}, name="get_positions")
     */
    public function cgetAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Position::class);
        $positions = $repository->createQueryBuilder('p')
            ->orderBy('p.name')->getQuery()->getResult();

        if ($request->getRequestFormat() == 'html') {
            return $this->render('position/index.html.twig', [
                'positions' => $positions,
            ]);
        } else {
            return $this->view($positions);
        }
    }

    /**
     * @Route("/positions/{identifier}.{_format}", format="html", methods={"GET"}, name="get_position")
     */
    public function getAction(Request $request, $identifier)
    {
        $repository = $this->getDoctrine()->getRepository(Position::class);
        $position = $repository->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $identifier)->getQuery()->getOneOrNullResult();

        if ($request->getRequestFormat() == 'html') {
            return $this->render('position/view.html.twig', [
                'position' => $position,
            ]);
        } else {
            return $this->view($position);
        }
    }

    public function wikiEmbedAction($pageName)
    {
        //https://wiki.cuadc.org/w/api.php?action=parse&page=Head%20Carpenter&format=json
        $url = self::WIKI_URL.'/w/api.php';
        $params = [
            'action' => 'parse',
            'page' => $pageName,
            'format' => 'json',
        ];

        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                ['query' => $params],
            );
            $data = $response->toArray();
            if (isset($data['error'])) {
                return new Response('This page does not exist in the CUADC wiki');
            }
            $html = $data['parse']['text']['*'];
            $html .= '<p class="attribution">Wiki content is licensed under the
            <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">
            Creative Commons Attribution-ShareAlike Licence</a>.</p>';
            $response = new Response($html);
            $response->setPublic();
            $response->setMaxAge(3600);
            return $response;
        } catch (\Exception $e) {
            return new Response('Error fetching data from the CUADC wiki');
        }
        
    }

    /**
     * @Route("/wiki/{pageName}", format="html", methods={"GET"}, name="position_wiki", requirements={"pageName"=".+"})
     */
    public function wikiRedirectAction($pageName)
    {
        $repository = $this->getDoctrine()->getRepository(Position::class);
        $position = $repository->createQueryBuilder('p')
            ->where('p.wikiName = :name')
            ->setParameter('name', $pageName)->getQuery()->getOneOrNullResult();

        if ($position) {
            return $this->redirectToRoute('get_position', ['identifier' => $position->getSlug()]);
        } else {
            return $this->redirect(self::WIKI_URL.'/wiki/'.$pageName);   
        }
    }
}