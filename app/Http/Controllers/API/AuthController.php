<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use App\Http\Resources\UserResource;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Students;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function studentLogin(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'matric' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validated->fails()) {
            return response(['message' => $validated->errors()->first()], 200);
        }

        $student = Students::where(['matric' => $request->matric])->first();
        if(isset($student) && Hash::check($request->password, $student->password)){
            $token = $student->createToken('authToken')->accessToken;
            return response()->json([
                'status' => 200,
                'token' => $token,
                'user' => new StudentResource3($student)
            ]);
        }

        return response()->json(['status' => 300, 'message' => 'Invalid Credentails']);
    }

    public function studentLogout(Request $request) {
        $token = $request->user('student_api')->token();
        $token->revoke();
        return response()->json(['status' => 200]);
    }

    public function userLogin(Request $request) 
    {
        $validated = Validator::make($request->all(), [
            'phone' => 'required'
        ]);

        if ($validated->fails()) {
            return response(['message' => $validated->errors()->first()], 200);
        }

        $user = Guardian::where(['phone' => $request->phone])->first();
        if(isset($user)){
            $token = $user->createToken('authToken')->accessToken;
            return response()->json([
                'status' => 200,
                'token' => $token,
                'phone' => $request->phone
            ]);
        } else {
            $child = Students::where('parent_phone_number', $request->phone)->first();
            if(isset($child)) {
                $guardian = new Guardian();
                $guardian->phone = $request->phone;
                $guardian->password = Hash::make('12345678');
                $guardian->save();

                $token = $guardian->createToken('authToken')->accessToken;
                return response()->json([
                    'status' => 200,
                    'token' => $token,
                    'phone' => $request->phone
                ]);
            }
        }

        return response()->json(['status' => 300, 'message' => 'Invalid Credentails']);
    }

    public function teacherLogin(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validated->fails()) {
            return response(['message' => $validated->errors()->first()], 200);
        }

        $user = User::where(['email' => $request->email])->first();
        if(isset($user) && Hash::check($request->password, $user->password)){
            $token = $user->createToken('authToken')->accessToken;
            return response()->json([
                'status' => 200,
                'token' => $token,
                'user' => new UserResource($user)
            ]);
        }

        return response()->json(['status' => 300, 'message' => 'Invalid Credentails']);
    }

    public function teacherLogout(Request $request)
    {
        $token = $request->user('api')->token();
        $token->revoke();
        return response()->json(['status' => 200]);
    }
}