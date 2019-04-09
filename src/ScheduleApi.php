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
     * @return ScheduleItemModel[]
     */
    public function getSchedule() : array
    {
        return $this->di->get(GetScheduleService::class)->get();
    }

    public function saveSchedule(ScheduleItemModel $model) : void
    {
        $this->di->get(SaveScheduleService::class)->save($model);
    }
}
