<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\data\ScheduleTracking;

use Atlas\Mapper\Record;

/**
 * @method ScheduleTrackingRow getRow()
 */
class ScheduleTrackingRecord extends Record
{
    use ScheduleTrackingFields;
}
