<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\TrainingBatchSchedule;
use App\Repositories\TrainingMonitoring\Interfaces\TrainingBatchScheduleInterface;

class TrainingBatchScheduleRepository implements TrainingBatchScheduleInterface
{
    public function store($data)
    {
        return $batch_schedule = TrainingBatchSchedule::create([
            'training_id' => $data['training_id'],
            'training_batch_id' => $data['training_batch_id'],
            'provider_id' => $data['provider_id'],
            'class_days' => $data['class_days'],
            'class_time' => $data['class_time'],
            'class_duration' => $data['class_duration'],
        ]);
    }
}
