<?php

declare(strict_types=1);

namespace corbomite\tests\services\GetScheduleServiceTest;

use corbomite\configcollector\Collector;
use corbomite\db\Factory;
use corbomite\db\Orm;
use corbomite\schedule\data\ScheduleTracking\ScheduleTracking;
use corbomite\schedule\data\ScheduleTracking\ScheduleTrackingRecord;
use corbomite\schedule\data\ScheduleTracking\ScheduleTrackingSelect;
use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\GetScheduleService;
use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Throwable;

class DbHasGuidsTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test() : void
    {
        $guid = '0b1f47f2d615593ade9d0c96a46dc592';

        $record1 = self::createMock(ScheduleTrackingRecord::class);

        $record1->expects(self::once())
            ->method('__get')
            ->with(self::equalTo('guid'))
            ->willReturn('FooBar');

        $record2 = self::createMock(ScheduleTrackingRecord::class);

        $record2->expects(self::at(0))
            ->method('__get')
            ->with(self::equalTo('guid'))
            ->willReturn($guid);

        $record2->expects(self::at(1))
            ->method('__get')
            ->with(self::equalTo('is_running'))
            ->willReturn(1);

        $lastRunStartAt = new DateTime('+ 20 years', new DateTimeZone('America/Chicago'));

        $record2->expects(self::at(2))
            ->method('__get')
            ->with(self::equalTo('last_run_start_at'))
            ->willReturn($lastRunStartAt->format('Y-m-d H:i:s'));

        $record2->expects(self::at(3))
            ->method('__get')
            ->with(self::equalTo('last_run_start_at'))
            ->willReturn($lastRunStartAt->format('Y-m-d H:i:s'));

        $record2->expects(self::at(4))
            ->method('__get')
            ->with(self::equalTo('last_run_start_at_time_zone'))
            ->willReturn($lastRunStartAt->getTimezone()->getName());

        $lastRunEndAt = new DateTime('+ 40 years', new DateTimeZone('America/Belem'));

        $record2->expects(self::at(5))
            ->method('__get')
            ->with(self::equalTo('last_run_end_at'))
            ->willReturn($lastRunEndAt->format('Y-m-d H:i:s'));

        $record2->expects(self::at(6))
            ->method('__get')
            ->with(self::equalTo('last_run_end_at'))
            ->willReturn($lastRunEndAt->format('Y-m-d H:i:s'));

        $record2->expects(self::at(7))
            ->method('__get')
            ->with(self::equalTo('last_run_end_at_time_zone'))
            ->willReturn($lastRunEndAt->getTimezone()->getName());

        $scheduleTrackingSelect = self::createMock(ScheduleTrackingSelect::class);

        $scheduleTrackingSelect->expects(self::once())
            ->method('where')
            ->with(
                self::equalTo('guid IN '),
                self::equalTo([$guid])
            )
            ->willReturn($scheduleTrackingSelect);

        $scheduleTrackingSelect->expects(self::once())
            ->method('fetchRecords')
            ->willReturn([
                $record1,
                $record2,
            ]);

        $orm = self::createMock(Orm::class);

        $orm->expects(self::once())
            ->method('select')
            ->with(self::equalTo(ScheduleTracking::class))
            ->willReturn($scheduleTrackingSelect);

        $ormFactory = self::createMock(Factory::class);

        $ormFactory->expects(self::once())
            ->method('makeOrm')
            ->willReturn($orm);

        $configCollector = self::createMock(Collector::class);

        $configCollector->expects(self::once())
            ->method('__invoke')
            ->with(self::equalTo('scheduleConfigFilePath'))
            ->willReturn([
                [
                    'foo' => 'bar',
                    'method' => 'baz',
                ],
                [
                    'foo' => 'bar',
                    'method' => 'baz',
                    'class' => 'asdf',
                    'runEvery' => 'foo',
                ],
                [
                    'method' => 'foo',
                    'runEvery' => 'Day',
                ],
                [
                    'class' => 'TestClass',
                    'method' => 'foo',
                    'runEvery' => 'Day',
                ],
            ]);

        /** @noinspection PhpParamsInspection */
        $service = new GetScheduleService($ormFactory, $configCollector);

        $result = $service();

        self::assertCount(1, $result);

        foreach ($result as $key => $model) {
            self::assertEquals($guid, $key);

            self::assertInstanceOf(ScheduleItemModel::class, $model);

            self::assertEquals('TestClass', $model->class());

            self::assertEquals('foo', $model->method());

            self::assertEquals('Day', $model->runEvery());

            self::assertEquals($guid, $model->guid());

            self::assertTrue($model->isRunning());

            self::assertEquals(
                $lastRunStartAt->format('Y-m-d H:i:s'),
                $model->lastRunStartAt()->format('Y-m-d H:i:s')
            );

            self::assertEquals(
                $lastRunStartAt->getTimezone()->getName(),
                $model->lastRunStartAt()->getTimezone()->getName()
            );

            self::assertEquals(
                $lastRunEndAt->format('Y-m-d H:i:s'),
                $model->lastRunEndAt()->format('Y-m-d H:i:s')
            );

            self::assertEquals(
                $lastRunEndAt->getTimezone()->getName(),
                $model->lastRunEndAt()->getTimezone()->getName()
            );
        }
    }
}
