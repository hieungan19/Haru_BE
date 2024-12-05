<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;


class UserController extends Controller
{
    public function getUsers()
    {
        $getUsers = User::get();
        return $getUsers;
    }
    public function registerUser(Request $request)
    {
        $client = new Client();
        if ($request->isMethod('post')) {
            $data = $request->input();

            $rules = [
                "email" => "required|email|unique:users,email",
                "password" => "required|required_with:confirmPassword|same:confirmPassword|min:6"
            ];
            $customMessages = [
                "email.required" => "Email is required",
                "email.email" => "Email address is invalid",
                "email.unique" => "Email is existed",
                "password.required" => "Password is required",
                "password.min" => "Password must have at least 6 characters",
                "password.same" => "Password is confirmed"
            ];

            $validator = Validator::make($data, $rules, $customMessages);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = User::create([
                'name' => $data['email'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;
            $user->token = $token;
            $user->save();
            $emailFunctionUrl = env('FUNCTION_EMAIL_WELCOME'); 
            $queryParams = [
                'email' => $data['email'],
                'name' => $data['email']
            ];   
             $response = $client->get($emailFunctionUrl, [
        'query' => $queryParams]);
            return response()->json([
                'status' => true,
                'message' => 'Singup successful'
            ], 201);
        }
    }
    public function loginUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();

            $rules = [
                "email" => "required|email|exists:users",
                "password" => "required"
            ];
            $customMessages = [
                "email.required" => "Email is required",
                "email.email" => "Email address is invalid",
                "email.exists" => "Email does not exists",
                "password.required" => "Password is required"
            ];

            $validator = Validator::make($data, $rules, $customMessages);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Xác minh người dùng
            $userCount = User::where('email', $data['email'])->count();
            if ($userCount > 0) {
                $userDetails = User::where('email', $data['email'])->first();

                if (password_verify($data['password'], $userDetails->password)) {
                    return response()->json([
                        'userDetails' => $userDetails,
                        'status' => true,
                        'message' => 'Login successfully'
                    ], 201);
                } else {
                    $message = "Password is incorrect";
                    return response()->json(['status' => false, 'message' => $message], 422);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Email does not exists'
                ], 422);
            }
        }
    }
    public function checkUserPassword(Request $request)
    {
            $data = $request->input();

            $userCount = User::where('email', $data['email'])->count();
            if ($userCount > 0) {
                $userDetails = User::where('email', $data['email'])->first();

                if (password_verify($data['password'], $userDetails->password)) {
                    return response()->json([
                        'status' => true,
                    ], 201);
                } else {
                    return response()->json(['status' => false, 'message' => "Password is incorrect"], 422);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Email does not exists'
                ], 422);
            }
    }
    public function logoutUser()
    {
        // auth()->user()->tokens()->delete();
        Auth::logout();
        // $request->user('sanctum')->currentAccessToken()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
    public function deleteUser(Request $request)
    {
            $data = $request->input();

            User::where('id', $data['id'])->update(['status' => false]);
            
            return response()->json([
                'status' => true,
                'message' => 'Delete user successful'
            ], 201);
    }
    public function updateUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();

            $userCount = User::where('id', $data['id'])->count();
            if ($userCount > 0) {
                $updateData = array_filter($request->all());
                if ($request->has('password'))
                    $updateData['password'] = bcrypt($updateData['password']);
                User::where('id', $data['id'])->update($updateData);

                return response()->json([
                    'status' => true,
                    'message' => 'User update successfully'
                ], 201);
            } else {
                $message = "User does not exists";
                return response()->json(['status' => false, 'message' => $message], 422);
            }
        }
    }
    public function loginGoogle(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();
            
            $userCount = User::where('email', $data['email'])->count();

            $flag = false;
            if($userCount > 0) {
                $flag = true;
            }
 
            if(!$flag){
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'])
                ]);

                $token = $user->createToken('myapptoken')->plainTextToken;
                $user->token = $token;
                $user->save();
            }

            $userDetails = User::where('email', $data['email'])->first();

            return response()->json([
                'status' => true,
                'message' => 'Login with google successful',
                'userDetails' => $userDetails,
            ], 201);
        }
    }
}
