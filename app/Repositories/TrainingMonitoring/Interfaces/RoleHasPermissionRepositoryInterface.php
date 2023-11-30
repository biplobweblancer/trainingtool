<?php

namespace App\Repositories\TrainingMonitoring\Interfaces;

interface RoleHasPermissionRepositoryInterface
{

    public function store($role,$request);
}
