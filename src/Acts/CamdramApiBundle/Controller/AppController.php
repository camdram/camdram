<?php

namespace Acts\CamdramApiBundle\Controller;

use Acts\CamdramApiBundle\Entity\ExternalApp;
use Acts\CamdramApiBundle\Form\Type\ExternalAppType;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Util\Random;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AppController
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class AppController extends AbstractController
{
    /**
     * @Route("/apps", methods={"GET"}, name="get_apps")
     */
    public function cgetAction()
    {
        $repo = $this->getDoctrine()->getRepository(ExternalApp::class);
        $user_apps = $repo->findByUser($this->getUser());

        return $this->render('api/app/index.html.twig', array(
            'user_apps' => $user_apps
        ));
    }

    private function getApp($app_id)
    {
        $repo = $this->getDoctrine()->getRepository(ExternalApp::class);
        $app = $repo->getByUserAndId($this->getUser(), $app_id);
        if (!$app) {
            throw $this->createNotFoundException("You do not own an app with ID $app_id.");
        }

        return $app;
    }

    /**
     * @Route("/apps/new", methods={"GET"}, name="new_app")
     */
    public function newAction()
    {
        $form = $this->createForm(ExternalAppType::class);

        return $this->render('api/app/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/apps/{app_id}", methods={"GET"}, name="get_app")
     */
    public function getAction($app_id)
    {
        $app = $this->getApp($app_id);
        $form = $this->createForm(ExternalAppType::class, $app, ['method' => 'put']);

        return $this->render('api/app/view.html.twig', array(
                'ex_app' => $app,
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/apps", methods={"POST"}, name="post_app")
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

    /**
     * @Route("/apps/{app_id}", methods={"PUT"}, name="put_app")
     */
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

    /**
     * @Route("/apps/{app_id}", methods={"DELETE"}, name="delete_app")
     */
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
     * @Route("/apps/{app_id}/regenerate-secret", methods={"PATCH"}, name="regenerate_app_secret")
     */
    public function regenerateSecretAction(Request $request, $app_id)
    {
        if (!$this->isCsrfTokenValid('regenerate_app_secret', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $app = $this->getApp($app_id);
        $app->setSecret(Random::generateToken());
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('get_app', ['app_id' => $app->getRandomId()]));
    }
}
