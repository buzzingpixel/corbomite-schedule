<?php

declare(strict_types=1);

namespace corbomite\schedule\models;

use DateTime;
use function is_numeric;
use function mb_strtolower;

class ScheduleItemModel
{
    public const RUN_EVERY_MAP = [
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
    ];

    public const MIDNIGHT_STRINGS = [
        'dayatmidnight',
        'saturdayatmidnight',
        'sundayatmidnight',
        'mondayatmidnight',
        'tuesdayatmidnight',
        'wednesdayatmidnight',
        'thursdayatmidnight',
        'fridayatmidnight',
    ];

    /** @var string */
    private $class = '';

    public function class(?string $class = null) : string
    {
        return $this->class = $class ?? $this->class;
    }

    /** @var string */
    private $method = '__invoke';

    public function method(?string $method = null) : string
    {
        return $this->method = $method ?? $this->method;
    }

    /** @var string */
    private $runEvery = 'Always';

    public function runEvery(?string $runEvery = null) : string
    {
        return $this->runEvery = $runEvery ?? $this->runEvery;
    }

    /** @var string */
    private $guid = '';

    public function guid(?string $guid = null) : string
    {
        return $this->guid = $guid ?? $this->guid;
    }

    /** @var bool */
    private $isRunning = false;

    public function isRunning(?bool $isRunning = null) : bool
    {
        return $this->isRunning = $isRunning ?? $this->isRunning;
    }

    /** @var DateTime|null */
    private $lastRunStartAt;

    public function lastRunStartAt(?DateTime $lastRunStartAt = null) : ?DateTime
    {
        return $this->lastRunStartAt = $lastRunStartAt ?? $this->lastRunStartAt;
    }

    /** @var DateTime|null */
    private $lastRunEndAt;

    public function lastRunEndAt(?DateTime $lastRunEndAt = null) : ?DateTime
    {
        return $this->lastRunEndAt = $lastRunEndAt ?? $this->lastRunEndAt;
    }

    /**
     * Translates run every into actionable values.
     * - If the value of runEvery is numeric, it is assumed to be minutes and
     * will be converted to seconds
     * - Else if the runEvery value is not set on the RUN_EVERY_MAP, a 0 will be
     * returned (same value as always)
     * - Else if the runEvery mapped value is numeric, it is minutes and will be
     * converted to seconds and returned
     * - Else the mapped value will be returned
     *
     * @return mixed
     */
    public function getTranslatedRunEvery()
    {
        $val = $this->runEvery;

        if (is_numeric($val)) {
            return ((int) $val) * 60;
        }

        $val = mb_strtolower($val);

        if (! isset(self::RUN_EVERY_MAP[$val])) {
            return 0;
        }

        $mappedVal = self::RUN_EVERY_MAP[$val];

        if (is_numeric($mappedVal)) {
            $mappedVal = (int) $mappedVal;

            return $mappedVal * 60;
        }

        return $mappedVal;
    }

    public function shouldRun() : bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $currentTime = new DateTime();

        $currentTimeStamp = $currentTime->getTimestamp();

        $lastRunTimeStamp = $this->lastRunStartAt() ?
            $this->lastRunStartAt()->getTimestamp():
            0;

        $oneHourInSeconds = 60 * 60;

        $secondsSinceLastRun = $currentTimeStamp - $lastRunTimeStamp;

        $runEvery = $this->getTranslatedRunEvery();

        // If the task is running, wait on hour before trying again
        if ($secondsSinceLastRun < $oneHourInSeconds && $this->isRunning) {
            return false;
        }

        // If $runEvery is numeric we'll check if it's time to run based on that
        if (is_numeric($runEvery)) {
            $runEvery = (int) $runEvery;

            return $secondsSinceLastRun >= $runEvery;
        }

        /**
         * Now we know it's a midnight string and we're checking for that
         */

        // Increment timestamp by 20 hours
        $incrementTime = $lastRunTimeStamp + 72000;

        /**
         * Don't run if it hasn't been more than 20 hours (we're trying to
         * hit the right window, but we can't be too precise because what if
         * the cron doesn't run right at midnight. But we also only want to
         * run this once)
         */
        if ($incrementTime > $currentTimeStamp) {
            return false;
        }

        // If the hour is not in the midnight range, we know we can stop
        if ($currentTime->format('H') !== '00') {
            return false;
        }

        // Now if we're running every day, we know it's time to run
        if ($runEvery === 'dayatmidnight') {
            return true;
        }

        $day = $currentTime->format('l');

        // If we're running on Saturday, and it's Saturday, we should run
        if ($runEvery === 'saturdayatmidnight' && $day === 'Saturday') {
            return true;
        }

        // If we're running on Sunday, and it's Sunday, we should run
        if ($runEvery === 'sundayatmidnight' && $day === 'Sunday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'mondayatmidnight' && $day === 'Monday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'tuesdayatmidnight' && $day === 'Tuesday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'wednesdayatmidnight' && $day === 'Wednesday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        if ($runEvery === 'thursdayatmidnight' && $day === 'Thursday') {
            return true;
        }

        // If we're running on Monda, and it's Monday, we should run
        return $runEvery === 'fridayatmidnight' && $day === 'Friday';
    }
}
