<?php

namespace Acts\CamdramBundle\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramBundle\Form\Type\UserType;
use Acts\CamdramBundle\Form\Type\AddAclType;

/**
 * @RouteResource("User")
 */
class UserController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\User';

    protected $type = 'user';

    protected $type_plural = 'users';

    protected $search_index = 'user';

    protected function getRouteParams($user)
    {
        return array('identifier' => $user->getId());
    }

    protected function checkAuthenticated()
    {
        $this->get('camdram.security.utils')->ensureRole('IS_AUTHENTICATED_FULLY');
        $this->get('camdram.security.utils')->ensureRole('ROLE_SUPER_ADMIN');
    }

    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBy(array('id' => $identifier));

        if (!$entity) {
            throw $this->createNotFoundException('That user does not exist');
        }

        return $entity;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:User');
    }

    protected function getForm($society = null)
    {
        return $this->createForm(new UserType(), $society);
    }

    /**
     * Action which returns a list of entities.
     *
     * If a search term 'q' is provided, then a text search is performed against Sphinx. Otherwise, a paginated
     * collection of all entities is returned.
     */
    public function cgetAction(Request $request)
    {
        $this->checkAuthenticated();
        if ($request->get('q')) {
            /** @var $search_provider \Acts\CamdramBundle\Service\Search\ProviderInterface */
            $search_provider = $this->get('acts.camdram.search_provider');
            $data = $search_provider->executeAutocomplete($this->search_index, $request->get('q'), $request->get('limit'), array());
        }
        else {
            $repo = $this->getRepository();
            $qb = $repo->createQueryBuilder('e');
            $adapter = new DoctrineORMAdapter($qb);
            $data = new Pagerfanta($adapter);
            $data->setMaxPerPage(25);
        }

        return $this->view($data, 200)
            ->setTemplateVar('result')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
        ;
    }

    public function newAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(new AddAclType(), array('identifier' => $identifier));

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:User:ace-new-form.html.twig');
    }

    public function postAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(new AddAclType(), array('identifier' => $identifier));
        $form->bind($request);
        if ($form->isValid()) {
            $user = $this->getEntity($identifier);
            $data = $form->getData();
            $this->get('camdram.security.acl.provider')->grantAccess($data['entity'], $user, $this->getUser());
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($user));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('user')
                ->setTemplate('ActsCamdramBundle:User:ace-new.html.twig');
        }
    }

}