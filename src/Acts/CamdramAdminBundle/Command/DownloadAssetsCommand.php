<?php

namespace Acts\CamdramAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Psr\Http\Message\StreamInterface;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\RedirectPlugin;

class DownloadAssetsCommand extends Command
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct(HttpClient $httpClient, MessageFactory $messageFactory, Filesystem $fileSystem)
    {
        //Add redriect plugin so that HTTP client follows 30x's
        $this->httpClient = new PluginClient($httpClient, [new RedirectPlugin()]);

        $this->messageFactory = $messageFactory;
        $this->fileSystem = $fileSystem;

        parent::__construct();
    }

    protected static $defaultName = 'camdram:assets:download';
    protected static $defaultDomain = 'https://development.camdram.net';
    protected static $outputDirectory = __DIR__.'/../../../../web/';

    protected function configure(): void
    {
        $this
            ->setDescription('Download latest assets from camdram.net')
            ->addOption('domain', 'd', InputOption::VALUE_REQUIRED, 'Domain to download from', self::$defaultDomain)
            ->addOption('force', 'f', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domain = $input->getOption('domain');
        if (substr($domain, -1) == '/') $domain = substr($domain, 0, -1);

        //Always download manifset
        $output->writeln('<info>Downloading assets from ' . $domain . '</info>');
        $url = $domain.'/build/manifest.json';
        $outputPath = self::$outputDirectory.'/build/manifest.json';
        $output->writeln('Downloading <fg=cyan>'.$url.'</>');
        $stream = $this->downloadFile($url);
        $manifest = json_decode($stream, true);

        //Read existing manifest file
        $existingManifest = [];
        if ($this->fileSystem->exists($outputPath)) {
            $existingManifest = json_decode(file_get_contents($outputPath), true);
        }

        $forceDownload = $input->getOption('force') !== false;

        //Redownload files only if manifest has changed, or --force flag is set
        if ($forceDownload || $manifest != $existingManifest) {
            $this->fileSystem->dumpFile($outputPath, $stream);

            // Alway need the entrypoints and CSP hashes files
            $manifest['build/entrypoints.json'] = '/build/entrypoints.json';
            $manifest['build/csp_hashes.txt'] = '/build/csp_hashes.txt';

            //Check each file in manifest. Only download if doesn't exist
            foreach ($manifest as $path => $realPath) {
                $url = $domain.$realPath;
                $outputPath = self::$outputDirectory.$realPath;

                if ($forceDownload || !$this->fileSystem->exists($outputPath)
                      || $path === 'build/entrypoints.json') {
                    $output->writeln('Downloading <fg=cyan>'.$url.'</>');
                    $stream = $this->downloadFile($url);
                    $this->fileSystem->dumpFile($outputPath, $stream);
                }
                else {
                    $output->writeln('Skipped <fg=blue>'.$url.'</>');
                }
            }

            $this->deleteFiles($manifest, $existingManifest, $output);

            $output->writeln('<info>All assets downloaded</info>');
        }
        else {
            $output->writeln('<info>Assets already to date</info>');
        }
        return 0;
    }

    private function deleteFiles(array $manifest, array $existingManifest, OutputInterface $output): void
    {
        $changedFiles = array_diff($existingManifest, $manifest);

        foreach ($changedFiles as $file) {
            $path = self::$outputDirectory.$file;
            if ($this->fileSystem->exists($path)) {
                $output->writeln('Deleting <fg=yellow>'.$file.'</>');
                $this->fileSystem->remove(self::$outputDirectory.$file);
            }
        }
    }

    private function downloadFile(string $url): string
    {
        $response = $this->httpClient->sendRequest(
            $this->messageFactory->createRequest('GET', $url)
        );

        if ($response->getStatusCode() != 200)
        {
            throw new \RuntimeException('Could not download ' . $url);
        }

        return $response->getBody();
    }

}
