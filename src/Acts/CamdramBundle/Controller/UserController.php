<?php

namespace Acts\CamdramBundle\Controller;

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
        $this->get('camdram.security.utils')->ensureRole('ROLE_ADMIN');
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