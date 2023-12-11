<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\RegisterRequest;
use App\Models\TrainingMonitoring\Emailnotification;
use App\Models\TrainingMonitoring\Role;
use App\Models\TrainingMonitoring\User;
use App\Models\TrainingMonitoring\UserVerify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Handle user account registration request
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {

        try {
            $data = $request->all();
            $role = Role::where('name', 'Trainee')->first();
            if ($role != null) {
                $data['role_id'] = $role->id;
            } else {
                $data['role_id'] = 0;
            }
            $data['password'] = Hash::make($data['password']);
            $data['reg_id'] = mt_rand(100000, 999999);
            $user = User::create($data);
            $token = Str::random(64);
            UserVerify::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            $subject = 'Email Verification Mail';

            Mail::send('emails.emailVerificationEmail', ['token' => $token], function ($message) use ($request, $subject) {
                $message->to($request->email);
                $message->subject($subject);
            });

            Emailnotification::create([
                'email_id' => $request->email,
                'subject' => $subject,
                'user_id' => $user->id,
                'send_date' => date('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('register.successful_registration_msg'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Write code on Method for verification of user email account
     *
     * @return response()
     */
    public function verifyAccount($token)
    {
        $verifyUser = UserVerify::with('user')->where('token', $token)->first();
        $message = trans('register.email_not_identified');

        if (!is_null($verifyUser)) {
            $user = $verifyUser->user;

            if (!$user->email_verified_at) {
                $verifyUser->user->email_verified_at = date('Y-m-d H:i:s');
                $verifyUser->user->save();
                $message = trans('register.email_verified_msg');
            } else {
                $message = trans('register.email_already_verified');
            }
        }
        
        // $adminBaseUrl = config('app.admin_url') . '/login';
        // return redirect($adminBaseUrl);
        $adminBaseUrl = config('app.admin_url') . '/login?message=' . urlencode($message); // Pass the message as a query parameter
       return redirect($adminBaseUrl);
    }
}
