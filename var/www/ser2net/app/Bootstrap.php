<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
        public static function boot(): Configurator
        {
                $configurator = new Configurator;
                $appDir = dirname(__DIR__);

                $configurator->setDebugMode(true);
                //$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
                //$configurator->setDebugMode('10.10.109.114'); // enable for your remote IP
                $configurator->enableTracy($appDir . '/log');

                $configurator->setTimeZone('Europe/Prague');
                $configurator->setTempDirectory($appDir . '/temp');

                $configurator->createRobotLoader()
                        ->addDirectory(__DIR__)
                        ->register();
                $configurator->addConfig($appDir . '/app/config/config.local.neon');
                $configurator->addConfig($appDir . '/app/config/config.neon');

                return $configurator;
        }
}
