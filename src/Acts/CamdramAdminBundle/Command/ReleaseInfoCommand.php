<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Github\Client;

class ReleaseInfoCommand extends Command
{
    /**
     * @var Client
     */
    private $client;

    private $githubId, $githubSecret;

    /**
     * @var Twig
     */
    private $twig;


    public function __construct(Client $client, $githubId, $githubSecret, \Twig_Environment $twig)
    {
        $this->client = $client;
        $this->githubId = $githubId;
        $this->githubSecret = $githubSecret;
        $this->twig = $twig;

        parent::__construct();
    }

    protected static $defaultName = 'camdram:release-info';

    protected function configure()
    {
        $this
            ->setDescription('Gather info about a Camdram release')
            ->addArgument('start', InputArgument::REQUIRED)
            ->addArgument('end', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = $input->getArgument('start');
        $end = $input->getArgument('end');
        $data = $this->getCommitData($start, $end);

        $data['start_tag'] = $start;
        $data['end_tag'] = $end;
        
        $text = $this->twig->render('admin/email/release-email.txt.twig', $data);
        $output->write($text);
    }

    private function getCommitData($start, $end)
    {
        $commits = `git log $start..$end --format=oneline --date-order --reverse`;
        $lines = explode("\n", $commits);
        $commits = array();
        
        $this->client->authenticate(
            $this->githubId,
            $this->githubSecret,
            Client::AUTH_URL_CLIENT_ID
        );

        foreach ($lines as $line) {
            if (!empty($line)) {
                list($hash, $message) = explode(' ', $line, 2);
                $data = $this->client->api('repo')->commits()->show('camdram', 'camdram', $hash);
                $commits[$hash] = array(
                    'message' => $message,
                    'author' => $data['author']['login'],
                    'url' => 'https://github.com/camdram/camdram/commit/'.$hash
                );
            }
        }

        $issues = array();

        foreach ($commits as $hash => $commit_data) {
            if (preg_match('/#([0-9]+)/i', $commit_data['message'], $matches)) {
                $number = $matches[1];
                if (isset($issues[$number])) {
                    $issues[$number]['commits'][$hash] = $commit_data;
                } else {
                    $data = $this->client->api('issue')->show('camdram', 'camdram', $number);
                    $issues[$number] = array(
                        'name' => $data['title'],
                        'state' => $data['state'],
                        'url' => $data['html_url'],
                        'commits' => array(
                            $hash => $commit_data
                        )
                    );
                }
                unset($commits[$hash]);
            }
        }

        return array('issues' => $issues, 'commits' => $commits);
    }
}
