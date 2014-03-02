<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application,
    Acts\CamdramBundle\Entity\Person,
    Acts\CamdramBundle\Entity\Role,
    Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Event\CamdramEvents;
use Acts\CamdramBundle\Event\TechieAdvertEvent;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use Doctrine\DBAL\Query\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\RoleType;
use Acts\CamdramBundle\Form\Type\ShowType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Gedmo\Sluggable\Util as Sluggable;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("My-Show")
 */
class ShowAdminController extends Controller
{

    public function cgetAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');

        $ids = $this->get('camdram.security.acl.provider')->getShowIdsByUser($this->getUser());
        $shows = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findIdsByDate($ids);
        return $this->render('ActsCamdramBundle:ShowAdmin:index.html.twig', array('shows' => $shows));
    }

    public function cgetUnauthorisedAction(Request $request)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');

        $shows = $this->get('acts.camdram.moderation_manager')->getEntitiesToModerate();
        return $this->render('ActsCamdramBundle:ShowAdmin:unauthorised.html.twig', array('shows' => $shows));
    }

}
