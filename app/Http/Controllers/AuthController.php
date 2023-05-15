<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'dob' => ['required', 'date', 'before_or_equal:' . Carbon::now()->subYears(12)->format('Y-m-d')],
            'interests' => ['required', 'array'],
            'interests.*' => ['string', Rule::in(['Reading', 'Video Games', 'Sports', 'Travelling'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' =>
            $validator->errors()], 422);
         }
 
         $user = new User();
         $user->first_name = $request->first_name;
         $user->last_name = $request->last_name;
         $user->address = $request->address;
         $user->dob = \DateTime::createFromFormat('m/d/Y', $request->dob)->format('Y-m-d');
         $user->email = $request->email;
         $user->password = bcrypt($request->password);
         $user->save();
 
         $user->interests()->attach($request->interests);
 
         return response()->json(['message' => 'User registered successfully.']);
     }

     public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Email not verified'], 401);
            }
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
 }