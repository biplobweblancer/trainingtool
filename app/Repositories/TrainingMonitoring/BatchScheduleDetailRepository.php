<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\ProvidersTrainer;
use App\Models\TrainingMonitoring\TrainingBatch;
use App\Repositories\TrainingMonitoring\Interfaces\BatchScheduleDetailInterface;
use Carbon\Carbon;

class BatchScheduleDetailRepository implements BatchScheduleDetailInterface
{
    public function store($data)
    {

        $batch_schedule_id = $data['batch_schedule_id'];
        $batch_id = $data['training_batch_id'];
        $provider_id = $data['provider_id'];
        $start_time = $data['start_time'];
        $end_time = Carbon::createFromFormat('H:i', $start_time)->addHours(config('app.class_hour'))->format('H:i');
        $total_class = $data['total_class'];

        $batch = TrainingBatch::find($batch_id);
        $start_date = $batch->startDate ?? now();

        $provider_trainer = ProvidersTrainer::where('provider_id', $provider_id)
            ->where('batch_id', $batch_id)
            ->first();

        $trainer_id = $provider_trainer->ProfileId;

        //Date         
        $class_date = Carbon::createFromFormat('Y-m-d H:i:s', $start_date);
        $class_days = explode(',', $data['class_days']);
        $class_days = array_map(function ($item) {
            return strtolower($item);
        }, $class_days);

        $schedule_details = [];

        if ($total_class) {
            for ($i = 0; $i < $total_class; $i++) {
                while (1) {
                    if (in_array(strtolower($class_date->format('l')), $class_days)) {
                        $schedule_details[] = [
                            'batch_schedule_id' => $batch_schedule_id,
                            'ConductedProfileId' => $trainer_id,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'date' => $class_date->format('Y-m-d'),
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $class_date->addDay();
                        break;
                    }
                    $class_date->addDay();
                }
            }

            BatchScheduleDetail::insert($schedule_details);
        }
    }
}
