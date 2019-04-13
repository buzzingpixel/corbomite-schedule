<?php

declare(strict_types=1);

namespace corbomite\schedule\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;
use corbomite\schedule\models\ScheduleItemModel;
use DateTimeZone;

class SaveScheduleService
{
    /** @var OrmFactory */
    private $ormFactory;

    public function __construct(OrmFactory $atlas)
    {
        $this->ormFactory = $atlas;
    }

    public function __invoke(ScheduleItemModel $model) : void
    {
        $this->save($model);
    }

    public function save(ScheduleItemModel $model) : void
    {
        if (! $model->guid()) {
            return;
        }

        $orm = $this->ormFactory->makeOrm();

        $record = $orm->select(ScheduleTracking::class)
            ->where('guid = ', $model->guid())
            ->fetchRecord();

        if (! $record) {
            $record = $orm->newRecord(ScheduleTracking::class);
        }

        $record->guid       = $model->guid();
        $record->is_running = $model->isRunning();

        $record->last_run_start_at           = null;
        $record->last_run_start_at_time_zone = null;
        $record->last_run_end_at             = null;
        $record->last_run_end_at_time_zone   = null;

        $lastRunStartAt = $model->lastRunStartAt();

        if ($lastRunStartAt) {
            $lastRunStartAt->setTimezone(new DateTimeZone('UTC'));

            $record->last_run_start_at = $lastRunStartAt->format('Y-m-d H:i:s');

            $record->last_run_start_at_time_zone = $lastRunStartAt->getTimezone()->getName();
        }

        $lastRunEndAt = $model->lastRunEndAt();

        if ($lastRunEndAt) {
            $lastRunEndAt->setTimezone(new DateTimeZone('UTC'));

            $record->last_run_end_at = $lastRunEndAt->format('Y-m-d H:i:s');

            $record->last_run_end_at_time_zone = $lastRunEndAt->getTimezone()->getName();
        }

        $orm->persist($record);
    }
}
