<?php

namespace Acts\CamdramAdminBundle\Service;

use Acts\CamdramBundle\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Acts\CamdramBundle\Form\Type\EntitySearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PeopleMerger
{
    private $entityManager;

    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function createForm()
    {
        return $this->formFactory->createBuilder()
            ->add('search_by', ChoiceType::class, array(
                'choices' => array(
                    'Search for person' => 'person',
                    'Enter slug or URL' => 'slug'
                ),
                'expanded' => true,
                'data' => 'person'
            ))
            ->add('person', EntitySearchType::class, array('other_allowed' => false, 'prefetch' => false,
                'required' => false, 'route' => 'get_people', 'class' => 'Acts\\CamdramBundle\\Entity\\Person'))
            ->add('slug', TextType::class, array('label' => 'Slug or URL', 'required' => false))
            ->add('keep_person', ChoiceType::class, array(
                'label' => 'Keep which name?',
                'expanded' => true,
                'choices' => array(
                    'This name' => 'this',
                    "The other person's name" => 'other'
                ),
                'data' => 'this'
            ))
            ->getForm();
    }

    public function getPersonFromFormData($data)
    {
        $repo = $this->entityManager->getRepository('ActsCamdramBundle:Person');
        if ($data['search_by'] == 'person') {
            return $data['person'];
        } else {
            $slug = $data['slug'];
            if (preg_match('/\\/people\\/([a-z\-_]+)/i', $slug, $matches)) {
                $slug = $matches[1];
            }

            return $repo->findOneBySlug($slug);
        }
    }

    /**
     * @param Person $person1
     * @param Person $person2
     * @param $keepFirst
     *
     * @return Person
     */
    public function mergePeople(Person $person1, Person $person2, $keepFirst)
    {
        if (!$keepFirst) {
            $tempPerson = $person2;
            $person2 = $person1;
            $person1 = $tempPerson;
        }

        foreach ($person2->getRoles() as $role) {
            $role->setPerson($person1);
        }

        $person2->setMappedTo($person1);

        $this->entityManager->flush();

        return $person1;
    }
}
