<?php

namespace App\Repositories\TrainingMonitoring;

use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\Userlog;
use App\Models\TrainingMonitoring\UserType;
use App\Repositories\TrainingMonitoring\Interfaces\AdminRepositoryInterface;
use App\Traits\TrainingMonitoring\UtilityTrait;

class AdminRepository implements AdminRepositoryInterface
{
    use UtilityTrait;

    public function all()
    {
        /*return User::with('role', 'userType')->whereHas('role', function ($query) {
            $query->where('name', '!=', 'trainee')
                ->orWhere('name', '!=', 'Trainee');
        })->get();*/
        return UserType::with('role','profile','district','upazila','provider')
               ->get();
    }

    public function store($data)
    {
        if (isset($data['image_url'])) {
            $file = $data['image_url'];
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('uploads/admin/images'), $filename);
            $data['image_url'] = $filename;
        }

        return $userTypeData = UserType::create($data);
    }

    public function find($id)
    {
    }

    public function details($id)
    {
        return UserType::with('role','profile','district','upazila','provider')
            ->where('id', $id)
            ->first();
    }

    public function destroy($id)
    {
        $user_type = UserType::where('user_id', $id)->first();
        $profile_file_name = $user_type->image_url;
        $image_file_path = public_path('uploads/admin/images/') . $profile_file_name;

        if ($profile_file_name != null) {
            $this->deleteLocalFile($image_file_path);
        }

        $user_type->delete();

        User::destroy($id);
    }

    public function update($user_id,$user_type_data)
    {

        $userType = UserType::where('id', $user_id)->first();

        $user_type_update = $userType->update($user_type_data);

        if ($user_type_update) {
            return true;
        } else {
            return false;
        }
    }

    public function user_logs($id)
    {
        return $userLogs = Userlog::where('user_id', $id)->orderBy('id', 'DESC')->take(10)->get();
    }

    public function userProfile($ProfileId)
    {
        return UserType::with('role', 'profile', 'district', 'upazila', 'provider')
            ->where('ProfileId', $ProfileId)
            ->first();
    }
}
