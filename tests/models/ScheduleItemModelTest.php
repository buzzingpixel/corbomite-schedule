<?php

declare(strict_types=1);

namespace corbomite\tests\models;

use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\services\GetCurrentDateTimeService;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Throwable;

class ScheduleItemModelTest extends TestCase
{
    public function testRunEveryMap() : void
    {
        self::assertEquals(
            [
                'always' => 0,
                'fiveminutes' => 5,
                'tenminutes' => 10,
                'thirtyminutes' => 30,
                'hour' => 60,
                'day' => 1440,
                'week' => 10080,
                'month' => 43800,
                'dayatmidnight' => 'dayatmidnight',
                'saturdayatmidnight' => 'saturdayatmidnight',
                'sundayatmidnight' => 'sundayatmidnight',
                'mondayatmidnight' => 'mondayatmidnight',
                'tuesdayatmidnight' => 'tuesdayatmidnight',
                'wednesdayatmidnight' => 'wednesdayatmidnight',
                'thursdayatmidnight' => 'thursdayatmidnight',
                'fridayatmidnight' => 'fridayatmidnight',
            ],
            ScheduleItemModel::RUN_EVERY_MAP
        );
    }

    public function testMidnightStrings() : void
    {
        self::assertEquals(
            [
                'dayatmidnight',
                'saturdayatmidnight',
                'sundayatmidnight',
                'mondayatmidnight',
                'tuesdayatmidnight',
                'wednesdayatmidnight',
                'thursdayatmidnight',
                'fridayatmidnight',
            ],
            ScheduleItemModel::MIDNIGHT_STRINGS
        );
    }

    public function testClass() : void
    {
        self::assertEquals(
            '',
            (new ScheduleItemModel())->class()
        );

        $model = new ScheduleItemModel();

        self::assertEquals(
            'baz',
            $model->class('baz')
        );

        self::assertEquals(
            'baz',
            $model->class()
        );
    }

    public function testMethod() : void
    {
        self::assertEquals(
            '__invoke',
            (new ScheduleItemModel())->method()
        );

        $model = new ScheduleItemModel();

        self::assertEquals(
            'baz',
            $model->method('baz')
        );

        self::assertEquals(
            'baz',
            $model->method()
        );
    }

    public function testRunEvery() : void
    {
        self::assertEquals(
            'Always',
            (new ScheduleItemModel())->runEvery()
        );

        $model = new ScheduleItemModel();

        self::assertEquals(
            'baz',
            $model->runEvery('baz')
        );

        self::assertEquals(
            'baz',
            $model->runEvery()
        );
    }

    public function testGuid() : void
    {
        self::assertEquals(
            '',
            (new ScheduleItemModel())->guid()
        );

        $model = new ScheduleItemModel();

        self::assertEquals(
            'baz',
            $model->guid('baz')
        );

        self::assertEquals(
            'baz',
            $model->guid()
        );
    }

    public function testIsRunning() : void
    {
        self::assertFalse((new ScheduleItemModel())->isRunning());

        $model = new ScheduleItemModel();

        self::assertTrue($model->isRunning(true));

        self::assertTrue($model->isRunning());
    }

    /**
     * @throws Throwable
     */
    public function testLastRunStartTime() : void
    {
        self::assertNull((new ScheduleItemModel())->lastRunStartAt());

        $dateTime = new DateTime('+20 years', new DateTimeZone('US/Eastern'));

        $model = new ScheduleItemModel();

        self::assertSame(
            $dateTime->getTimestamp(),
            $model->lastRunStartAt($dateTime)->getTimestamp()
        );

        self::assertEquals(
            $dateTime->getTimestamp(),
            $model->lastRunStartAt()->getTimestamp()
        );

        self::assertSame(
            (new DateTime())->getTimezone()->getName(),
            $model->lastRunStartAt()->getTimezone()->getName()
        );
    }

    public function testRunEveryNumeric() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('60');

        self::assertEquals(3600, $model->getTranslatedRunEvery());
    }

    public function testRunEveryNotSet() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('Foo');

        self::assertEquals(0, $model->getTranslatedRunEvery());
    }

    public function testRunEveryAlways() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('Always');

        self::assertEquals(0, $model->getTranslatedRunEvery());
    }

    public function testRunEveryDayAtMidnight() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('DayAtMidnight');

        self::assertEquals('dayatmidnight', $model->getTranslatedRunEvery());
    }

    public function testShouldRunFalse() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('DayAtMidnight');

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldRunNumericFalse() : void
    {
        $model = new ScheduleItemModel();

        $model->isRunning(true);

        $model->runEvery('Always');

        $model->lastRunStartAt(new DateTimeImmutable('now'));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldRunNumeric() : void
    {
        $model = new ScheduleItemModel();

        $model->runEvery('Always');

        $model->lastRunStartAt(new DateTimeImmutable('-20 years'));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightIncrementOutofHourBounds() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-02 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('DayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-02 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightEveryDay() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-02 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('DayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightSaturday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-08 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('SaturdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotSaturday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-09 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('SaturdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightSunday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-09 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('SundayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotSunday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-10 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('SundayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightMonday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-10 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('MondayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotMonday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-11 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('MondayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightTuesday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-11 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('TuesdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotTuesday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-12 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('TuesdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightWednesday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-12 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('WednesdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotWednesday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-13 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('WednesdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightThursday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-13 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('ThursdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotThursday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-14 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('ThursdayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightFriday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-14 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('FridayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertTrue($model->shouldRun());
    }

    /**
     * @throws Throwable
     */
    public function testShouldMidnightNotFriday() : void
    {
        $midNight = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-15 12:01:00 am'
        );

        $getCurrentDateTime = self::createMock(GetCurrentDateTimeService::class);

        $getCurrentDateTime->expects(self::once())
            ->method('get')
            ->willReturn($midNight);

        /** @noinspection PhpParamsInspection */
        $model = new ScheduleItemModel($getCurrentDateTime);

        $model->runEvery('FridayAtMidnight');

        $model->lastRunStartAt(DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s a',
            '2000-01-01 12:01:00 am'
        ));

        self::assertFalse($model->shouldRun());
    }
}
