<?php

namespace Acts\CamdramAdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface as Kernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for viewing Camdram's log files
 *
 * @Security("is_granted('ROLE_SUPER_ADMIN') and is_granted('IS_AUTHENTICATED_FULLY')")
 */
class LogController extends AbstractController
{
    /**
     * @Route("/logs", name="acts_camdram_logs")
     */
    public function indexAction(): Response
    {
        return $this->render('admin/log/index.html.twig');
    }

    /**
     * @Route("/logs/get/{file}", name="acts_camdram_logs_get")
     */
    public function getAction(Kernel $kernel, string $file): Response
    {
        $log_dir = $kernel->getLogDir();
        $env = $kernel->getEnvironment();

        switch ($file) {
            case 'action.log':
                $file = "$log_dir/$env.action.log";
                break;
            case 'mailer.log':
                $file = "$log_dir/$env.mailer.log";
                break;
            case 'symfony.log':
                $file = "$log_dir/$env.log";
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

        $text = `tail "$file" -n500`;
        $response = new Response($text);
        $response->headers->set("Content-Type", "text/plain; charset=UTF-8");
        return $response;
    }
}
