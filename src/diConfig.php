<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use corbomite\configcollector\Collector;
use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\PhpCalls;
use corbomite\schedule\ScheduleApi;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;

return [
    CreateMigrationsAction::class => static function () {
        $appBasePath = null;

        if (defined('APP_BASE_PATH')) {
            $appBasePath = APP_BASE_PATH;
        }

        if (! $appBasePath) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $reflection = new ReflectionClass(ClassLoader::class);

            $appBasePath = dirname($reflection->getFileName(), 3);
        }

        return new CreateMigrationsAction(
            __DIR__ . '/migrations',
            new ConsoleOutput(),
            $appBasePath,
            new Filesystem(),
            new PhpCalls()
        );
    },
    GetScheduleService::class => static function (ContainerInterface $di) {
        return new GetScheduleService(
            new OrmFactory(),
            $di->get(Collector::class)
        );
    },
    RunScheduleAction::class => static function (ContainerInterface $di) {
        return new RunScheduleAction(
            $di,
            new ConsoleOutput()
        );
    },
    SaveScheduleService::class => static function () {
        return new SaveScheduleService(new OrmFactory());
    },
    ScheduleApi::class => static function (ContainerInterface $di) {
        return new ScheduleApi($di);
    },
];
