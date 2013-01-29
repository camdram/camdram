<?php
namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DevelopmentController extends Controller
{
    public function indexAction()
    {
        $api = $this->get('acts.social_api.apis.github');
        $owner = 'camdram';
        $repo = 'camdram';

        $data = array(
            'repo' => $api->doRepo($owner, $repo),
            'inprogress' => $api->doIssues($owner, $repo, 'open', null, 'in-progress'),
            'recent' => $api->doIssues($owner, $repo, 'open', 'created'),
            'fixed' => $api->doIssues($owner, $repo, 'closed', 'updated'),
        );

        return $this->render('ActsCamdramBundle:Development:index.html.twig', $data);
    }
}
