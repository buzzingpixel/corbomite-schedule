<?php

declare(strict_types=1);

namespace corbomite\schedule\services;

use corbomite\configcollector\Collector;
use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;
use corbomite\schedule\models\ScheduleItemModel;
use DateTime;
use DateTimeZone;
use function array_values;
use function implode;
use function mb_strtolower;
use function md5;

class GetScheduleService
{
    /** @var OrmFactory */
    private $ormFactory;
    /** @var ScheduleItemModel[] */
    private $scheduleConfig;

    public function __construct(
        OrmFactory $ormFactory,
        Collector $configCollector
    ) {
        $this->ormFactory     = $ormFactory;
        $this->scheduleConfig = $this->populateModelDbVals(
            $this->convertConfigToModels(
                $configCollector('scheduleConfigFilePath')
            )
        );
    }

    /**
     * @return ScheduleItemModel[]
     */
    public function __invoke() : array
    {
        return $this->get();
    }

    /**
     * @return ScheduleItemModel[]
     */
    public function get() : array
    {
        return $this->scheduleConfig;
    }

    /**
     * @param mixed[] $scheduleConfig
     *
     * @return ScheduleItemModel[]
     */
    private function convertConfigToModels(array $scheduleConfig) : array
    {
        $models = [];

        foreach ($scheduleConfig as $item) {
            if (! isset($item['class'], $item['runEvery'])) {
                continue;
            }

            $runEvery = mb_strtolower($item['runEvery']);

            if (! isset(ScheduleItemModel::RUN_EVERY_MAP[$runEvery])) {
                continue;
            }

            $model = new ScheduleItemModel();
            $model->class($item['class']);
            $model->method($item['method'] ?? '__invoke');
            $model->runEvery($item['runEvery']);
            $model->guid(md5(implode('-', [
                $model->class(),
                $model->method(),
                $model->runEvery(),
            ])));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param ScheduleItemModel[] $scheduleModels
     *
     * @return ScheduleItemModel[]
     */
    private function populateModelDbVals(array $scheduleModels) : array
    {
        $guids = [];

        /** @var ScheduleItemModel[] $modelsByGuid */
        $modelsByGuid = [];

        foreach ($scheduleModels as $model) {
            $modelsByGuid[$model->guid()] = $model;
            $guids[$model->guid()]        = $model->guid();
        }

        if (! $guids) {
            return [];
        }

        $records = $this->ormFactory->makeOrm()->select(ScheduleTracking::class)
            ->where('guid IN ', array_values($guids))
            ->fetchRecords();

        foreach ($records as $record) {
            $guid  = $record->guid;
            $model = $modelsByGuid[$guid] ?? null;

            if (! $model) {
                continue;
            }

            $model->isRunning((bool) $record->is_running);

            if ($record->last_run_start_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastRunStartAt(new DateTime(
                    $record->last_run_start_at,
                    new DateTimeZone($record->last_run_start_at_time_zone)
                ));
            }

            if ($record->last_run_end_at) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $model->lastRunEndAt(new DateTime(
                    $record->last_run_end_at,
                    new DateTimeZone($record->last_run_end_at_time_zone)
                ));
            }

            $modelsByGuid[$guid] = $model;
        }

        return $modelsByGuid;
    }
}
