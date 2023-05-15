<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'address' => $user->address,
            'dob' => $user->dob,
            'interests' => $user->interests,
        ]);
    }
}
