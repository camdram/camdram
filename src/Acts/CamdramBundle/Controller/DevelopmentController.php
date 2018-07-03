<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DevelopmentController
 *
 * Very basic controller used by the small 'Development' section, which contains information about contributing to
 * Camdram.
 */
class DevelopmentController extends Controller
{
    public function indexAction()
    {
        $response = $this->render('ActsCamdramBundle:Development:index.html.twig');

        return $response;
    }

    public function activityAction()
    {
        /**
         *
         * @var \Github\Client $api
         */
        try {
            $github = $this->get('github.api');
            $github->authenticate(
                $this->getParameter('github_id'),
                $this->getParameter('github_secret'),
                \Github\Client::AUTH_URL_CLIENT_ID
            );
            $owner = 'camdram';
            $repoName = 'camdram';
    
            $repo = $github->api('repo')->show($owner, $repoName);
            $inprogress = $github->api('issues')->all($owner, $repoName, ['labels' => 'in-progress']);
            $recent = $github->api('issues')->all($owner, $repoName, ['state' => 'open', 'sort' => 'created']);
            $fixed = $github->api('issues')->all($owner, $repoName, ['state' => 'closed', 'sort' => 'updated']);
            
            $data = array(
                'repo' => $repo,
                'inprogress' => array_slice($inprogress, 0, 10),
                'recent' => array_slice($recent, 0, 10),
                'fixed' => array_slice($fixed, 0, 10),
            );
    
            $response = $this->render('ActsCamdramBundle:Development:activity.html.twig', $data);
            $response->setSharedMaxAge(60 * 15);
    
            return $response;
        } catch (\Github\Exception\RuntimeException $ex) {
            return $this->render('ActsCamdramBundle:Development:github-error.html.twig');
        }
    }
}
