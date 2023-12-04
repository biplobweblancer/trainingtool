<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\LoginRequest;
use App\Models\TrainingMonitoring\Role;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\Userlog;
use App\Models\TrainingMonitoring\UserType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SomsUser;
use Illuminate\Support\Facades\Validator;
use App\Models\TrainingMonitoring\Profile;
use Exception;

class LoginController extends Controller
{
    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $requestData = $request->all();
        $validator = Validator::make($requestData, [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['success' => false, 'message' => 'Please check the form', 'data' => $validator->errors()], 200);
        }

        // Manually check the credentials
        $user = SomsUser::where('email', $requestData['email'])->first();

        if (!$user || !Hash::check($requestData['password'], $user->password)) {
            return response(['success' => false, 'message' => 'Invalid SOMS credentials'], 200);
        }
        $profileEmail = $requestData['email'];

        // return $profileEmail;

        $userType = UserType::with('profile', 'role')
            ->whereHas('profile', function ($query) use ($profileEmail) {
                $query->where('Email', $profileEmail);
            })->first();

        $role = Role::with('permissions')->where('id', '=', $userType->role_id)->first();
        $accessPermissions = $role->permissions;


        $isExists = Userlog::where("user_id", $user->id)->where("status", 1)->first();

        if (!is_null($isExists)) {

        } else {
            $userLog = new Userlog();
            $userLog->user_id = $user->id;
            $userLog->login_date = date("Y-m-d H:i:s");
            $userLog->status = 1;
            $userLog->save();
        }


        // Create a token for the authenticated user
        $accessToken = $user->createToken('authToken')->accessToken;

        //return $role;

        return $this->authenticated($accessToken, $user, $userType, $accessPermissions);



    }

    /**
     * Handle response after user authenticated
     *
     * @param Auth $token
     *
     * @return \Illuminate\Http\Response
     */
    protected function authenticated($token, $user_info, $userType, $accessPermissions)
    {
        // dd($user_info->role->name);
        return response()->json([
            'success' => true,
            'error' => false,
            'access_token' => $token,
            'token_type' => 'Bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60,
            'expires_in' => Carbon::now()->addweek()->timestamp,
            'user' => $user_info,
            'userType' => $userType,
            'accessPermissions' => $accessPermissions,
        ]);
    }

    /**
     * Handle response after user logout
     *
     * @return Json Response
     */
    public function rolePermissionAccess($profileId)
    {

        try {
            $userType = UserType::where('ProfileId', '=', $profileId)->first();
            $roleId = $userType->role_id;
            $accessPermissions = Role::with('permissions')->where('id', '=', $roleId)->first();
            return response()->json([
                'success' => true,
                'accessPermissions' => $accessPermissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Handle response after user logout
     *
     * @return Json Response
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    /**
     * Show greetings
     *
     * @param Request $request [description]
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        try {
            $data = trans('language.welcome');
            return response()->json([
                'success' => true,
                'message' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    protected function responsWithToken($token)
    {
        return response()->json([
            'success' => true,
            'error' => false,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Carbon::now()->addweek()->timestamp
        ]);
    }
    // refresh token method
    public function refreshToken()
    {
        try {

            return $this->responsWithToken(auth()->refresh());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User is not Authenticated',
            ]);
        }
    }


}
