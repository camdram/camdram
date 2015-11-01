<?php

namespace Acts\CamdramLegacyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramLegacyBundle\Entity\ApiShow;

/**
 * Class LegacyApiController
 *
 * Very basic controller
 */
class LegacyApiController extends Controller
{
    public function showAction(Request $request)
    {
        $showid = $request->query->get('showid');
        $type = $request->query->get('type');

        if ($type != 'json') {
            $type = 'xml';
        }
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
        $show = $repo->findOneBySlug($showid);

        if (!$show) {
            if (is_numeric($showid)) {
                $show = $repo->find($showid);
            }
        }
        if (!$show) {
            throw $this->createNotFoundException('That show does not exist.');
        }

        $apiShow = new ApiShow($show, $this->get('router'));

        $serializer = $this->get('jms_serializer');
        $response = new Response($serializer->serialize($apiShow, $type));

        $response->headers->set('content-type', $request->getMimeType($type));

        return $response;
    }
}
