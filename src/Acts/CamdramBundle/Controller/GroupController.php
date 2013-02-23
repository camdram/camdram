<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramSecurityBundle\Entity\Group;
use Acts\CamdramSecurityBundle\Form\Type\GroupType;
use Acts\CamdramBundle\Form\Type\AddAclType;

/**
 * @RouteResource("Group")
 */
class GroupController extends AbstractRestController
{
    protected $class = 'Acts\\CamdramSecurityBundle\\Entity\\Group';

    protected $type = 'group';

    protected $type_plural = 'groups';

    protected $search_index = 'group';

    protected function getRouteParams($group)
    {
        return array('identifier' => $group->getShortName());
    }

    protected function checkAuthenticated()
    {
        $this->get('camdram.security.utils')->ensureRole('IS_AUTHENTICATED_FULLY');
        $this->get('camdram.security.utils')->ensureRole('ROLE_ADMIN');
    }

    protected function getEntity($identifier)
    {
        $entity = $this->getRepository()->findOneBy(array('short_name' => $identifier));

        if (!$entity) {
            throw $this->createNotFoundException('That group does not exist');
        }

        return $entity;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramSecurityBundle:Group');
    }

    protected function getForm($society = null)
    {
        return $this->createForm(new GroupType(), $society);
    }

    public function newAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(new AddAclType(), array('identifier' => $identifier));

        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':ace-new-form.html.twig');
    }

    public function postAceAction(Request $request, $identifier)
    {
        $form = $this->createForm(new AddAclType(), array('identifier' => $identifier));
        $form->bind($request);
        if ($form->isValid()) {
            $group = $this->getEntity($identifier);
            $data = $form->getData();
            $this->get('camdram.security.acl.provider')->grantAccess($data['entity'], $group, $this->getUser());
            return $this->routeRedirectView('get_'.$this->type, $this->getRouteParams($group));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('group')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':ace-new.html.twig');
        }
    }

}
