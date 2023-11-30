<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\ClassAttendance;
use App\Repositories\TrainingMonitoring\Interfaces\ClassAttendanceRepositoryInterface;
use Exception;

class ClassAttendanceRepository implements ClassAttendanceRepositoryInterface
{

    public function checkAttendants($batchScheduleDetailId, $attendanceId)
    {
        return ClassAttendance::where('batch_schedule_detail_id', '=', $batchScheduleDetailId)
            ->where('ProfileId', '=', $attendanceId)
            ->first();
    }

    public function store($data)
    {
        return ClassAttendance::create($data);
    }

    public function markAsAbsent($attendances, $scheduleId)
    {
        try {
            foreach ($attendances as $value) {
                $attendance = ClassAttendance::where('training_batch_schedule_id', $scheduleId)
                    ->where('trainee_id', $value)
                    ->where('attendant_date', today())
                    ->first();

                $attendance->update([
                    'is_present' => 0,
                ]);
            }
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
