<?php

namespace App\Models\TrainingMonitoring;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchScheduleDetail extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';

    protected $table = 'tms_batch_schedule_details';
    protected $guarded = [];

    public function isClassStarted()
    {
        $now = Carbon::now()->format('H:i:s');
        $classStartTime = Carbon::parse($this->start_time)->format('H:i:s');

        if (strcmp($now, $classStartTime) <= 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isClassExpired()
    {
        $now = Carbon::now()->format('H:i:s');
        $classEndTime = Carbon::parse($this->end_time)->format('H:i:s');

        if (strcmp($now, $classEndTime) <= 0) {
            return false;
        } else {
            return true;
        }
    }

    public function schedule()
    {
        return $this->belongsTo(TrainingBatchSchedule::class, 'batch_schedule_id');
    }
}


/**
 * Write code on Method
 *
 * @return response()
 */