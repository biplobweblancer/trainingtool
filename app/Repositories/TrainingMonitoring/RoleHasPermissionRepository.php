<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Role;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\RoleHasPermission;
use App\Repositories\TrainingMonitoring\Interfaces\RoleHasPermissionRepositoryInterface;

class RoleHasPermissionRepository implements RoleHasPermissionRepositoryInterface
{

    public function store($role,$request)
    {
        RoleHasPermission::where('role_id',$role)->delete();
        //$rolePermission = new RoleHasPermission;
        $permissions = explode(',',$request);
        foreach($permissions as $value){
            $data = array();
            $data['permission_id'] = $value;
            $data['role_id'] = $role;
            RoleHasPermission::create([
                'permission_id' => $value,
                'role_id' => $role
            ]);
        }
    }
}
