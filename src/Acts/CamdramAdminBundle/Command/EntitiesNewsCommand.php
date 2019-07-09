<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Abraham\TwitterOAuth\TwitterOAuth as Twitter;

use Acts\CamdramBundle\Entity\News;
use Acts\CamdramBundle\Entity\NewsLink;
use Acts\CamdramBundle\Entity\NewsMention;

/**
 * Class EntitiesNewsCommand
 *
 * A console command to pull in the latest 'news' for societies and venues (i.e. Facebook page updates and tweets).
 * Should be run regularly from a cron job.
 */
class EntitiesNewsCommand extends Command
{

    /**
     * @var Twitter
     */
    private $twitter;

    public function __construct(Twitter $twitter, EntityManagerInterface $entityManager)
    {
        $this->twitter = $twitter;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected static $defaultName = 'camdram:entities:social-news';

    protected function configure()
    {
        $this
            ->setDescription('Automatically pull in news for linked Twitter accounts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executeForTwitter($output);
    }

    private function executeForTwitter(OutputInterface $output)
    {
        $this->twitter->setDecodeJsonAsArray(true);

        $org_repo = $this->entityManager->getRepository('ActsCamdramBundle:Organisation');
        $news_repo = $this->entityManager->getRepository('ActsCamdramBundle:News');
        $entities = $org_repo->findWithService('twitter');
        foreach ($entities as $entity) {
            $response = $this->twitter->get(
                'statuses/user_timeline', [
                    'user_id' => $entity->getTwitterId(),
                    'count' => 50,
                    'trim_user' => true,
                    'include_rts' => false,
                    'tweet_mode' => 'extended',
                    'exclude_replies' => true,
                    'include_rts' => false,
                ]
            );
            if ($this->twitter->getLastHttpCode() == 200) {
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
        $news = new News();
        $news->setEntity($entity);
        $news->setRemoteId($item['id_str']);
        $news->setSource($service_name);
        $news->setPostedAt(new \DateTime($item['created_at']));

        $body = $item['full_text'];
        $replacements = [];

        foreach ($item['entities']['urls'] as $url) {
            $replacements[$url['url']] = '<a href="'.$url['url'].'" target="_blank">'.$url['display_url'].'</a>';
        }

        foreach ($item['entities']['hashtags'] as $hashtag) {
            $hashText = '#'.$hashtag['text'];
            $replacements[$hashText] = '<a href="https://twitter.com/hashtag/'.$hashtag['text'].'" target="_blank">'.$hashText.'</a>';
        }

        foreach ($item['entities']['user_mentions'] as $mention) {
            $atText = '@'.$mention['screen_name'];
            $replacements[$atText] = '<a href="https://twitter.com/'.$mention['screen_name'].'" target="_blank">'.$atText.'</a>';
        }

        if (isset($item['extended_entities'])) {
            foreach ($item['extended_entities']['media'] as $media) {
                if (!$news->getPicture())
                {
                    //Display first linked 'media' in picture field
                    $news->setPicture($media['media_url_https'].':thumb');
                }
                $replacements[$media['url']] = '';
            }
        }

        $news->setBody(nl2br(strtr($body, $replacements)));

        $this->entityManager->persist($news);
        $output->writeln('Created news '.$news->getRemoteId().' for '.$entity->getName().' on '.$service_name);
        $this->entityManager->flush();
    }
}
