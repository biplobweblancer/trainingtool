<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface UserDetailRepositoryInterface
{
    public function all();

    public function find($id);

    public function update($data, $id);

    public function search($column_name, $search_value);
}
