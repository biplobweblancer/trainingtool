<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface TrainerEnrollRepositoryInterface
{
    public function all();
    
    public function details($id);
}
