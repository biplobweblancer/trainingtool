<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\RegisterRequest;
use App\Http\Requests\TrainingMonitoring\UpdateProfileRequest;
use App\Models\TrainingMonitoring\Emailnotification;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\UserVerify;
use App\Models\TrainingMonitoring\Role;
use App\Models\TrainingMonitoring\UserDetail;
use App\Models\TrainingMonitoring\Userlog;
use App\Repositories\TrainingMonitoring\UserDetailRepository;
use App\Repositories\TrainingMonitoring\UserlogRepository;
use App\Repositories\TrainingMonitoring\UserRepository;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mail;
use Exception;

class ProfileController extends Controller
{
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $userRepository;
    private $userDetailRepository;
    private $userlogRepository;
    public function __construct(UserRepository $userRepository, UserDetailRepository $userDetailRepository, UserlogRepository $userlogRepository)
    {
        
        $this->userRepository = $userRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->userlogRepository = $userlogRepository;
    }

    /**
     * Display profile data
     *
     * @return Json Response
     */
    public function index()
    {
        try {
            $data = [];
            $userId = auth()->id();
            $userInfo = $this->userRepository->userWithRole($userId);
            $userDetails = $this->userDetailRepository->find($userId);
            $userLog = $this->userlogRepository->findByUserIdWithLimit($userId, 10);
            //$percentage = $this->generatePercentage($userData); // here we use the generatePercentage trait

            if (!is_null($userLog)) {
                $lastLogin = $userLog->first();
            } else {
                $lastLogin = null;
            }

            $data = [
                'userInfo' => $userInfo,
                'userDetails' => $userDetails,
                'userLog' => $userLog,
                'lastLogin' => $lastLogin
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Update Profile Request
     *
     * @param Request $request
     *
     * @return Json Response
     */
    public function update(User $user, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_url' => 'sometimes|nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            "fname" => "required",
            "lname" => "required",
            'phone_number' => 'required|unique,users',
            "father_name" => "required",
            "mother_name" => "required",
            "gender" => "required",
            "nid" => "required",
            "dob" => "required",
            "b_certificate" => "required",
            "district_id" => "required",
            "upazila_id" => "required",
            "address" => "required",
            "employment_status" => "required",
            "financial_status" => "required",
            'past_training' => 'required|in:0,1',
            'past_course_name' => 'nullable|required_if:past_training,1',
            'past_course_duration' => 'nullable|required_if:past_training,1',
            'past_provider_id' => 'nullable|required_if:past_training,1',
            "bank_id" => "required",
            "bkash_id" => "required",
            "category_id" => "required",
            "sub_category_id" => "sometimes|nullable",
            "hsc_certificate" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
            "ssc_certificate" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
            "signature" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->messages(),
            ]);
        }

        $user_data = $validator->safe()->only(['fname', 'lname', 'phone_number']);

        $user_details_data = $validator->safe()->except(['fname', 'lname', 'phone_number']);
        $user_details_data['user_id'] = $user->id;

        try {
            $this->userRepository->update($user, $user_data);
            $this->userDetailRepository->update($user_details_data, $user);

            return response()->json([
                'success' => true,
                'message' => 'Successfully updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
