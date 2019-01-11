<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use corbomite\di\Di;
use corbomite\schedule\ScheduleApi;
use corbomite\db\Factory as OrmFactory;
use corbomite\configcollector\Collector;
use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;
use Symfony\Component\Console\Output\ConsoleOutput;
use corbomite\schedule\actions\CreateMigrationsAction;

return [
    CreateMigrationsAction::class => function () {
        return new CreateMigrationsAction(
            __DIR__ . '/migrations',
            new ConsoleOutput()
        );
    },
    RunScheduleAction::class => function () {
        return new RunScheduleAction(new Di(), new ConsoleOutput());
    },
    GetScheduleService::class => function () {
        return new GetScheduleService(
            new OrmFactory(),
            Di::get(Collector::class)
        );
    },
    SaveScheduleService::class => function () {
        return new SaveScheduleService(new OrmFactory());
    },
    ScheduleApi::class => function () {
        return new ScheduleApi(new Di());
    },
];
