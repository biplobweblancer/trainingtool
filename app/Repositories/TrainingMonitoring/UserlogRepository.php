<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Userlog;
use App\Repositories\TrainingMonitoring\Interfaces\UserlogRepositoryInterface;

class UserlogRepository implements UserlogRepositoryInterface
{
    public function findByUserIdWithLimit($id, $take)
    {
        return Userlog::where('userlogs.user_id', $id)
            ->orderBy('id', 'DESC')
            ->take($take)
            ->get();
    }
}
