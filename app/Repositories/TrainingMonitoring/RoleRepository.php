<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\Role;
use App\Models\TrainingMonitoring\User;
use App\Repositories\TrainingMonitoring\Interfaces\RoleRepositoryInterface;
use App\Traits\TrainingMonitoring\UtilityTrait;

class RoleRepository implements RoleRepositoryInterface
{
    use UtilityTrait;
    public function all()
    {
        $user = auth()->user();
        $userType = $this->authUser($user->email);
        $provider_id = $userType->provider_id;
        if($provider_id){
            return Role::with('permissions','userType')->whereHas('userType', function ($query) use ($provider_id) {
                $query->where('provider_id',$provider_id);
            })->get();
        }else{
            return Role::with('permissions','userType')->get();
        }
        
    }


    public function store($data,$request)
    {
        
        $role = Role::create($data);
        return $role->syncPermissions($request->get('permission'));
    }

    public function details($id)
    {
        return Role::with('permissions')->where('id', '=', $id)->first();
    }

    public function find($id)
    {

        return Role::find($id);
    }

    public function update($role, $data)
    {
        $role->name = $data['name'];
        $role->save();
    }

    public function delete($id)
    {
        return Role::find($id);
    }
}
