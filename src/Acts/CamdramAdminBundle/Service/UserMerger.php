<?php

namespace Acts\CamdramAdminBundle\Service;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;

class UserMerger
{
    private $entityManager;

    private $formFactory;

    public function __construct(EntityManager $entityManager, FormFactory $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function createForm()
    {
        return $this->formFactory->createBuilder()
            ->add('email', 'text', array('label' => "Other user's email", 'required' => true))
            ->add('keep_user', 'choice', array(
                'label' => 'Keep which user?',
                'expanded' => true,
                'choices' => array(
                    'this' => 'This user',
                    'other' => 'The other user'
                ),
                'data' => 'this'
            ))
            ->getForm();
    }

    /**
     * @param Person $person1
     * @param Person $person2
     * @param $keepFirst
     *
     * @return Person
     */
    public function mergeUsers(User $user1, User $user2, $keepFirst)
    {
        if (!$keepFirst) {
            $tempUser = $user2;
            $user2 = $user1;
            $user1 = $tempUser;
        }

        foreach ($user2->getAuthorisedShows() as $show)
        {
            $show->setAuthorisedBy($user1);
        }
        foreach ($user2->getAces() as $ace) {
            $ace->setUser($user1);
        }
        foreach ($user2->getApps() as $app) {
            $app->setUser($user1);
        }
        foreach ($user2->getKnowledgeBaseRevisions() as $revision) {
            $revision->setUser($user1);
        }
        foreach ($user2->getAceGrants() as $ace) {
            $ace->setGrantedBy($user1);
        }
        foreach ($user2->getEmailBuilders() as $email) {
            $email->setUser($user1);
        }
        foreach ($user2->getEmailAliases() as $alias) {
            $alias->setUser($user1);
        }
        foreach ($user2->getEmailSigs() as $sig) {
            $sig->setUser($user1);
        }
        foreach ($user2->getOwnedIssues() as $issue) {
            $issue->setOwner($user1);
        }
        if (!$user1->getPerson() && $user2->getPerson()) {
            $user1->setPerson($user2->getPerson());
        }

        $this->entityManager->remove($user2);
        
        $this->entityManager->flush();

        return $user1;
    }
}
