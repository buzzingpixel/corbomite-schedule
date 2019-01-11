<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use corbomite\schedule\actions\RunScheduleAction;
use corbomite\schedule\actions\CreateMigrationsAction;

return [
    'schedule' => [
        'description' => 'Corbomite Schedule Commands',
        'commands' => [
            'create-migrations' => [
                'description' => 'Adds migrations to create schedule tables',
                'class' => CreateMigrationsAction::class,
            ],
            'run' => [
                'description' => 'Runs schedule (run on cron every minute)',
                'class' => RunScheduleAction::class,
            ],
        ],
    ],
];
