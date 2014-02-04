<?php
namespace Acts\CamdramLegacyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramLegacyBundle\Entity\ApiShow;
use Illuminate\Routing\UrlGenerator;

/**
 * Class LegacyApiController
 *
 * Very basic controller 
 *
 */
class LegacyApiController extends Controller
{
    public function showAction(Request $request, $slug = null, $id = null, $_format)
    {
        $repo= $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
	if($id)
	{
	   $show = $repo->find($id);
	}
	else
	{
	    $show = $repo->findOneBySlug($slug);
	}
        if (!$show) {
            throw $this->createNotFoundException('That show does not exist.');
        }

	$apiShow = new ApiShow($show, $this);

	$serializer = $this->get('jms_serializer');
	$response = new Response($serializer->serialize($apiShow, $_format));

        return $response;
    }
}

