<?php
namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use  Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;
use Doctrine\ORM\EntityManager;

class CamdramUserProvider implements UserProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Actually loads by id...but has to comply with the Symfony interface
     *
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws
     */
    public function loadUserByUsername($id)
    {
        $user = $this->em->getRepository('ActsCamdramBundle:User')->findOneById($id);
        if ($user) return $user;
        else throw UnsupportedUserException();
    }

    public function loadUserByServiceAndUser($service, $info)
    {
        if ($service == 'local') {
            return $this->em->getRepository('ActsCamdramBundle:User')->findOneById($info['id']);
        }

        $res = $this->em->createQuery('SELECT u FROM ActsCamdramBundle:User u JOIN u.identities i WHERE i.service = :service AND (i.remote_id = :id OR i.remote_user = :username)')
                ->setParameter('service', $service)
                ->setParameter('id', $info['id'])
                ->setParameter('username', $info['username'])
                ->getResult();

        if (count($res) > 0) {
            return $res[0];
        }
        else {
            throw new IdentityNotFoundException(sprintf('An identity cannot be found for "%s" and credentials %i/%s', $service, $info['id'], $info['username']));
        }
    }

    public function updateAccessToken(User $user, $service, $access_token)
    {
        $s = $user->getIdentityByServiceName($service);
        if ($s) {
            $s->loadAccessToken($access_token);
            $this->em->flush();
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Acts\CamdramBundle\Entity\User';
    }

    public function mergeUsers($user1, $user2)
    {
        //Merge old camdram auth tokens
        $tokens = $this->em->getRepository('ActsCamdramBundle:Access')->findBy(array('uid' => $user2->getId()));
        foreach ($tokens as $token) {
            $token->setUid($user1->getId());
        }
        $this->em->flush();

        $tokens = $this->em->getRepository('ActsCamdramBundle:Access')->findBy(array('issuer_id' => $user2->getId()));
        foreach ($tokens as $token) {
            $token->setIssuerId($user1->getId());
        }
        $this->em->flush();

        $tokens = $this->em->getRepository('ActsCamdramBundle:Access')->findBy(array('revoke_id' => $user2->getId()));
        foreach ($tokens as $token) {
            $token->setRevokeId($user1->getId());
        }
        $this->em->flush();

        //Merge emails
        $emails = $this->em->getRepository('ActsCamdramBundle:Email')->findBy(array('user_id' => $user2->getId()));
        foreach ($emails as $email) {
            $email->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge email aliases
        $aliases = $this->em->getRepository('ActsCamdramBundle:EmailAlias')->findBy(array('user_id' => $user2->getId()));
        foreach ($aliases as $alias) {
            $alias->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge email sigs
        $sigs = $this->em->getRepository('ActsCamdramBundle:EmailSig')->findBy(array('user_id' => $user2->getId()));
        foreach ($sigs as $sig) {
            $sig->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge forum messages
        $msgs = $this->em->getRepository('ActsCamdramBundle:EmailSig')->findBy(array('user_id' => $user2->getId()));
        foreach ($msgs as $msg) {
            $msg->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge knowledge base
        $kbs = $this->em->getRepository('ActsCamdramBundle:KnowledgeBaseRevision')->findBy(array('user_id' => $user2->getId()));
        foreach ($kbs as $kb) {
            $kb->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge mailing list members
        $r = $this->em->getRepository('ActsCamdramBundle:MailingListMember');
        $members = $r->findBy(array('user_id' => $user2->getId()));
        foreach ($members as $member) {
            if ($m2 = $r->findOneBy(array('list_id' => $member->getListId(), 'user_id' => $user1->getId()))) {
                $this->em->remove($member);
            }
            else {
                $member->setUserId($user1->getId());
            }
        }
        $this->em->flush();

        //Merge reviews
        $reviews = $this->em->getRepository('ActsCamdramBundle:Review')->findBy(array('user_id' => $user2->getId()));
        foreach ($reviews as $review) {
            $review->setUserId($user1->getId());
        }
        $this->em->flush();

        //Merge user identities
        $identities = $this->em->getRepository('ActsCamdramSecurityBundle:UserIdentity')->findBy(array('user' => $user2));
        foreach ($identities as $identity) {
            $identity->setUser($user1);
        }
        $this->em->flush();

        //Merge user groups
        $groups = $this->em->getRepository('ActsCamdramSecurityBundle:Group')->findByUser($user2);
        foreach ($groups as $group) {
            $group->addUser($user1);
            $group->removeUser($user2);
        }
        $this->em->flush();

        if ($user2->getPerson() && !$user1->getPerson()) {
            $user1->setPerson($user2->getPerson());
        }

        $this->em->remove($user2);
        $this->em->flush();
    }
}
