<?php
namespace Acts\CamdramAdminBundle\Service;


use Acts\CamdramBundle\Entity\Person;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;

class PeopleMerger
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
            ->add('search_by', 'choice', array(
                'choices' => array(
                    'person' => 'Search for person',
                    'slug' => 'Enter slug or URL'
                ),
                'expanded' => true,
                'data' => 'person'
            ))
            ->add('person', 'entity_search', array('other_allowed' => false, 'prefetch' => false,
                'required' => false, 'route' => 'get_people', 'class' => 'Acts\\CamdramBundle\\Entity\\Person'))
            ->add('slug', 'text', array('label' => 'Slug or URL', 'required' => false))
            ->add('keep_person', 'choice', array(
                'label' => 'Keep which name?',
                'expanded' => true,
                'choices' => array(
                    'this' => "This name",
                    'other' => "The other person's name"
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