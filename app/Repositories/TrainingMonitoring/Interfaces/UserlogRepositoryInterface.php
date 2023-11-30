<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface UserlogRepositoryInterface
{
    public function findByUserIdWithLimit($id, $take);
}
