<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\data\ScheduleTracking;

use Atlas\Mapper\MapperSelect;

/**
 * @method ScheduleTrackingRecord|null fetchRecord()
 * @method ScheduleTrackingRecord[] fetchRecords()
 * @method ScheduleTrackingRecordSet fetchRecordSet()
 */
class ScheduleTrackingSelect extends MapperSelect
{
}
