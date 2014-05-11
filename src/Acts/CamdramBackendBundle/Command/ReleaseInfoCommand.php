<?php
namespace Acts\CamdramBackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ReleaseInfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('camdram:release-info')
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
        var_dump($data);
        $text = $this->getContainer()->get('twig')->render('ActsCamdramBackendBundle:Email:commit-email.txt.twig', $data);
        $output->write($text);
    }

    private function getCommitData($start, $end)
    {
        chdir($this->getContainer()->getParameter('kernel.root_dir').'/../');
        $commits = `git log $start..$end --format=oneline --date-order --reverse`;
        $lines = explode("\n", $commits);
        $commits = array();
        $client = $this->getContainer()->get('acts.social_api.apis.github');

        foreach ($lines as $line) {
            if (!empty($line)) {
                list($hash, $message) = explode(" ", $line, 2);
                $data = $client->doCommit('camdram', 'camdram', $hash);
                $commits[$hash] = array(
                    'message' => $message,
                    'author' => $data['author']['name'],
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
                    $data = $client->doIssue('camdram', 'camdram', $number);
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
