<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

use Acts\CamdramBundle\Entity\Entity;

abstract class AbstractRestController extends FOSRestController
{

    protected $class;

    protected $type;

    protected $type_plural;

    protected $search_index = 'entity';

    protected function getController()
    {
        return ucfirst($this->type);
    }

    protected function checkAuthenticated()
    {

    }

    protected function getRouteParams($entity)
    {
        return array('identifier' => $entity->getSlug());
    }


    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBySlug($identifier);

        if (!$entity) {
            throw $this->createNotFoundException('That '.$this->type.' does not exist');
        }

        return $entity;
    }

    abstract protected function getRepository();

    abstract protected function getForm($entity = null);


    public function newAction()
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm();
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':new.html.twig');
    }

    public function postAction(Request $request)
    {
        $this->checkAuthenticated();
        $this->get('camdram.security.acl.helper')->ensureGranted('CREATE', $this->class);

        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get('camdram.security.acl.provider')->grantAccess($form->getData(), $this->getUser(), $this->getUser());
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':new.html.twig');
        }
    }

    public function editAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':edit.html.twig');
    }

    public function putAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $entity);

        $form = $this->getForm($entity);

        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($form->getData()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':edit.html.twig');
        }
    }

    public function removeAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $entity);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return $this->routeRedirectView('get_'.$this->type_plural);
    }

    public function cgetAction(Request $request)
    {
        $this->checkAuthenticated();
        if ($request->get('q')) {
            /** @var $search_provider \Acts\CamdramBundle\Service\Search\ProviderInterface */
            $search_provider = $this->get('acts.camdram.search_provider');

            if ($request->query->has('autocomplete')) {
                $data = $search_provider->executeAutocomplete($this->search_index, $request->get('q'), $request->get('limit'));
            }
            else {
                $data = $search_provider->executeTextSearch($this->search_index, $request->get('q'));
            }
        }
        else {
            $repo = $this->getRepository();
            $qb = $repo->createQueryBuilder('e');
            $adapter = new DoctrineORMAdapter($qb);
            $data = new Pagerfanta($adapter);
        }

        $view = $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
        ;

        return $view;
    }

    public function getAction($identifier)
    {
        $this->checkAuthenticated();
        $entity = $this->getEntity($identifier);
        $view = $this->view($entity, 200)
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':show.html.twig')
            ->setTemplateVar($this->type)
        ;

        return $view;
    }

    public function toolbarAction(Request $request)
    {
        $entity = null;

        if ($request->query->has('identifier')) {
            $entity = $this->getEntity($request->get('identifier'));
        }
        $type = $this->type;

        if ($entity) {
            $routes = array(
                'edit' => 'edit_'.$type,
                'new' => 'new_'.$type,
                'delete' => 'remove_'.$type,
            );
        }
        else {
            $routes = array('new' => 'new_'.$type);
        }
        $label = $type;
        $identity = new ObjectIdentity('class', $this->class);

        return $this->render('ActsCamdramBundle:Entity:toolbar.html.twig', array(
            'routes' => $routes,
            'entity' => $entity,
            'label' => $label,
            'type' => $type,
            'identity' => $identity,
        ));
    }

}
