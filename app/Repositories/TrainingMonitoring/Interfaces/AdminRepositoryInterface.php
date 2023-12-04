<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface AdminRepositoryInterface
{
    public function all();

    public function store($data);

    public function find($id);

    public function details($id);

    public function update($user_id, $user_type_data);

    public function destroy($id);

    public function user_logs($id);
    public function userProfile($ProfileId);
}
