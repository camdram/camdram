<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Acts\CamdramBundle\Entity\Show;
use Doctrine\ORM\EntityRepository;

class ShowsFreebaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:shows:freebase')
            ->setDescription('Map shows to freebase plays')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var $repo EntityRepository */
        $repo = $em->getRepository('ActsCamdramBundle:Show');

        $shows = $repo->findBy(array('freebase_id' => null));
        $social = $this->getContainer()->get('acts.social_api.provider');
        $api = $social->get('google_simple');

        $i = 0;

        foreach ($shows as $show) {
            $results = $api->doFreebaseSearch($show->getName(), '(any type:/theater/play type:/theater/opera)', 2);
            if (count($results) > 1 && ($results[0]['score'] > 150 || $results[0]['name'] == $show->getName())) {
                $show->setFreebaseId($results[0]['mid']);
                $output->writeln('Mapped show "'.$show->getName().'" to Freebase play "'
                        .$results[0]['name'].'" ('.$results[0]['mid'].')');

            }
            if ($i % 30 == 0) {
                $em->flush();
            }
            $i++;
        }
        $em->flush();
    }

}