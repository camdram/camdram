<?php

namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Acts\CamdramBundle\Entity\News;
use Acts\CamdramBundle\Entity\NewsLink;
use Acts\CamdramBundle\Entity\NewsMention;

/**
 * Class EntitiesNewsCommand
 *
 * A console command to pull in the latest 'news' for societies and venues (i.e. Facebook page updates and tweets).
 * Should be run regularly from a cron job.
 */
class EntitiesNewsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:entities:social-news')
            ->setDescription('Automatically pull in news for linked Facebook / Twitter accounts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executeForService('twitter', $output);
        $this->executeForService('facebook', $output);
    }

    private function executeForService($service_name, OutputInterface $output)
    {
        $api = $this->getContainer()->get('acts.social_api.provider')->get($service_name);
        $api->authenticateAsSelf();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $org_repo = $em->getRepository('ActsCamdramBundle:Organisation');
        $news_repo = $em->getRepository('ActsCamdramBundle:News');
        $entities = $org_repo->findWithService($service_name);
        foreach ($entities as $entity) {
            $news = $api->doPosts($entity->getSocialId($service_name));
            foreach ($news as $item) {
                if (!$news_repo->itemExists($service_name, $item['id'])) {
                    $this->addNews($service_name, $item, $entity, $output);
                }
            }
        }
    }

    private function addNews($service_name, $item, $entity, OutputInterface $output)
    {
        if (!isset($item['text']) || empty($item['text'])) {
            return;
        }

        if (isset($item['application']['name']) && $item['application']['name'] == 'Twitter') {
            //Item is cross-posted from Twitter...no point picking it up twice
            return;
        }

        $news = new News();
        $news->setBody(htmlspecialchars_decode($item['text']));
        $news->setEntity($entity);
        $news->setRemoteId($item['id']);

        $news->setPostedAt(new \DateTime($item['created_at']));
        $news->setSource($service_name);
        $news->setPublic(true);

        if (isset($item['picture'])) {
            $news->setPicture($item['picture']);
        }

        if (isset($item['link'])) {
            if (isset($item['source'])) {
                $type = $item['type'] == 'video' ? 'video' : null;
                $this->addLink($news, $item['link'], $item['name'], $item['description'], $item['picture'], $item['source'], $type);
            } else {
                $this->addLink($news, $item['link'], $item['name'], $item['description'], $item['picture']);
            }
        }

        if (isset($item['entities']['urls'])) {
            foreach ($item['entities']['urls'] as $url) {
                $this->addLink($news, $url['url'], $url['display_url']);
            }
        }
        if (isset($item['mentions'])) {
            foreach ($item['mentions'] as $m) {
                if (isset($m[0])) {
                    $m = $m[0];
                }
                if ($service_name == 'twitter') {
                    $m['offset'] = $m['indices'][0];
                    $m['length'] = $m['indicies'][1] - $m['indicies'][0];
                }
                $this->addMention($news, $m['name'], $m['id'], $service_name, $m['offset'], $m['length']);
            }
        }

        if (isset($item['likes']['count'])) {
            $news->setNumLikes($item['likes']['count']);
        }
        if (isset($item['comments']['count'])) {
            $news->setNumComments($item['comments']['count']);
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($news);
        $output->writeln('Created news '.$news->getRemoteId().' for '.$entity->getName().' on '.$service_name);
        $em->flush();
    }

    private function addLink(News $news, $url, $name, $description = null, $picture = null, $source = null, $media_type = null)
    {
        $link = new NewsLink();
        $link->setLink($url)
            ->setName($name)
            ->setDescription(htmlspecialchars_decode($description))
            ->setPicture($picture)
            ->setMediaType($media_type)
            ->setSource($source)
            ->setNews($news);
        $news->addLink($link);
    }

    private function addMention(News $news, $name, $id, $service_name, $offset, $length)
    {
        $m = new NewsMention();
        $m->setName($name)
            ->setName($name)
            ->setRemoteId($id)
            ->setService($service_name)
            ->setOffset($offset)
            ->setLength($length)
            ->setNews($news);
        $news->addMention($m);
    }
}
