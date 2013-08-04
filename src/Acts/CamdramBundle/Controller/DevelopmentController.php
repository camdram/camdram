<?php
namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DevelopmentController
 *
 * Very basic controller used by the small 'Development' section, which contains information about contributing to
 * Camdram.
 *
 * @package Acts\CamdramBundle\Controller
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
        $api = $this->get('acts.social_api.apis.github');
        $owner = 'camdram';
        $repo = 'camdram';

        $data = array(
            'repo' => $api->doRepo($owner, $repo),
            'inprogress' => $api->doIssues($owner, $repo, 'open', null, 'in-progress'),
            'recent' => $api->doIssues($owner, $repo, 'open', 'created'),
            'fixed' => $api->doIssues($owner, $repo, 'closed', 'updated'),
        );

        $response = $this->render('ActsCamdramBundle:Development:activity.html.twig', $data);
        $response->setSharedMaxAge(60*15);
        return $response;
    }
}
