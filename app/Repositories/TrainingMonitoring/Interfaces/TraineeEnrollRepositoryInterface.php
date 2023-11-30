<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface TraineeEnrollRepositoryInterface
{
    public function all();
    
    public function details($id);
}
