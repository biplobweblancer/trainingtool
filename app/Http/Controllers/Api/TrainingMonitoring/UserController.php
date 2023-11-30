<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Models\TrainingMonitoring\UserDetail;
use App\Repositories\TrainingMonitoring\PreliminarySelectionRepository;
use App\Repositories\TrainingMonitoring\UserDetailRepository;
use App\Repositories\TrainingMonitoring\UserRepository;
use App\Traits\TrainingMonitoring\UtilityTrait;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    use UtilityTrait;
    /*
     * Handle Bridge Between Database and Business layer
     */
    private $userRepository, $userDetailRepository, $preliminarySelectionRepository;
    public function __construct(
        UserRepository $userRepository,
        UserDetailRepository $userDetailRepository,
        PreliminarySelectionRepository $preliminarySelectionRepository
    ) {
        
        $this->userRepository = $userRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->preliminarySelectionRepository = $preliminarySelectionRepository;
    }

    /**
     * 
     * All Users 
     * 
     */
    public function index(Request $request)
    {
        $search = $request->all();
        try {
            if ($search) {
                $reg_id = $search['reg_id'];
                $name = $search['name'];
                $phone_number = $search['phone_number'];
                $email = $search['email'];
                $division_id = $search['division_id'];
                $district_id = $search['district_id'];
                $upazila_id = $search['upazila_id'];

                if ($upazila_id !== null && $name !== null) {
                    $query = UserDetail::with('user.role', 'district', 'upazila', 'category', 'subCategory');

                    $words = explode(" ", $name);
                    $f_name = $words[0] ?? '';
                    $l_name = $words[1] ?? '';

                    $query->whereHas('user', function ($query) use ($f_name, $l_name) {
                        $query->where('fname', 'LIKE', '%' . $f_name . '%')
                            ->where('lname', 'LIKE', '%' . $l_name . '%');
                    });

                    $query->where('upazila_id', 'LIKE', '%' . $upazila_id . '%');

                    $users = $query->get();
                } elseif ($district_id !== null && $name !== null) {
                    $query = UserDetail::with('user.role', 'district', 'upazila', 'category', 'subCategory');

                    $words = explode(" ", $name);
                    $f_name = $words[0] ?? '';
                    $l_name = $words[1] ?? '';

                    $query->whereHas('user', function ($query) use ($f_name, $l_name) {
                        $query->where('fname', 'LIKE', '%' . $f_name . '%')
                            ->where('lname', 'LIKE', '%' . $l_name . '%');
                    });

                    $query->where('district_id', 'LIKE', '%' . $district_id . '%');

                    $users = $query->get();
                } elseif ($division_id !== null && $name !== null) {
                } elseif ($reg_id !== null) {
                    $users = $this->userDetailRepository->search("reg_id", $reg_id);
                } elseif ($name !== null) {
                    $users = $this->userDetailRepository->search("name", $name);
                } elseif ($phone_number !== null) {
                    $users = $this->userDetailRepository->search("phone_number", $phone_number);
                } elseif ($email !== null) {
                    $users = $this->userDetailRepository->search("email", $email);
                } elseif ($upazila_id !== null) {
                    $users = $this->userDetailRepository->search("upazila_id", $upazila_id);
                } elseif ($district_id !== null) {
                    $users = $this->userDetailRepository->search("district_id", $district_id);
                } elseif ($division_id !== null) {
                    $users = $this->userDetailRepository->search("division_id", $division_id);
                }
            } else {
                $users = $this->userDetailRepository->all();
            }
            // add selection status
            $users_with_status = $this->selectionStatusAdd($users);

            return response()->json([
                'success' => true,
                'error' => false,
                'data' => $users_with_status
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
     * Show single user
     * 
     */
    public function show($userId)
    {
        try {
            
            return response([
                'success' => true,
                'error' => false,
                'data' => $userId
            ]);
        } catch (\Throwable $th) {
            return response([
                'success' => false,
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * 
     * preliminary select user add
     * 
     */
    public function preliminary_select(Request $request)
    {
        try {
            $all_selected_users = $request->input('selectedUserIds');
            $auth_id = auth()->id();

            $select_result = $this->preliminarySelectionRepository->store($all_selected_users, $auth_id);


            return response([
                'success' => true,
                'error' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
