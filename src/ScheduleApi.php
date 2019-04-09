<?php

declare(strict_types=1);

namespace corbomite\schedule;

use corbomite\di\Di;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;

class ScheduleApi
{
    /** @var Di */
    private $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    /**
     * @return mixed[]
     */
    public function getSchedule() : array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(GetScheduleService::class);

        return $service();
    }

    public function saveSchedule(ScheduleItemModel $model) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->getFromDefinition(SaveScheduleService::class);
        $service($model);
    }
}
