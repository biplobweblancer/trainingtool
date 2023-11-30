<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface RoleRepositoryInterface
{
    public function all();

    public function details($id);

    public function store($data,$request);

    public function find($id);

    public function update($data, $id);

    public function delete($id);
}
