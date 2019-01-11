<?php
declare(strict_types=1);

namespace src\app\schedule\services;

use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;

class SaveScheduleService
{
    private $ormFactory;

    public function __construct(OrmFactory $atlas)
    {
        $this->ormFactory = $atlas;
    }

    public function __invoke(ScheduleItemModel $model): void
    {
        if (! $model->guid()) {
            return;
        }

        $orm = $this->ormFactory->makeOrm();

        $record = $this->ormFactory->makeOrm()->select(ScheduleTracking::class)
            ->where('guid = ', $model->guid())
            ->fetchRecord();

        if (! $record) {
            $record = $orm->newRecord(ScheduleTracking::class);
        }

        $record->guid = $model->guid();
        $record->is_running = $model->isRunning();

        $record->last_run_start_at = null;
        $record->last_run_start_at_time_zone = null;
        $record->last_run_end_at = null;
        $record->last_run_end_at_time_zone = null;

        if ($model->lastRunStartAt()) {
            $record->last_run_start_at = $model->lastRunStartAt()
                ->format('Y-m-d H:i:s');
            $record->last_run_start_at_time_zone = $model->lastRunStartAt()
                ->getTimezone()->getName();
        }

        if ($model->lastRunEndAt()) {
            $record->last_run_end_at = $model->lastRunEndAt()
                ->format('Y-m-d H:i:s');
            $record->last_run_end_at_time_zone = $model->lastRunEndAt()
                ->getTimezone()->getName();
        }

        $orm->persist($record);
    }
}
