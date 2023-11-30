<?php

namespace App\Models\TrainingMonitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    use HasFactory;
protected $connection = 'mysql-soms';

    protected $table = "tms_class_attendances";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'trainee_id', 'id');
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId');
    }
    public function scheduleDetail()
    {
        return $this->belongsTo(BatchScheduleDetail::class, 'batch_schedule_detail_id');
    }
}
