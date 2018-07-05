<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for viewing Camdram's log files
 *
 * @Security("has_role('ROLE_SUPER_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class LogController extends Controller
{
    /**
     * @Route("/logs", name="acts_camdram_logs")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ActsCamdramAdminBundle:Log:index.html.twig');
    }

    /**
     * @Route("/logs/get/{file}", name="acts_camdram_logs_get")
     *
     * @param unknown $file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction($file)
    {
        $log_dir = $this->get('kernel')->getRootDir().'/logs/';
        $env = $this->get('kernel')->getEnvironment();

        switch ($file) {
            case 'action.log':
                $file = $log_dir.$env.'.action.log';
                break;
            case 'mailer.log':
                $file = $log_dir.$env.'.mailer.log';
                break;
            case 'symfony.log':
                $file = $log_dir.$env.'.log';
                break;
            case 'error.log':
                $file = '/var/log/apache2/error.log';
                break;
            case 'access.log':
                $file = '/var/log/apache2/camdram_prod.log';
                break;
            default:
                throw $this->createNotFoundException();
        }

        if (!is_readable($file)) {
            throw $this->createNotFoundException();
        }

        $text = nl2br(`tail "$file" -n500`);
        return new Response($text);
    }
}
