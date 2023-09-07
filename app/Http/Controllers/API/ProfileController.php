<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth('api')->user();

        return response([
            'status' => 200,
            'user' => new UserResource($user)
        ]);
    }
}
