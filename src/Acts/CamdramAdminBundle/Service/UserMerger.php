<?php

namespace Acts\CamdramAdminBundle\Service;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramSecurityBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserMerger
{
    private $entityManager;

    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function createForm(bool $setDefault = false)
    {
        return $this->formFactory->createBuilder()
            ->add('email', TextType::class, ['label' => "Other user's email", 'required' => true])
            ->add('keep_user', ChoiceType::class, [
                'label' => 'Keep which user?',
                'expanded' => true,
                'choices' => [
                    'This user' => 'this',
                    'The other user' => 'other'
                ]
            // Setting a 'data' attribute overrides any user-submitted choice
            // (defeating the point of the field) so have to set it only for
            // the form that is sent to the user.
            ] + ($setDefault ? ['data' => 'this'] : []) )
            ->setMethod('PATCH')
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
        
        $metadata = $this->entityManager->getClassMetadata('ActsCamdramSecurityBundle:User');
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($metadata->getAssociationMappings() as $mapping) {
            $fieldName = $mapping['fieldName'];
            
            if ($accessor->isReadable($user2, $fieldName)
                && $accessor->isWritable($user1, $fieldName)) {
                $user2Value = $accessor->getValue($user2, $fieldName);
                if ($user2Value instanceof \Traversable) {
                    //1-to-many mapping
                    $mappedBy = $mapping['mappedBy'];
                    foreach ($user2Value as $user2Obj) {
                        $accessor->setValue($user2Obj, $mappedBy, $user1);
                    }
                } else {
                    //1-to-1 mapping. Only set on merged object if not already set
                    $user1Value = $accessor->getValue($user1, $fieldName);
                    if (is_null($user1Value) && !is_null($user2Value)) {
                        $accessor->setValue($user1, $fieldName, $user2Value);
                    }
                }
            }
        }
        
        $this->entityManager->remove($user2);
        
        $this->entityManager->flush();

        return $user1;
    }
}
