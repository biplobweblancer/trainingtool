<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Models\TrainingMonitoring\ProvidersBatchCoordinator;
use App\Models\TrainingMonitoring\UserType;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Illuminate\Http\Request;
use Exception;

class CoordinatorController extends Controller
{
    use UtilityTrait;

    public function index()
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $userRole = $userType->role ? $userType->role->name : '';

            if ($userRole == "Provider" || $userRole == "provider") {
                $coordinators = UserType::with('profile', 'role')
                    ->where('provider_id', $userType->provider_id)
                    ->whereHas('role', function ($query) {
                        $query->where('name', '=', 'coordinator')
                            ->orWhere('name', '=', 'Coordinator');
                    })->get();
            } else {
                $coordinators = UserType::with('profile', 'role')
                    ->whereHas('role', function ($query) {
                        $query->where('name', '=', 'coordinator')
                            ->orWhere('name', '=', 'Coordinator');
                    })->get();
            }

            return response()->json([
                'success' => true,
                'data' => $coordinators,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // coordinators batch
    public function linkBatch($batch_id)
    {
        try {
            $user = auth()->user();
            $userType = $this->authUser($user->email);
            $userRole = $userType->role ? $userType->role->name : '';

            if ($userRole == "Provider" || $userRole == "provider") {
                $provider_id = $userType->provider_id;

                $coordinators = UserType::with('profile', 'role', 'profile.coordinator')
                    ->where('provider_id', $provider_id)
                    ->whereHas('role', function ($query) {
                        $query->where('name', '=', 'coordinator')
                            ->orWhere('name', '=', 'Coordinator');
                    })
                    ->whereHas('profile.coordinator', function ($query) use ($provider_id, $batch_id) {
                        $query->where('provider_id', '!=', $provider_id)
                            ->orWhere('batch_id', '!=', $batch_id);
                    })
                    ->get();

                $providerBatchCoordinators = ProvidersBatchCoordinator::where('provider_id', $provider_id)
                    ->where('batch_id', $batch_id)
                    ->pluck('ProfileId');
            }

            return response()->json([
                'success' => true,
                'data' => $coordinators,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}