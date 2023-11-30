<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\User;
use App\Repositories\TrainingMonitoring\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function all()
    {
        return User::with(['role', 'userDetail'])
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->whereHas('role', function ($query) {
                $query->where('name', 'LIKE', 'trainee'); // Use ILIKE for case-insensitive search
            })
            ->get();
    }

    public function userWithRole($id)
    {
        return $userInfo = User::with('role')->where('id', $id)->get();
    }

    public function store($data)
    {
        return User::create($data);
    }

    public function update($user, $data)
    {
        return $user->update($data);
    }
}
