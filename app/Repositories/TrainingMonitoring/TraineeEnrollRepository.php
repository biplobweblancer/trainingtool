<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\TrainingApplicant;
use App\Repositories\TrainingMonitoring\Interfaces\TraineeEnrollRepositoryInterface;
use App\Traits\TrainingMonitoring\UtilityTrait;

class TraineeEnrollRepository implements TraineeEnrollRepositoryInterface
{
    use UtilityTrait;

    public function all()
    {
        return TrainingApplicant::with('profile', 'trainingBatch', 'trainingTitle')->where('IsTrainee',1)->get();
    }

    public function details($id)
    {
        return TrainingApplicant::with('profile', 'trainingBatch', 'trainingTitle')->where('id', '=', $id)->first();
    }

}
