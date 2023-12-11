<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\RoleRequest;
use App\Http\Requests\TrainingMonitoring\StoreRoleRequest;
use App\Http\Requests\TrainingMonitoring\UpdateRoleRequest;
use App\Models\TrainingMonitoring\Role;
use App\Repositories\TrainingMonitoring\Interfaces\PermissionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\RoleHasPermissionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\RoleRepositoryInterface;
use Exception;

class RoleController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $roleRepository;
    private $permissionRepository;
    private $roleHasPermissionRepository;
    public function __construct(RoleRepositoryInterface $roleRepository, PermissionRepositoryInterface $permissionRepository, RoleHasPermissionRepositoryInterface $roleHasPermissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->roleHasPermissionRepository = $roleHasPermissionRepository;
    }

    /**
     * Display all Role
     *
     * @return Json Response
     */
    public function index()
    {
        try {
            $roles = $this->roleRepository->all();
            return response()->json([
                'success' => true,
                'data' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

    /**
     * Handle user role request
     *
     * @param roleRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(StoreRoleRequest $roleRequest)
    {
        try {
            $data = $roleRequest->all();
            $permissions = $this->roleRepository->store($data, $roleRequest);
            return response()->json([
                'success' => true,
                'message' => __('Role created successfully done.'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Role Edit request
     *
     * @param Role $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $tmsRole)
    {
        try {
            $role = $this->roleRepository->details($tmsRole->id);
            $rolePermissions = $tmsRole->permissions->pluck('name')->toArray();

            $permissions = $this->permissionRepository->all();
            return response()->json([
                'success' => true,
                'data' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $tmsRole, UpdateRoleRequest $request)
    {
        

        try {
            $data = $request->all();
            $roleData = [
                'name' => $data['name']
            ];
            $this->roleRepository->update($tmsRole, $roleData);
            $this->roleHasPermissionRepository->store($tmsRole->id, $data['accessPermissionIds']);
            return response()->json([
                'success' => true,
                'message' => 'Role and permission updated succefully',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }
}
