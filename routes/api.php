<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController as ApiHomeController;
use App\Http\Controllers\API\Student\ProfileController;
use App\Http\Controllers\API\ProfileController as UserProfileController;
use App\Http\Controllers\Controller;

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

Route::post('hook/payments', [Controller::class, 'payments_hook_listener'])->name('tranzak.hook');
Route::get('hook/payments', [Controller::class, 'payments_hook_listener'])->name('tranzak.hook');

Route::get('login', [ApiController::class, 'degrees'])->name('degrees');
Route::get('login/student', [AuthController::class, 'studentLogin'])->name('student.login');
Route::get('logout/student', [AuthController::class, 'studentLogout'])->name('student.logout');
Route::get('login/user', [AuthController::class, 'userLogin'])->name('parent.login');
Route::get('logout/user', [AuthController::class, 'userLogout'])->name('parent.logout');
Route::get('login/teacher', [AuthController::class, 'teacherLogin'])->name('teacher.login');
Route::get('logout/teacher', [AuthController::class, 'teacherLogout'])->name('teacher.logout');
Route::get('faqs', [\App\Http\Controllers\API\PageController::class, 'faqs'])->name('faqs');
Route::get('year', [\App\Http\Controllers\API\PageController::class, 'year']);
Route::get('current_year', [\App\Http\Controllers\API\PageController::class, 'current_year']);
Route::get('semesters', [\App\Http\Controllers\API\PageController::class, 'semester']);

Route::group([ 'prefix' => 'student', 'as' => 'student.', 'middleware'=> 'api_student'], function() {
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('profile_details', [ProfileController::class, 'profile_details'])->name('profile_details');
    Route::get('notifications', [ProfileController::class, 'notifications']);

    Route::get('faqs', [ApiHomeController::class, 'faqs'])->name('faqs');
    Route::get('years', [ApiHomeController::class, 'batches'])->name('years');
    Route::get('levels', [ProfileController::class, 'levels'])->name('levels');
    Route::get('semesters', [ProfileController::class, 'semesters'])->name('semesters');
    Route::get('current_semester', [ProfileController::class, 'current_semester']);
    Route::get('current_accademic_year', [ApiHomeController::class, 'current_accademic_year']);
    Route::get('school', [ApiHomeController::class, 'school']);

    Route::get('courses', [App\Http\Controllers\API\Student\CourseController::class, 'courses']);
    Route::get('class_courses/{level_id?}', [App\Http\Controllers\API\Student\CourseController::class, 'class_courses']);
    Route::get('register_courses', [App\Http\Controllers\API\Student\CourseController::class, 'register']);
    Route::get('drop_courses', [App\Http\Controllers\API\Student\CourseController::class, 'drop']);

    Route::get('results/ca', [App\Http\Controllers\API\Student\ResultController::class, 'ca']);
    Route::get('results/exam', [App\Http\Controllers\API\Student\ResultController::class, 'exam']);
    Route::get('results/exam/download', [App\Http\Controllers\API\Student\ResultController::class, 'download_exam']);

    Route::get('fee', [App\Http\Controllers\API\Student\FeeController::class, 'index']);

    Route::get('registration/eligible', [App\Http\Controllers\API\Student\CourseController::class, 'registration_eligible']);
    Route::get('registered_courses', [App\Http\Controllers\API\Student\CourseController::class, 'registered_courses']);//request payload {year:int, semester:int}
    Route::post('form_b/download', [App\Http\Controllers\API\Student\CourseController::class, 'form_b']);//request payload {year:int, semester:int}

});

Route::group(['prefix' => 'user', 'as' => 'user.'], function() {
    Route::get('profile', [UserProfileController::class, 'profile'])->name('profile');
    Route::get('students', [\App\Http\Controllers\API\PageController::class, 'students']);
});

Route::get('notifications', [App\Http\Controllers\API\NotificationController::class, 'notifications']);
Route::get('attendance', [App\Http\Controllers\API\PageController::class, 'studentAttendance']);
Route::get('school', [App\Http\Controllers\API\PageController::class, 'school']);
Route::get('school_contacts', [App\Http\Controllers\API\PageController::class, 'school_contacts']);

Route::group([ 'prefix' => 'teacher', 'middleware'=>'api'], function() {
    Route::get('profile', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'profile']);
    Route::get('classes', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'classes']);
    Route::get('notifications', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'notifications']); // takes optional notification_id
    Route::get('course_notifications', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'course_notifications']); // takes course_id and optional notification_id
    Route::get('courses', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'subjects']); // takes campus_id or class_id or
    Route::get('create_notification', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'create_notification']); // takes course_id or class_id, or unit_id
    Route::get('{campus_id}/subjects/{class_id}', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'subjects']);
    Route::get('{campus_id}/student/{class_id}', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'students']);
    Route::get('student/attendance', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'studentAttendance']);
    Route::get('{class_id}/attendance', [\App\Http\Controllers\API\Teacher\TeacherController::class, 'attendance']);
});

Route::prefix('parent')->middleware(['parent_api'])->group(function(){
    Route::get('profile', [App\Http\Controllers\API\parent\HomeController::class, 'profile']);
    Route::get('students', [App\Http\Controllers\API\parent\HomeController::class, 'students']);
    Route::get('semesters', [App\Http\Controllers\API\parent\HomeController::class, 'semesters']);
    Route::get('fee', [App\Http\Controllers\API\parent\HomeController::class, 'fee']); // expects year and student as params
    Route::get('result', [App\Http\Controllers\API\parent\HomeController::class, 'results']); // expects year, semester and student as params
    Route::get('registerd_courses', [App\Http\Controllers\API\parent\HomeController::class, 'registerd_courses']); // expects year, semester and student as params
    Route::get('contact', [App\Http\Controllers\API\parent\HomeController::class, 'contacts']);
});

// ----------------------------- APPLICATION PORTAL API ENDPOINTS ----------------------------------
Route::post('student/store', [ApiController::class, 'store_student']);
Route::get('student/update', [ApiController::class, 'update_student']);
Route::get('degrees', [ApiController::class, 'degrees'])->name('degrees');
Route::get('certificates', [ApiController::class, 'certificates'])->name('certificates');
Route::get('certificate/program/{certificate_id}', [ApiController::class, 'get_certificate_programs'])->name('certificate.program.save');
Route::post('certificate/program/{certificate_id}', [ApiController::class, 'save_certificate_programs']);
Route::get('campuses', [ApiController::class, 'campuses'])->name('campuses');
Route::get('programs/{program_id?}', [ApiController::class, 'programs'])->name('programs');
Route::get('campus/program/levels/{campus_id}/{program_id}', [ApiController::class, 'campus_program_levels'])->name('campus.program.levles');
Route::get('campus/programs/{campus_id}', [ApiController::class, 'campus_programs'])->name('campus.programs');
Route::get('campus/programs/by_school/{campus_id}', [ApiController::class, 'campus_programs_by_school'])->name('campus.programs');
Route::get('campus/degree/certificate/programs/{campus_id}/{degree_id}/{certificate_id?}', [ApiController::class, 'campus_degree_certificate_programs'])->name('certificate.programs');
Route::get('campus/degrees/{campus_id}', [ApiController::class, 'campus_degrees'])->name('campus.degrees');
Route::post('campus/degrees/{campus_id}', [ApiController::class, 'update_campus_degrees']);
Route::get('levels', [ApiController::class, 'levels']);
Route::get('matrics/highest/{pref}/{year}', [ApiController::class, 'max_matric']);
Route::post('matric/exists', [ApiController::class, 'matricule_exists']);
Route::get('degree/certificates/{degree_id}', [ApiController::class, 'get_degree_certificates']);
Route::post('degree/certificates/{degree_id}', [ApiController::class, 'set_degree_certificates']);
Route::get('portal_fee_structure/{year_id?}', [ApiController::class, 'portal_fee_structure']);
Route::get('class_portal_fee_structure/{program_id}/{level_id}/{year_id?}', [ApiController::class, 'class_portal_fee_structure']);
Route::get('school_program_structure', [ApiController::class, 'school_program_structure']);
Route::post('appliable_programs/set', [ApiController::class, 'save_appliable_programs']);
// ----------------------------- END OF APPLICATION PORTAL API ENDPOINTS ----------------------------------
