<?php

declare(strict_types=1);

namespace corbomite\tests\services\SaveScheduleService;

use corbomite\db\Factory as OrmFactory;
use corbomite\db\Orm;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;
use corbomite\schedule\data\ScheduleTracking\ScheduleTrackingRecord;
use corbomite\schedule\data\ScheduleTracking\ScheduleTrackingSelect;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\SaveScheduleService;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Throwable;

class HasGuidTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $lastRunStartAt = new DateTime('now', new DateTimeZone('America/Chicago'));

        $lastRunStartAtActual = new DateTime('now', new DateTimeZone('UTC'));

        $lastRunEndAt = new DateTime('now', new DateTimeZone('America/Chicago'));

        $lastRunEndAtActual = new DateTime('now', new DateTimeZone('UTC'));

        $record = self::createMock(ScheduleTrackingRecord::class);

        $record->expects(self::at(0))
            ->method('__set')
            ->with(
                self::equalTo('guid'),
                self::equalTo('GuidTestValue')
            );

        $record->expects(self::at(1))
            ->method('__set')
            ->with(
                self::equalTo('is_running'),
                self::equalTo(true)
            );

        $record->expects(self::at(2))
            ->method('__set')
            ->with(
                self::equalTo('last_run_start_at'),
                self::equalTo(null)
            );

        $record->expects(self::at(3))
            ->method('__set')
            ->with(
                self::equalTo('last_run_start_at_time_zone'),
                self::equalTo(null)
            );

        $record->expects(self::at(4))
            ->method('__set')
            ->with(
                self::equalTo('last_run_end_at'),
                self::equalTo(null)
            );

        $record->expects(self::at(5))
            ->method('__set')
            ->with(
                self::equalTo('last_run_end_at_time_zone'),
                self::equalTo(null)
            );

        $record->expects(self::at(6))
            ->method('__set')
            ->with(
                self::equalTo('last_run_start_at'),
                self::equalTo($lastRunStartAtActual->format('Y-m-d H:i:s'))
            );

        $record->expects(self::at(7))
            ->method('__set')
            ->with(
                self::equalTo('last_run_start_at_time_zone'),
                self::equalTo('UTC')
            );

        $record->expects(self::at(8))
            ->method('__set')
            ->with(
                self::equalTo('last_run_end_at'),
                self::equalTo($lastRunEndAtActual->format('Y-m-d H:i:s'))
            );

        $record->expects(self::at(9))
            ->method('__set')
            ->with(
                self::equalTo('last_run_end_at_time_zone'),
                self::equalTo('UTC')
            );

        $model = self::createMock(ScheduleItemModel::class);

        $model->expects(self::exactly(3))
            ->method('guid')
            ->willReturn('GuidTestValue');

        $model->expects(self::once())
            ->method('isRunning')
            ->willReturn(true);

        $model->expects(self::once())
            ->method('lastRunStartAt')
            ->willReturn($lastRunStartAt);

        $model->expects(self::once())
            ->method('lastRunEndAt')
            ->willReturn($lastRunEndAt);

        $scheduleTrackingSelect = self::createMock(ScheduleTrackingSelect::class);

        $scheduleTrackingSelect->expects(self::once())
            ->method('where')
            ->with(
                self::equalTo('guid = '),
                self::equalTo('GuidTestValue')
            )
            ->willReturn($scheduleTrackingSelect);

        $scheduleTrackingSelect->expects(self::once())
            ->method('fetchRecord')
            ->willReturn(null);

        $orm = self::createMock(Orm::class);

        $orm->expects(self::once())
            ->method('select')
            ->with(self::equalTo(ScheduleTracking::class))
            ->willReturn($scheduleTrackingSelect);

        $orm->expects(self::once())
            ->method('newRecord')
            ->with(self::equalTo(ScheduleTracking::class))
            ->willReturn($record);

        $orm->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($record));

        $ormFactory = self::createMock(OrmFactory::class);

        $ormFactory->expects(self::once())
            ->method('makeOrm')
            ->willReturn($orm);

        /** @noinspection PhpParamsInspection */
        $service = new SaveScheduleService($ormFactory);

        /** @noinspection PhpParamsInspection */
        $service($model);
    }
}
