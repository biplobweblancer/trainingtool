<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface DistrictRepositoryInterface
{
    public function all($division_id = null);

    public function details($id);

    public function store($data);

    public function find($id);

    public function update($data, $id);

    public function delete($id);
}
