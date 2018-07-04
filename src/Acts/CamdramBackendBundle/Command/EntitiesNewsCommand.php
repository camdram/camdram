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
        //$this->executeForFacebook($output);
        $this->executeForTwitter($output);
    }

    private function executeForFacebook(OutputInterface $output)
    {
        /**
         * @var \Facebook\Facebook $facebook
         */
        $facebook = $this->getContainer()->get('facebook.api');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $org_repo = $em->getRepository('ActsCamdramBundle:Organisation');
        $news_repo = $em->getRepository('ActsCamdramBundle:News');
        $entities = $org_repo->findWithService('facebook');
        foreach ($entities as $entity) {
            try {
                $response = $facebook->get('/'.$entity->getFacebookId().'/posts');
                $body = $response->getDecodedBody();
                
                foreach ($body['data'] as $item) {
                    if (!$news_repo->itemExists('facebook', $item['id'])) {
                        $this->addNews('facebook', $item, $entity, $output);
                    }
                }
            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                $output->writeln('Graph returned an error: ' . $e->getMessage());
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                $output->writeln('Facebook SDK returned an error: ' . $e->getMessage());
            }
        }
    }
    
    private function executeForTwitter(OutputInterface $output)
    {
        /**
         *
         * @var \Abraham\TwitterOAuth\TwitterOAuth $twitter
         */
        $twitter = $this->getContainer()->get('twitter.api');
        $twitter->setDecodeJsonAsArray(true);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $org_repo = $em->getRepository('ActsCamdramBundle:Organisation');
        $news_repo = $em->getRepository('ActsCamdramBundle:News');
        $entities = $org_repo->findWithService('twitter');
        foreach ($entities as $entity) {
            $response = $twitter->get(
                'statuses/user_timeline',
                ['user_id' => $entity->getTwitterId(), 'count' => 50,
                    'trim_user' => true, 'include_rts' => false
                ]
            );
            if ($twitter->getLastHttpCode() == 200) {
                foreach ($response as $tweet) {
                    if (!$news_repo->itemExists('twitter', $tweet['id'])) {
                        $this->addNews('twitter', $tweet, $entity, $output);
                    }
                }
            } else {
                $output->writeln('Twitter API error');
            }
        }
    }

    private function addNews($service_name, $item, $entity, OutputInterface $output)
    {
        if (isset($item['application']['name']) && $item['application']['name'] == 'Twitter') {
            //Item is cross-posted from Twitter...no point picking it up twice
            return;
        }
        
        if (isset($item['message']) && !empty($item['message'])) {
            $message = $item['message'];
        } elseif (isset($item['text']) && !empty($item['text'])) {
            $message = $item['text'];
        } else {
            return;
        }

        $news = new News();
        $news->setBody($message);
        $news->setEntity($entity);
        $news->setRemoteId(isset($item['id_str']) ? $item['id_str'] : $item['id']);

        if (isset($item['created_at'])) {
            $news->setPostedAt(new \DateTime($item['created_at']));
        } elseif (isset($item['created_time'])) {
            $news->setPostedAt(new \DateTime($item['created_time']));
        }
        
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
        if (isset($item['message_tags'])) {
            foreach ($item['message_tags'] as $m) {
                if (isset($m[0])) {
                    $m = $m[0];
                }
                $this->addMention($news, $m['name'], $m['id'], $service_name, $m['offset'], $m['length']);
            }
        }
        if (isset($item['entities']['user_mentions'])) {
            foreach ($item['entities']['user_mentions'] as $m) {
                if (isset($m[0])) {
                    $m = $m[0];
                }
                $m['offset'] = $m['indices'][0];
                $m['length'] = $m['indicies'][1] - $m['indicies'][0];
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
