<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Permission;
use App\Repositories\TrainingMonitoring\Interfaces\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function all()
    {
        return Permission::all();
    }

    public function store($data)
    {
        return Permission::create($data);
    }

    public function details($id)
    {
        return Permission::where('id', '=', $id)->first();
    }

    public function find($id)
    {

        return Permission::find($id);
    }

    public function update($permission, $data)
    {
        $permission->update($data);
    }

    public function delete($id)
    {
        return Permission::find($id);
    }
}
