<?php

// src/Acme/BasicCmsBundle/DataFixtures/PHPCR/LoadPageData.php
namespace Acts\CamdramBundle\DataFixtures\PHPCR;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use PHPCR\Util\NodeHelper;

use Gedmo\Sluggable\Util as Sluggable;

use Acts\CamdramBundle\Entity\Page as Page;
use Acts\CamdramBundle\Document\Page as CmsPage;

class LoadPageData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        if (null === $this->container) {
            $this->container = $this->getApplication()->getKernel()->getContainer();
        }

        return $this->container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * Load data from the old Camdram schema into the PHPCR 
     *
     * Execute by calling php app/console doctrine:phpcr:fixtures:load
     */
    public function load(ObjectManager $dm)
    {
        NodeHelper::createPath($dm->getPhpcrSession(), '/cms/pages');
        $parent = $dm->find(null, '/cms/pages');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        // This magic number is based on inspection of the existing Camdram database.
        $this->add_child_nodes($dm, $em, 119, $parent);
        
    }

    /**
     * Add child nodes
     *
     * Recursively called. Take only the current version of a current page
     * on Camdram. 
     */    
    private function add_child_nodes($dm, $em, $parent_id, $parent)
    {
        $pages = $em->createQuery('SELECT p FROM ActsCamdramBundle:Page p WHERE p.parent_id = :pid AND p.ghost = 0')
            ->setParameter('pid', $parent_id)
            ->getResult();
        foreach ($pages as $page) {
            $cms_page = new CmsPage();
            $title = $page->getFullTitle();
            if ($title != '') {
                $cms_page->setSlug(Sluggable\Urlizer::urlize($title, '-'));
                $cms_page->setTitle($title);
                $cms_page->setParent($parent);
                $cms_page->setContent($page->getHelp());
                $dm->persist($cms_page);
                $dm->flush();
            }            
            // Recurse
            $this->add_child_nodes($dm, $em, $page->getId(), $cms_page);
        }
    }    
}

