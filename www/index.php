<?php

$container = require __DIR__ . '/../App/bootstrap.php';

$container->getByType(Nette\Application\Application::class)
	->run();
