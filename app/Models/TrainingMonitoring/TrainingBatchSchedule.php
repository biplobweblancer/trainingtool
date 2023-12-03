<?php

namespace App\Models\TrainingMonitoring;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingBatchSchedule extends Model
{
    use HasFactory;
    protected $connection = 'mysql-soms';

    protected $table = "tms_training_batch_schedules";
    protected $guarded = [];
    public $timestamps = false;

    public function trainingBatch()
    {
        return $this->belongsTo(TrainingBatch::class);
    }

    public function scheduleDetails()
    {
        return $this->hasMany(BatchScheduleDetail::class, 'batch_schedule_id');
    }
    public function isStatus1()
    {
        return $this->hasOne(BatchScheduleDetail::class, 'batch_schedule_id')
            ->where('status', 1);
    }
    public function isStatus2()
    {
        return $this->hasOne(BatchScheduleDetail::class, 'batch_schedule_id')
            ->where('status', 2);
    }
    public function isClassDay()
    {
        return false;
    }
}
