<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\UserIdentity;

class VenuesLocationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:venues:location')
            ->setDescription('Automatically locate venues')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $social = $this->getContainer()->get('acts.social_api.provider');
        $api = $social->get('google_simple');


        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('ActsCamdramBundle:Venue');
        $venues = $repo->findByLatitude(null);
        foreach ($venues as $venue) {
            $results = $api->doPlaceSearch($venue->getName(), '52.20531,0.12179', 5000);
            if (count($results) > 0) {
                $venue->setLatitude($results[0]['geometry']['location']['lat']);
                $venue->setLongitude($results[0]['geometry']['location']['lng']);
                $venue->setAddress($results[0]['vicinity']);
                $output->writeln('Located '.$venue->getName().' at '.$venue->getAddress().': '.$venue->getLatitude().','.$venue->getLongitude());
                $em->flush();
            }
        }
    }

}