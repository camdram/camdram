<?php

namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class EntitiesAutoSocialCommand
 *
 * This console command attempt to link venues and societies in the database to Facebook pages and Twitter
 * accounts using their respective search APIs. It isn't very accurate so should be used with caution!
 */
class EntitiesAutoSocialCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
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

        $venues_rep = $em->getRepository('ActsCamdramBundle:Organisation');
        $this->linkEntities($venues_rep->findAll(), $output);
    }

    /**
     * @param array           $entities An array of entities which it should attempt to link to social media accounts
     * @param OutputInterface $output
     */
    private function linkEntities(array $entities, OutputInterface $output)
    {
        $social = $this->getContainer()->get('acts.social_api.provider');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $dialog = $this->getHelperSet()->get('dialog');

        foreach ($entities as $entity) {
            foreach (array('facebook', 'twitter') as $api) {
                if (is_null($entity->getSocialId($api))) {
                    $data = $social->get($api)->doSearch($entity->getName(), 'page');

                    if (count($data) > 0) {
                        similar_text($data[0]['name'], $entity->getName(), $percent);
                        $url = $this->getSocialUrl($api, $data[0]['id']);
                        if ($percent > 70) {
                            $question = 'Would you like to add '.ucfirst($api).' page/account "'.$data[0]['name'].'" ('.$url.') for entity '.$entity->getName().'?';
                            if ($dialog->askConfirmation($output, "<question>$question</question>\n")) {
                                $entity->setSocialId($api, $data[0]['id']);
                            }
                            $em->flush();
                        }
                    }
                }
            }
        }
    }

    private function getSocialUrl($service, $id)
    {
        switch ($service) {
            case 'facebook': return 'http://www.facebook.com/'.$id;
            case 'twitter': return 'http://twitter.com/account/redirect_by_id?id='.$id;
        }
    }
}
