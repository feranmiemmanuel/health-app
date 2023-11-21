<?php

namespace App\Http\Controllers\Auth;

use JWTAuth;
use App\Models\User;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\HospitalUser;
use Illuminate\Http\Request;
use App\Providers\SendmailEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name' => 'required|string',
                    'last_name' => 'required|string',
                    'email' => 'required|string|email|max:255|unique:users,email',
                    'phone' => 'required|unique:users,phone',
                    'password' => 'required|string|confirmed|min:6',
                    'hospital_id' => 'required|exists:hospitals,id'
                ]
            );
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }
            $userId = uniqid('US');
            $user = new User();
            $user->id = $userId;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = 'PATIENT';
            $user->password = Hash::make($request->password);
            $user->save();
            $user = $user->refresh();
            // dd($user);

            $patient = new Patient();
            $patient->user_id = $userId;
            $patient->save();

            $userHospital = new HospitalUser();
            $userHospital->patient_id = $userId;
            $userHospital->hospital_id = $request->hospital_id;
            $userHospital->save();

            $hospital = Hospital::where('id', $request->hospital_id)->first();

            $details = [
                'title' => 'New Patient!',
                'subject' => 'You have a new Patient',
                'content' => [
                    'date' => now(),
                    'user_name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                ],
                'email' => $hospital->email,
                'name' => $hospital->name,
                'sending_type' => 'Verify Email',
                'template' => 'emails/newPatient'
            ];

            event(new SendmailEvent($details));

            $details = [
                'title' => 'Welcome to Health App',
                'subject' => 'Registration Successful',
                'content' => ['date' => now()],
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'sending_type' => 'Verify Email',
                'template' => 'emails/welcome'
            ];

            event(new SendmailEvent($details));

            $credentials = $request->only('email', 'password');

            $bearertoken = auth('api')->attempt($credentials);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registration is Successful',
                'access_token' => $bearertoken,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'user_type' => $user->user_type,
                'user_phone' => $user->phone,
                'user_email' => $user->email,
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
        $credentials = $request->only('email', 'password');

        $bearertoken = auth('api')->attempt($credentials);
        if (!$bearertoken) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Details'
            ], 400);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'token_type' => 'bearer',
            'access_token' => $bearertoken,
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
            'user_type' => auth()->user()->user_type,
            'user_phone' => auth()->user()->phone,
            'user_email' => auth()->user()->email,
        ], 200);
    }
}
