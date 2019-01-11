<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use corbomite\di\Di;
use corbomite\db\Factory as OrmFactory;
use src\app\schedule\services\SaveScheduleService;
use corbomite\schedule\services\GetScheduleService;
use Symfony\Component\Console\Output\ConsoleOutput;
use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\services\ScheduleCollectorService;

return [
    CreateMigrationsAction::class => function () {
        return new CreateMigrationsAction(
            __DIR__ . '/migrations',
            new ConsoleOutput()
        );
    },
    ScheduleCollectorService::class => function () {
        return new ScheduleCollectorService();
    },
    GetScheduleService::class => function () {
        return new GetScheduleService(
            new OrmFactory(),
            Di::get(ScheduleCollectorService::class)
        );
    },
    SaveScheduleService::class => function () {
        return new SaveScheduleService(new OrmFactory());
    },
];
