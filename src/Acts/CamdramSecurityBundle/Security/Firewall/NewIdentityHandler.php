<?php
namespace Acts\CamdramSecurityBundle\Security\Firewall;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Acts\CamdramSecurityBundle\Security\ServiceMap;
use Acts\CamdramSecurityBundle\Security\NameUtils;
use Acts\CamdramSecurityBundle\Security\Authentication\Provider\CamdramProvider;
use Acts\CamdramSecurityBundle\Security\Authentication\Token\CamdramUserToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

use Acts\CamdramBundle\Entity\UserIdentity;


class NewIdentityHandler
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $security_context;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\ServiceMap
     */
    private $service_map;

    /**
     * @var \Symfony\Component\Security\Core\User\UserCheckerInterface
     */
    private $user_checker;

    /**
     * @var \Acts\CamdramSecurityBundle\Security\NameUtils
     */
    private $name_utils;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(SecurityContextInterface $security_context, EntityManager $em, ServiceMap $service_map,
                                    UserCheckerInterface $user_checker, NameUtils $name_utils,
                                    Session $session, RouterInterface $router) {
        $this->em = $em;
        $this->security_context = $security_context;
        $this->service_map = $service_map;
        $this->user_checker = $user_checker;
        $this->name_utils = $name_utils;
        $this->session = $session;
        $this->router = $router;
    }

    public function handle(CamdramUserToken $new_token) {
        if (($t = $this->security_context->getToken()) && $t->getUser()) {
            //User is already logged in - add identity to existing account, perhaps after asking for confirmation
            return $this->handleAlreadyLoggedIn($new_token, $t->getUser());
        }
        else {
            // User isn't logged in - try and find existing camdram account or create new one
            return $this->handleNotLoggedIn($new_token);
        }

    }

    public function handleAlreadyLoggedIn(CamdramUserToken $new_token, UserInterface $existing_user)
    {
        $service = $this->service_map->getServiceByName($new_token->getLastService()->getName());

        //if ($existing_user) $new_token->addPotentialUser($existing_user);
        $service = $new_token->getFirstService();

        if ($this->name_utils->isSamePerson($existing_user->getName(), $service->getUserInfo('name'))) {
            //The name on the service's user info and the name on the user account are sufficiently similar

            $i = new UserIdentity();
            $i->setRemoteUser($service->getUserInfo('username'));
            $i->setRemoteId($service->getUserInfo('id'));
            $i->setService($service->getName());
            $i->loadAccessToken($service->getAccessToken());
            $i->setUser($existing_user);
            $existing_user->addIdentity($i);

            $this->em->persist($i);
            $this->em->flush();

            $this->user_checker->checkPostAuth($existing_user);

            if ($new_token->getPotentialUserCount() == 0) {
                $new_token->setAuthenticated(true);
                return $new_token;
            }
            else {
                return new RedirectResponse($this->router->generate('camdram_security_connect_users_process'));
            }
        }
        else {
            //We want to get confirmation from the user before adding this identity to the current user
            return new RedirectResponse($this->router->generate('camdram_security_confirm_add_identity'));
        }
    }

    public function handleNotLoggedIn($new_token)
    {

        return new RedirectResponse($this->router->generate('camdram_security_connect_users'));
    }

}