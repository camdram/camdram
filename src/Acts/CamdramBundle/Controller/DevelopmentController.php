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
        return $this->render('development/index.html.twig');
    }

    public function gitInfoAction()
    {
        $git_info = [
            'tag' => exec('git tag --points-at HEAD'),
            'hash' => exec('git rev-parse HEAD'),
        ];

        if (empty($git_info['tag'])) {
            $git_info['branch'] = exec('git name-rev --name-only HEAD');
        }

        $response = $this->render('development/git-info.html.twig', ['git_info' => $git_info]);
        $response->setSharedMaxAge(3600 * 24);
        return $response;
    }

    public function activityAction(\Github\Client $github)
    {
        try {
            $github->authenticate(
                $this->getParameter('github_id'),
                $this->getParameter('github_secret'),
                \Github\Client::AUTH_URL_CLIENT_ID
            );
            $owner = 'camdram';
            $repoName = 'camdram';

            $repo = $github->api('repo')->show($owner, $repoName);
            $contributors = $github->api('repo')->contributors($owner, $repoName);
            $inprogress = $github->api('issues')->all($owner, $repoName, ['labels' => 'in-progress']);
            $recent = $github->api('issues')->all($owner, $repoName, ['state' => 'open', 'sort' => 'created']);
            $fixed = $github->api('issues')->all($owner, $repoName, ['state' => 'closed', 'sort' => 'updated']);

            $users = array();
            foreach ($contributors as $contributor) {
                $users[] = $github->api('user')->show($contributor['login']);
            }

            $data = array(
                'repo' => $repo,
                'users' => array_slice($users, 0, 10),
                'inprogress' => array_slice($inprogress, 0, 10),
                'recent' => array_slice($recent, 0, 10),
                'fixed' => array_slice($fixed, 0, 10),
            );

            $response = $this->render('development/activity.html.twig', $data);
            $response->setSharedMaxAge(60 * 15);

            return $response;
        } catch (\Github\Exception\RuntimeException $ex) {
            return $this->render('development/github-error.html.twig');
        }
    }
}
