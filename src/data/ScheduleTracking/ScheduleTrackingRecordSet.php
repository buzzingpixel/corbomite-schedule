<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\data\ScheduleTracking;

use Atlas\Mapper\RecordSet;

/**
 * @method ScheduleTrackingRecord offsetGet($offset)
 * @method ScheduleTrackingRecord appendNew(array $fields = [])
 * @method ScheduleTrackingRecord|null getOneBy(array $whereEquals)
 * @method ScheduleTrackingRecordSet getAllBy(array $whereEquals)
 * @method ScheduleTrackingRecord|null detachOneBy(array $whereEquals)
 * @method ScheduleTrackingRecordSet detachAllBy(array $whereEquals)
 * @method ScheduleTrackingRecordSet detachAll()
 * @method ScheduleTrackingRecordSet detachDeleted()
 */
class ScheduleTrackingRecordSet extends RecordSet
{
}
