<?php

namespace Acts\CamdramAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for viewing Camdram's log files
 *
 */
class LogController extends Controller
{

    public function indexAction()
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_SUPER_ADMIN');

        return $this->render('ActsCamdramAdminBundle:Log:index.html.twig');
    }

    public function getAction($file)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_SUPER_ADMIN');

        $log_dir = $this->get('kernel')->getRootDir().'/logs/';
        $env = $this->get('kernel')->getEnvironment();

        switch ($file) {
            case 'action.log':
                $file = $log_dir.$env.'.action.log';
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

        $text = nl2br(`tail $file -n500`);
        return new Response($text);
    }

}