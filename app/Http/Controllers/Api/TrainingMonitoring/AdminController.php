<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\UserVerify;
use App\Models\TrainingMonitoring\UserType;
use App\Models\TrainingMonitoring\Profile;
use App\Repositories\TrainingMonitoring\Interfaces\AdminRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class AdminController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $adminRepository;
    private $userRepository;
    public function __construct(AdminRepositoryInterface $adminRepository, UserRepositoryInterface $userRepository)
    {

        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    /**
     *
     * All Admin Users
     *
     */
    public function index()
    {
        try {
            $users = $this->adminRepository->all();
            //$userType = $users[1]->userType;
            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     *
     * Create Admin User
     *
     */
    /*public function store(Request $request)
    {
    // dd($request->all());
    if (Auth::check()) {
    $created_user_id = Auth::user()->id;
    $validator = Validator::make($request->all(), [
    "image_url" => "image|mimes:jpg,png,jpeg,gif,svg",
    "fname" => "required",
    "lname" => "required",
    "email" => "required|unique:users",
    "username" => "required|unique:users",
    "phone_number" => 'required',
    "role_id" => "required",
    'provider_id' => 'required_if:role_id,9,13',
    "name" => "required",
    "designation" => '',
    "gender" => "required",
    "district_id" => '',
    "upazila_id" => '',
    "address" => ''
    ]);

    if ($validator->fails()) {
    return response()->json([
    'error' => true,
    'message' => $validator->messages(),
    ]);
    }

    $userData = $validator->safe()
    ->only(['fname', 'lname', 'email', 'username', 'role_id', 'phone_number']);
    $userData['password'] = Hash::make(12345678);
    $userData['reg_id'] = mt_rand(100000, 999999);

    // dd($userData['phone_number']);

    $user = $this->userRepository->store($userData);

    $token = Str::random(64);
    $verifyUser = UserVerify::create([
    'user_id' => $user->id,
    'token' => $token,
    ]);

    if (!is_null($verifyUser)) {
    $user = $verifyUser->user;
    if (!$user->email_verified_at) {
    $verifyUser->user->email_verified_at = date('Y-m-d H:i:s');
    $verifyUser->user->save();
    }
    }

    $userTypeData = $validator->safe()->except(['fname', 'lname', 'email', 'username', 'role_id', 'phone_number']);
    $userTypeData['user_id'] = $user->id;
    $userTypeData['created_user_id'] = $created_user_id;

    $userType = $this->adminRepository->store($userTypeData);

    if ($user && $userType) {
    return response()->json([
    'success' => true,
    'error' => false
    ]);
    } else {
    return response()->json([
    'success' => false,
    'error' => true
    ]);
    }
    }
    } */

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                "email" => "required|email",
                "role_id" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->messages(),
                ]);
            }

            $email_is_exists = Profile::where('email', $data['email'])->first();
            if ($email_is_exists) {
                $providerId = null;
                if ($data['provider_id']) {
                    $providerId = $data['provider_id'];
                }
                $userType = UserType::create([
                    'role_id' => $data['role_id'],
                    'ProfileId' => $email_is_exists->id,
                    'district_id' => $data['district_id'],
                    'upazila_id' => $data['upazila_id'],
                    'provider_id' => $providerId,
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid Authentications',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin User Added Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     *
     * User Show with logs
     *
     */
    public function show($userProfileId)
    {
        $userData = [];

        $user = $this->adminRepository->userProfile($userProfileId);
        $userData = $user;

        return response([
            'success' => true,
            'error' => false,
            'data' => $userData,
        ]);
    }

    /**
     *
     * Get Single User for Edit
     *
     */
    public function edit($userId)
    {
        $userData = $this->adminRepository->details($userId);

        return response()->json([
            'success' => true,
            'error' => false,
            'data' => $userData,
        ]);
    }

    /**
     *
     * Update Admin User
     *
     */
    public function update($userId, Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "role_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }
        $userTypeData = array();
        $email_is_exists = Profile::where('email', $data['email'])->first();
        if ($email_is_exists) {
            $userTypeData['role_id'] = $data['role_id'];
            $userTypeData['ProfileId'] = $email_is_exists->id;
            $userTypeData['district_id'] = $data['district_id'];
            $userTypeData['upazila_id'] = $data['upazila_id'];
            $userTypeData['provider_id'] = $data['provider_id'];
            $adminUpdate = $this->adminRepository->update($userId, $userTypeData);
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Invalid Authentications',
            ]);
        }
        if ($adminUpdate) {
            return response()->json([
                'success' => true,
                'error' => false,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => true,
            ]);
        }
    }

    /**
     *
     * Delete Admin User
     *
     */
    public function destroy($userId)
    {
        try {
            $this->adminRepository->destroy($userId);
            return response()->json([
                'success' => true,
                'message' => 'Admin User Deleted Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
