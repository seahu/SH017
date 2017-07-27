<?php

require __DIR__ . '/../vendor/autoload.php'; //toto je puvodni nastaveni 

$configurator = new Nette\Configurator;

$configurator->setDebugMode('10.10.107.120'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');


$container = $configurator->createContainer();


return $container;
