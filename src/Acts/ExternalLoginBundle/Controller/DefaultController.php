<?php

namespace Acts\ExternalLoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function testLoginAction(Request $request, $service)
    {
        if ($this->container->get('kernel')->getEnvironment() == 'prod') {
            die('This page is not accessible in the production environment');
        }

        if ($request->getMethod() == 'POST') {
            $params = array(
                'service'   => $service,
                'id'        => $request->get('id'),
                'name'      => $request->get('name'),
                'email'     => $request->get('email'),
                'username'  => $request->get('username'),
                'picture'   => $request->get('picture'),
            );

            return $this->redirect($this->generateUrl('acts_external_login_auth', $params));
        }

        return $this->render('ActsExternalLoginBundle:Default:test_login.html.twig', array('service' => $service));
    }
}
