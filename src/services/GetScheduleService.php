<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\services;

use DateTime;
use DateTimeZone;
use corbomite\db\Factory as OrmFactory;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;

class GetScheduleService
{
    private $ormFactory;
    private $scheduleConfig;

    public function __construct(
        OrmFactory $ormFactory,
        ScheduleCollectorService $scheduleConfigCollector
    ) {
        $this->ormFactory = $ormFactory;
        $this->scheduleConfig = $this->populateModelDbVals(
            $this->convertConfigToModels($scheduleConfigCollector())
        );
    }

    /**
     * @return ScheduleItemModel[]
     */
    public function __invoke(): array
    {
        return $this->scheduleConfig;
    }

    /**
     * @return ScheduleItemModel[]
     */
    private function convertConfigToModels(array $scheduleConfig): array
    {
        $models = [];

        foreach ($scheduleConfig as $item) {
            if (! isset($item['class'], $item['runEvery'])) {
                continue;
            }

            $runEvery = strtolower($item['runEvery']);

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
                $model->runEvery()
            ])));

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param ScheduleItemModel[] $scheduleModels
     * @return ScheduleItemModel[]
     */
    private function populateModelDbVals(array $scheduleModels): array
    {
        $guids = [];

        /** @var ScheduleItemModel[] $modelsByGuid */
        $modelsByGuid = [];

        foreach ($scheduleModels as $model) {
            $modelsByGuid[$model->guid()] = $model;
            $guids[$model->guid()] = $model->guid();
        }

        if (! $guids) {
            return [];
        }

        $records = $this->ormFactory->makeOrm()->select(ScheduleTracking::class)
            ->where('guid IN ', array_values($guids))
            ->fetchRecords();

        foreach ($records as $record) {
            $guid = $record->guid;
            $model = $modelsByGuid[$guid];

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
