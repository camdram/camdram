<?php
require(__DIR__.'/../app/autoload.php');

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

//Create schema if the SQLite DB doesn't already exist
$testDbPath = __DIR__.'/../app/cache/test/test.db';

if (!file_exists($testDbPath) || filesize($testDbPath) == 0) {

    echo "Generating test DB schema...\n";

    //Find the PHP executable
    $phpFinder = new PhpExecutableFinder();
    if (!$phpPath = $phpFinder->find()) {
        throw new \RuntimeException('The php executable could not be found');
    }

    $process = new Process([$phpPath, 'app/console', 'doctrine:schema:create']);
    $process->run();
    if (!$process->isSuccessful()) {
        echo "STDOUT:\n";
        echo $process->getOutput();
        echo "\nSTDERR:\n";
        echo $process->getErrorOutput();
        echo "\n";
        throw new \RuntimeException('An error occurred generating the test DB schema');
    }
}

// Parallelized
if (getenv('TEST_TOKEN') !== false) {
    $tempfile = tempnam('/dev/shm', 'sql');
    copy($testDbPath, $tempfile);
    register_shutdown_function(function() use ($tempfile) {
        unlink($tempfile);
    });
    putenv("DATABASE_URL=sqlite:////$tempfile");
}
