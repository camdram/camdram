<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acts\CamdramBundle\Entity\Show;

/**
 * Class SigninsheetController
 *
 * Very basic controller used for generating sign in sheets. Currently the two
 * forms supported are a basic HTML table and a CSV format for downloading.
 */
class SigninsheetController extends Controller
{
    /*
     * @param null|string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $slug = null, $_format)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
        $show = $repo->findOneBySlug($slug);
        if (!$show) {
            throw $this->createNotFoundException('That show does not exist.');
        }
        $events = $show->getAllPerformances();
        if (!$events) {
            throw $this->createNotFoundException('There are no performances associated with this show.');
        }
        $last_perf = end($events);
        $last_perf = $last_perf['date'];
        $one_week_later = clone $events[0]['date'];
        $one_week_later->modify('+7 days');
        if ($one_week_later >= $last_perf) {
            $date_format = 'D';
        } else {
            $date_format = 'D j/n/y';
        }

        $response = $this->render(
            'ActsCamdramBundle:Show:signinsheet.'.$_format.'.twig',
            array('show' => $show, 'events' => $events, 'date_format' => $date_format)
            );
        if ($_format == 'csv') {
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'inline; attachment; filename="signinsheet.csv"');
        }

        return $response;
    }
}
