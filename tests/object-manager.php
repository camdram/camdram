<?php
# Required by PHPStan's Doctrine extension
$loader = require __DIR__.'/../app/autoload.php';
$kernel = new AppKernel('dev', (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
