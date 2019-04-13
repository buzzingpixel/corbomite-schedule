<?php

declare(strict_types=1);

namespace corbomite\tests;

use corbomite\schedule\actions\CreateMigrationsAction;
use corbomite\schedule\actions\RunScheduleAction;
use PHPUnit\Framework\TestCase;

class ActionConfigTest extends TestCase
{
    public function test() : void
    {
        $config = require TESTING_APP_PATH . '/src/actionConfig.php';

        self::assertEquals(
            [
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
            ],
            $config
        );
    }
}
