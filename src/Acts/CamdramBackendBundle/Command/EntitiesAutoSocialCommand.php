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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $venues_rep = $em->getRepository('ActsCamdramBundle:Venue');
        foreach ($venues_rep->findAll() as $venue) {

            foreach (array('facebook', 'twitter') as $api) {

                $data = $social->get($api)->doSearch($venue->getName(), 'page');

                var_dump($data[0]['id'], $data[0]['name']);
                if (count($data) > 0) {
                    if ($api == 'facebook') $venue->setFacebookId($data[0]['id']);
                    else $venue->setTwitterId($data[0]['id']);
                    $output->writeln('Added '.ucfirst($api).' page "'.$data[0]['name'].'" for venue '.$venue->getName());
                    $em->flush();
                }
            }
        }
    }

}