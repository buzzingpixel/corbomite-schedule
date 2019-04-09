<?php

declare(strict_types=1);

use corbomite\configcollector\Collector;
use corbomite\db\Factory as OrmFactory;
use corbomite\di\Di;
use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\ScheduleApi;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;
use Symfony\Component\Console\Output\ConsoleOutput;

return [
    CreateMigrationsAction::class => static function () {
        return new CreateMigrationsAction(
            __DIR__ . '/migrations',
            new ConsoleOutput()
        );
    },
    GetScheduleService::class => static function () {
        return new GetScheduleService(
            new OrmFactory(),
            Di::get(Collector::class)
        );
    },
    RunScheduleAction::class => static function () {
        return new RunScheduleAction(new Di(), new ConsoleOutput());
    },
    SaveScheduleService::class => static function () {
        return new SaveScheduleService(new OrmFactory());
    },
    ScheduleApi::class => static function () {
        return new ScheduleApi(new Di());
    },
];
