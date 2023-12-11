<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\ProviderBatchesRequest;
use App\Models\TrainingMonitoring\TrainingBatch;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Exception;

class ProviderBatchesController extends Controller
{
    use UtilityTrait;
    public function index()
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);

            $provider_id = $userType['provider_id'];

            if ($provider_id) {
                $batches = TrainingBatch::with('training', 'training.trainingTitle', 'trainingBatchSchedule')
                    ->where('provider_id', $provider_id)
                    ->get();

                $user_id = $user->id;
                $role = $userType->role->name;

                return response()->json([
                    'success' => true,
                    'error' => false,
                    'data' => $batches,
                    'user_id' => $user_id,
                    'role' => $role,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "User Not a Provider",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    //
    public function store(ProviderBatchesRequest $request)
    {
        try {
            $data = $request->all();
            $provider_id = $data['provider_id'];
            $batch_ids_string = $data['batch_ids'];
            $numberStrings = explode(",", $batch_ids_string);

            // Initialize an empty array to store the integers
            $batch_ids_array = [];

            // Convert the string elements to integers
            foreach ($numberStrings as $numberString) {
                $batch_ids_array[] = (int) $numberString;
            }

            foreach ($batch_ids_array as $key => $batch_id) {
                $training_batch = TrainingBatch::find($batch_id);
                if ($training_batch->provider_id) {
                    return response()->json([
                        'success' => false,
                        'error' => true,
                        'message' => __('provider-list.already_link_batches'),
                    ]);
                } else {
                    $training_batch->provider_id = $provider_id;
                    $training_batch->save();
                }
            }

            return response()->json([
                'success' => true,
                'error' => false,
                'message' => __('provider-list.provider_link_batch_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
