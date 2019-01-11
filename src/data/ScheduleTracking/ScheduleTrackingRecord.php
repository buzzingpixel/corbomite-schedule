<?php
declare(strict_types=1);

namespace corbomite\schedule\data\ScheduleTracking;

use Atlas\Mapper\Record;

/**
 * @method ScheduleTrackingRow getRow()
 */
class ScheduleTrackingRecord extends Record
{
    use ScheduleTrackingFields;
}
