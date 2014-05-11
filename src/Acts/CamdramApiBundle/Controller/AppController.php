<?php

namespace Acts\CamdramApiBundle\Controller;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramApiBundle\Form\Type\ExternalAppType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AppController
 * @package Acts\CamdramApiBundle\Controller
 * @RouteResource("App")
 */
class AppController extends FOSRestController
{
    private function checkAuthenticated()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AuthenticationException();
        }
    }

    public function cgetAction()
    {
        $this->checkAuthenticated();

        $repo = $this->getDoctrine()->getRepository('ActsCamdramApiBundle:ExternalApp');
        $user_apps = $repo->findByUser($this->getUser());

        return $this->render('ActsCamdramApiBundle:App:index.html.twig', array(
            'user_apps' => $user_apps
        ));
    }

    private function getApp($app_id)
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramApiBundle:ExternalApp');
        $app = $repo->getByUserAndId($this->getUser(), $app_id);
        if (!$app) {
            return $this->createNotFoundException();
        }
        return $app;
    }

    public function getAction($app_id)
    {
        $app = $this->getApp($app_id);
        $form = $this->getForm($app);

        return $this->render('ActsCamdramApiBundle:App:view.html.twig', array(
                'ex_app' => $app,
                'form' => $form->createView(),
            ));
    }

    private function getForm($app = null)
    {
        return $this->createForm(new ExternalAppType(), $app);
    }

    public function newAction()
    {
        $this->checkAuthenticated();
        return $this->render('ActsCamdramApiBundle:App:new.html.twig', array(
            'form' => $this->getForm()->createView()
        ));
    }

    public function postAction(Request $request)
    {
        $this->checkAuthenticated();
        $form = $this->getForm(new ExternalApp());
        $form->submit($request);
        if ($form->isValid()) {
            $app = $form->getData();
            $app->setUser($this->getUser());
            $clientManager = $this->get('fos_oauth_server.client_manager.default');
            $clientManager->updateClient($app);
            return $this->redirect($this->generateUrl('get_apps'));
        } else {
            return $this->render('ActsCamdramApiBundle:App:new.html.twig', array(
                'form' => $this->getForm()->createView()
            ));
        }
    }

    public function putAction(Request $request, $app_id)
    {
        $this->checkAuthenticated();
        $app = $this->getApp($app_id);
        $form = $this->getForm($app);
        $form->submit($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('get_app', array('app_id' => $app->getRandomId())));
        } else {
            return $this->render('ActsCamdramApiBundle:App:new.html.twig', array(
                    'form' => $this->getForm()->createView()
                ));
        }
    }

    public function removeAction($app_id)
    {
        $this->checkAuthenticated();
        $app = $this->getApp($app_id);
        $this->getDoctrine()->getManager()->remove($app);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('get_apps'));
    }
}
