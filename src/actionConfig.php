<?php

declare(strict_types=1);

use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\actions\RunScheduleAction;

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
