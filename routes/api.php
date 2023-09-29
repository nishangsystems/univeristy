<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login', [ApiController::class, 'degrees'])->name('degrees');
Route::get('login/student', [AuthController::class, 'studentLogin'])->name('student.login');
Route::get('logout/student', [AuthController::class, 'studentLogout'])->name('student.logout');
Route::get('login/user', [AuthController::class, 'userLogin'])->name('parent.login');
Route::get('login/teacher', [AuthController::class, 'teacherLogin'])->name('teacher.login');
Route::get('faqs', [\App\Http\Controllers\API\PageController::class, 'faqs'])->name('faqs');
Route::get('year', [\App\Http\Controllers\API\PageController::class, 'year']);
Route::get('semesters', [\App\Http\Controllers\API\PageController::class, 'semester']);

Route::group([ 'prefix' => 'student', 'as' => 'student.'], function() {
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');

    Route::get('courses', [App\Http\Controllers\API\Student\CourseController::class, 'courses']);
    //Get courses for registration - ('api/student/class_courses/optional_level_id'):: optional level_id to get the courses for another level 
    Route::get('class_courses/{level_id?}', [App\Http\Controllers\API\Student\CourseController::class, 'class_courses']);
    //Register Courses - ('api/student/register_courses'):: expected data: courses: array of selected course_ids to be registered
    Route::post('register_courses', [App\Http\Controllers\API\Student\CourseController::class, 'register']);


    Route::get('results/ca', [App\Http\Controllers\API\Student\ResultController::class, 'ca']);
    Route::get('results/exam', [App\Http\Controllers\API\Student\ResultController::class, 'exam']);


    Route::get('fee', [App\Http\Controllers\API\Student\FeeController::class, 'index']);
});

Route::group(['prefix' => 'user', 'as' => 'user.'], function() {
    Route::get('profile', [UserProfileController::class, 'profile'])->name('profile');
    Route::get('students', [\App\Http\Controllers\API\PageController::class, 'students']);
});

Route::get('notifications', [App\Http\Controllers\API\NotificationController::class, 'notifications']);
Route::get('attendance', [App\Http\Controllers\API\PageController::class, 'studentAttendance']);

Route::group([ 'prefix' => 'teacher'], function() {
    Route::get('classes', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'classes']);
    Route::get('{campus_id}/notifications/{level_id}', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'notifications']);
    Route::get('{campus_id}/subjects/{class_id}', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'subjects']);
    Route::get('{campus_id}/student/{class_id}', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'students']);
    Route::get('{class_id}/attendance', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'attendance']);
});

Route::post('student/store', [ApiController::class, 'store_student']);
Route::get('student/update', [ApiController::class, 'update_student']);
Route::get('degrees', [ApiController::class, 'degrees'])->name('degrees');
Route::get('certificates', [ApiController::class, 'certificates'])->name('certificates');
Route::get('certificate/program/{certificate_id}', [ApiController::class, 'get_certificate_programs'])->name('certificate.program.save');
Route::post('certificate/program/{certificate_id}', [ApiController::class, 'save_certificate_programs']);
Route::get('campuses', [ApiController::class, 'campuses'])->name('campuses');
Route::get('programs', [ApiController::class, 'programs'])->name('programs');
Route::get('campus/program/levels/{campus_id}/{program_id}', [ApiController::class, 'campus_program_levels'])->name('campus.program.levles');
Route::get('campus/programs/{campus_id}', [ApiController::class, 'campus_programs'])->name('campus.programs');
Route::get('campus/degree/certificate/programs/{campus_id}/{degree_id}/{certificate_id}', [ApiController::class, 'campus_degree_certificate_programs'])->name('certificate.programs');
Route::get('campus/degrees/{campus_id}', [ApiController::class, 'campus_degrees'])->name('campus.degrees');
Route::post('campus/degrees/{campus_id}', [ApiController::class, 'update_campus_degrees']);
Route::get('levels', [ApiController::class, 'levels']);
Route::get('matrics/highest/{pref}/{year}', [ApiController::class, 'max_matric']);
Route::post('matric/exists', [ApiController::class, 'matricule_exists']);
Route::get('degree/certificates/{degree_id}', [ApiController::class, 'get_degree_certificates']);
Route::post('degree/certificates/{degree_id}', [ApiController::class, 'set_degree_certificates']);

