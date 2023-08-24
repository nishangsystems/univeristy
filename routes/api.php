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

Route::get('student/store', [ApiController::class, 'store_student']);
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

