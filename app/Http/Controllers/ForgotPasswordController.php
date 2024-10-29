<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }

        $verify = User::where('email', $request->all()['email'])->exists();

        if ($verify) {
            $verify2 =  DB::table('password_reset_tokens')->where([
                ['email', $request->all()['email']]
            ]);

            if ($verify2->exists()) {
                $verify2->delete();
            }

            $token = random_int(100000, 999999);
            $password_reset = DB::table('password_reset_tokens')->insert([
                'email' => $request->all()['email'],
                'token' =>  $token,
                'created_at' => Carbon::now()
            ]);

            if ($password_reset) {
                try {
                    Mail::to($request->all()['email'])->send(new ResetPassword($token));
                    return new JsonResponse(
                        [
                            'success' => true,
                            'message' => "Please check your email for a 6 digit pin",
                        ],
                        200
                    );
                } catch (\Exception $e) {
                    return new JsonResponse(
                        [
                            'error' => $e->getMessage()
                        ]
                    );
                }
            }
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "This email does not exist"
                ],
                400
            );
        }
    }
    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'token' => ['required'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }

        $check = DB::table('password_reset_tokens')
        ->where('email', $request->all()['email'])
        ->where('token', $request->all()['token']);

        if ($check->exists()) {
            $difference = Carbon::now()->diffInSeconds($check->first()->created_at);
            if ($difference > 3600) {
                return new JsonResponse(['success' => false, 'message' => "Token Expired"], 400);
            }

            DB::table('password_reset_tokens')
            ->where('email', $request->all()['email'])
            ->where('token', $request->all()['token'])
            ->delete();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => "You can now reset your password"
                ],
                200
            );
        } else {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "Invalid token"
                ],
                401
            );
        }
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['success' => false, 'message' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email);
        $token = $user->first()->createToken('myapptoken')->plainTextToken;

        $user->update([
            'password' => Hash::make($request->password),
            'token' => $token
        ]);

        return new JsonResponse(
            [
                'success' => true,
                'message' => "Your password has been reset",
                'token' => $token
            ],
            200
        );
    }
}
