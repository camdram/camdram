<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\UserIdentity;

class EntitiesAutoSocialCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:entities:auto-social')
            ->setDescription('Automatically link entities to Facebook pages / Twitter accounts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $social = $this->getContainer()->get('acts.social_api.provider');
        $social->get('facebook')->authenticateAsSelf();
        $social->get('twitter')->authenticateAsSelf();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $venues_rep = $em->getRepository('ActsCamdramBundle:Venue');
        $this->linkEntities($venues_rep->findAll(), $output);

        $societies_rep = $em->getRepository('ActsCamdramBundle:Society');
        $this->linkEntities($societies_rep->findAll(), $output);
    }

    private function linkEntities(array $entities, OutputInterface $output)
    {
        $social = $this->getContainer()->get('acts.social_api.provider');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        foreach ($entities as $entity) {
            foreach (array('facebook', 'twitter') as $api) {
                if (is_null($entity->getSocialId($api))) {
                    $data = $social->get($api)->doSearch($entity->getName(), 'page');

                    if (count($data) > 0) {
                        similar_text($data[0]['name'], $entity->getName(), $percent);
                        if ($percent > 70) {
                            $entity->setSocialId($api, $data[0]['id']);
                            $output->writeln('Added '.ucfirst($api).' page/account "'.$data[0]['name'].'" for entity '.$entity->getName());
                            $em->flush();
                        }
                    }
                }

            }
        }
    }
}