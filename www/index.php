<?php

require __DIR__ . '/../vendor/autoload.php';

\App\Bootstrap\Bootstrap::boot()
    ->createContainer()
    ->getByType(Nette\Application\Application::class)
    ->run();

//$container = require __DIR__ . '/../App/Bootstrap/bootstrap.php';
//
//$container->getByType(Nette\Application\Application::class)
//	->run();