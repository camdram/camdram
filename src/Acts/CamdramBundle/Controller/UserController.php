<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramBundle\Form\Type\UserType;

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

}