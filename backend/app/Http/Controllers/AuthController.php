<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login( Request $request ){
        $validated = $request->validate([
        "email"=> 'required|email',
        "password" =>'required|string'
        ]);
        if (Auth::attempt(['email'=>$validated['email'],'password' => $validated['password']])) {
        $user= Auth::user();
        $token = $user->createToken('testing')->accessToken;
        return response()->json([
            'message'=>"Login Successful",
            "token" => $token
        ],200);
        }
        else{
            return response()->json([
            'message'=>"Login failed"
        ],401);
        }
    }
    public function register(Request $request) {
            $validated = $request->validate([
                "fullname"  => "required|string|max:255",
                "email"     => "required|email|unique:users,email",
                "password"  => "required|string|min:8",
                'admission' => [
                    'required',
                    'string',
                    'regex:/^[a-zA-Z0-9]+\/[a-zA-Z0-9]+\/\d{4}$/'
                ],
            ],
            [
            'admission.regex' => 'The admission number must be in the format COURSE/NUMBER/YEAR (e.g., BITC01/1065/2026).'
            ]);

            $user = User::create([
                'fullname'  => $validated['fullname'],
                'admission' => $validated['admission'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
            ]);

            // 3. Generate token
            $token = $user->createToken('testing')->accessToken;

            // 4. Return response
            return response()->json([
                "user"  => $user,
                "token" => $token
            ], 201); 
    }
 public function logout(Request $request){
    $request->user()->token()->revoke();

    return response()->json([
        'message' => 'Successfully logged out'
    ], 200);
 }
 public function getUser(){
    $response =  Auth::user();
    return response()->json([
    "message"=> "Got User successfully!!",
    "data" => $response
    ],200);
 }
public function updateUserData(Request $request) {
    $user = Auth::user();

    // 1. Map the keys EXACTLY to what Vue is sending
    $user->update([
        'fullname'         => $request->fullname,             // Vue sends 'name'
        'email'            => $request->email,
        'course'           => $request->course,
        'bio'              => $request->bio,
        'phone'            => $request->phone,
        'store_categories' => $request->storeCategories,  // Vue sends 'storeCategories'
    ]);

    // 2. Fetch the fresh, newly updated user from the database
    $updatedUser = $user->fresh();

    // 3. Return the actual user object, not a boolean
    return response()->json([
        "message" => "User updated successfully!!",
        "data"    => $updatedUser
    ]);
}
 }
