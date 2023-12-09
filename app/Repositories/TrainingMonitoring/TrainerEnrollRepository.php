<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\ProvidersTrainer;
use App\Repositories\TrainingMonitoring\Interfaces\TrainerEnrollRepositoryInterface;
use App\Traits\TrainingMonitoring\UtilityTrait;

class TrainerEnrollRepository implements TrainerEnrollRepositoryInterface
{
    use UtilityTrait;

    public function all()
    {
        $user = auth()->user();
        $userType = $this->authUser($user->email);
        $provider_id = $userType->provider_id;
        if($provider_id){
            return ProvidersTrainer::with('profile', 'trainingBatch', 'provider')->where('provider_id',$provider_id)->get();
        }else{
            return ProvidersTrainer::with('profile', 'trainingBatch', 'provider')->get();
        }
        
    }

    public function details($id)
    {
        return ProvidersTrainer::with('profile', 'trainingBatch', 'provider')->where('id', '=', $id)->first();
    }

}
