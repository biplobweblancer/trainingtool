<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Models\TrainingMonitoring\BatchScheduleDetail;
use App\Models\TrainingMonitoring\Provider;
use App\Models\TrainingMonitoring\TrainingBatch;
use App\Models\TrainingMonitoring\TrainingBatchSchedule;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    use UtilityTrait;

    public function runningBatch(Request $request)
    {
        try {
            
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $roleName = $userType->role->name;

            

            $batches = TrainingBatchSchedule::with('scheduleDetails', 'trainingBatch', 'trainingBatch.Provider','trainingBatch.Provider.userType','trainingBatch.providerTrainers', 'trainingBatch.training.title')
                ->whereHas('scheduleDetails', function ($query) {
                    $query->where('status', 2)
                        ->orWhere('status', 3);
                })
                ->whereHas('trainingBatch', function ($query) use ($request,$roleName,$userType) {
                    if ($request->search != '') {
                        $query->where('batchCode', 'like', '%' . $request->search . '%');
                    }
                    if (strtolower($roleName) == "provider") {
                        $provider_id = $userType->provider_id;
                        $query->where('provider_id', $provider_id);
                    }
                    
                })
                ->whereHas('trainingBatch.Provider.userType', function ($query) use ($roleName,$userType) {
                  
                    if (strtolower($roleName) == "trainer") {
                        $profile_id = $userType->ProfileId;
                        $query->where('ProfileId', $profile_id);
                    }
                    
                })
                ->distinct()
                ->paginate();

            $lastClass = BatchScheduleDetail::latest('id')->first();

            if (($lastClass->status != 3)) {
                return response()->json([
                    'success' => true,
                    'data' => $batches,
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
    public function index(Request $request)
    {
        try {
            $batches = TrainingBatch::all();

            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $batches,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // public function all batches for office
    public function allBatches(Request $request)
    {
        try {
            $search_batch = $request['batch'] ?? '';
            // dd($request['page']);
            if ($search_batch !== '') {
                $batches = TrainingBatch::with(['getTraining.title', 'schedule'])
                    ->where('batchCode', 'LIKE', '%' . $search_batch . '%')
                    ->paginate(20);
                // dd($batches);
            } else {
                $batches = TrainingBatch::with('getTraining.title', 'schedule')
                    ->paginate(20);
            }
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    // all batches for provider
    public function batchList(Request $request)
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $provider = Provider::find($userType->provider_id);
            if ($provider == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found',
                ]);
            }
            $batches = TrainingBatch::with('getTraining.title', 'schedule')
                ->whereNotNull('startDate')
                ->where('provider_id', $provider->id)
                ->whereHas('providerTrainers')
                ->paginate(20);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    // specific batch show
    public function batchShow($batch_id)
    {
        try {
            $batch = TrainingBatch::with('getTraining.title', 'schedule')
                ->where('id', $batch_id)
                ->first();
            if ($batch) {
                return response()->json([
                    'success' => true,
                    'data' => $batch,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "User Not a Provider",
                ]);
            }
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }
    }

    // specific batch for a specific provider
    public function show($id)
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $provider = Provider::find($userType->provider_id);
            if ($provider == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found',
                ]);
            }
            $batch = TrainingBatch::with('getTraining.title', 'schedule')
                ->whereNotNull('startDate')
                ->where('provider_id', $provider->id)
                ->whereHas('providerTrainers')
                ->where('id', $id)
                ->first();
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $batch,
        ]);
    }
}
