<?php
namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Acts\CamdramBundle\Entity\Show;

/**
 * Class LegacyApiController
 *
 * Very basic controller used for replicating the api of the old output
 *
 */
class LegacyApiController extends Controller
{
    /*
     * @param null|string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $slug = "", $id = "", $_format)
    {
        $repo= $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
	if($id != "" && is_numeric($id) )
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

        $response = $this->render(
            'ActsCamdramBundle:Show:legacyapi.'.$_format.'.twig',
            array('show' => $show)
            );
        if ($_format == 'xml') {
            $response->headers->set('Content-Type', 'text/xml');
        }
        return $response;
    }
}

