<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule;

use corbomite\di\Di;
use corbomite\schedule\models\ScheduleItemModel;
use src\app\schedule\services\SaveScheduleService;
use corbomite\schedule\services\GetScheduleService;

class ScheduleApi
{
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function getSchedule(): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(GetScheduleService::class);
        return $service();
    }

    public function saveSchedule(ScheduleItemModel $model): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveScheduleService::class);
        $service($model);
    }
}
