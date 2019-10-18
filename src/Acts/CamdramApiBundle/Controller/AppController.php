<?php

namespace Acts\CamdramApiBundle\Controller;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramApiBundle\Form\Type\ExternalAppType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Util\Random;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AppController
 *
 * @Rest\RouteResource("App")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AppController extends AbstractFOSRestController
{

    public function cgetAction()
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramApiBundle:ExternalApp');
        $user_apps = $repo->findByUser($this->getUser());

        return $this->render('api/app/index.html.twig', array(
            'user_apps' => $user_apps
        ));
    }

    private function getApp($app_id)
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramApiBundle:ExternalApp');
        $app = $repo->getByUserAndId($this->getUser(), $app_id);
        if (!$app) {
            throw $this->createNotFoundException("You do not own an app with ID $app_id.");
        }

        return $app;
    }

    public function getAction($app_id)
    {
        $app = $this->getApp($app_id);
        $form = $this->createForm(ExternalAppType::class, $app, ['method' => 'put']);

        return $this->render('api/app/view.html.twig', array(
                'ex_app' => $app,
                'form' => $form->createView(),
            ));
    }

    public function newAction()
    {
        $form = $this->createForm(ExternalAppType::class);

        return $this->render('api/app/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Rest\Post("/api/apps")
     */
    public function postAction(Request $request, ClientManagerInterface $clientManager)
    {
        $form = $form = $this->createForm(ExternalAppType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $app = $form->getData();
            $app->setUser($this->getUser());
            $clientManager->updateClient($app);

            return $this->redirect($this->generateUrl('get_apps'));
        } else {
            return $this->render('api/app/new.html.twig', array(
                'form' => $form->createView()
            ));
        }
    }

    public function putAction(Request $request, $app_id)
    {
        $app = $this->getApp($app_id);
        $form = $this->createForm(ExternalAppType::class, $app, ['method' => 'put']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('get_app', array('app_id' => $app->getRandomId())));
        }
        else {
            return $this->render('api/app/view.html.twig', array(
                'ex_app' => $app,
                'form' => $form->createView()
            ));
        }
    }

    public function deleteAction(Request $request, $app_id)
    {
        if (!$this->isCsrfTokenValid('delete_app', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $app = $this->getApp($app_id);
        $this->getDoctrine()->getManager()->remove($app);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('get_apps'));
    }

    /**
     * @Rest\Patch("/apps/{app_id}/regenerate-secret")
     */
    public function regenerateSecretAction(Request $request, $app_id)
    {
        if (!$this->isCsrfTokenValid('regenerate_app_secret', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $app = $this->getApp($app_id);
        $app->setSecret(Random::generateToken());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('get_app', array('app_id' => $app->getRandomId())));
    }
}
