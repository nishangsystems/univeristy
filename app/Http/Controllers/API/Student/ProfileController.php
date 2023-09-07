<?php


namespace App\Http\Controllers\API\Student;


use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource3;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request) 
    {
        $student = Auth('student_api')->user();

        return response([
            'status' => 200,
            'student' => new StudentResource3($student)
        ]);
    }
}