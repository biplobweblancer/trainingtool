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
        return ProvidersTrainer::with('profile', 'trainingBatch', 'provider')->get();
    }

    public function details($id)
    {
        return ProvidersTrainer::with('profile', 'trainingBatch', 'provider')->where('id', '=', $id)->first();
    }

}
