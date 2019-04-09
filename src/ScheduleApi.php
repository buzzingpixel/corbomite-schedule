<?php

declare(strict_types=1);

namespace corbomite\schedule;

use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\GetScheduleService;
use corbomite\schedule\services\SaveScheduleService;
use Psr\Container\ContainerInterface;

class ScheduleApi
{
    /** @var ContainerInterface */
    private $di;

    public function __construct(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @return mixed[]
     */
    public function getSchedule() : array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->get(GetScheduleService::class);

        return $service();
    }

    public function saveSchedule(ScheduleItemModel $model) : void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $service = $this->di->get(SaveScheduleService::class);
        $service($model);
    }
}
