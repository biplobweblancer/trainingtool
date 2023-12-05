<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\ScheduleCreateRequest;
use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\Provider;
use App\Models\TrainingMonitoring\TrainingBatch;
use App\Models\TrainingMonitoring\TrainingBatchSchedule;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use UtilityTrait;
    public function store(ScheduleCreateRequest $request)
    {

        try {
            $validated_data = $request->all();
            try {
                $user = auth()->user();
                $userType = $this->authUser($user->email);
                $provider = Provider::find($userType->provider_id);
                if ($provider == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Provider information not found',
                    ]);
                }
                $batch = TrainingBatch::with('getTraining.title', 'schedule')
                    ->whereNotNull('startDate')
                    ->where('provider_id', $provider->id)
                    ->whereHas('providerTrainers')
                    ->where('id', $validated_data['training_batch_id'])
                    ->whereHas('primaryTrainer')
                    ->first();

                if ($batch == null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batch information not found',
                    ]);
                }
                if (((int) $batch->duration) <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Batch duration must be greater than zero',
                    ]);
                }
            } catch (\Exception $th) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => $th->getMessage(),
                ]);
            }

            $data = [
                ...$validated_data,
                'provider_id' => $provider->id,
                'training_id' => $batch->trainingId,
            ];

            try {
                $schedule = TrainingBatchSchedule::create($data);
                $class_date = Carbon::createFromFormat('Y-m-d H:i:s', $batch->startDate);
                $start_time = Carbon::createFromFormat('H:i', $validated_data['class_time']);
                $end_time = (clone $start_time)->addHours($schedule->class_duration);
                $total_class = (int) $batch->duration;
                $class_days = explode(',', $data['class_days']);
                $class_days = array_map(function ($item) {
                    return strtolower($item);
                }, $class_days);
            } catch (\Exception $th) {
                TrainingBatchSchedule::where('training_batch_id', $validated_data['training_batch_id'])->delete();
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => $th->getMessage(),
                ]);
            }

            try {
                $schedule_details = [];
                for ($i = 0; $i < $total_class; $i++) {
                    while (1) {
                        if (in_array(strtolower($class_date->format('l')), $class_days)) {
                            $schedule_details[] = [
                                'batch_schedule_id' => $schedule->id,
                                'ConductedProfileId' => $batch->primaryTrainer->ProfileId,
                                'start_time' => $start_time->format('H:i:s'),
                                'end_time' => $end_time->format('H:i:s'),
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
            } catch (\Exception $th) {
                BatchScheduleDetail::where('batch_schedule_id', $schedule->id)->delete();
                TrainingBatchSchedule::where('training_batch_id', $validated_data['training_batch_id'])->delete();
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => $th->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Schedule created Successfully',
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

    }
}
