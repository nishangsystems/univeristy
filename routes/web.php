<?php

use App\Http\Controllers;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;



Route::get('/clear', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";
});

Route::post('login', [CustomLoginController::class, 'login'])->name('login.submit');
Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('logout', [CustomLoginController::class, 'logout'])->name('logout');

Route::post('reset_password_with_token/password/reset', [CustomForgotPasswordController::class, 'validatePasswordRequest'])->name('reset_password_without_token');
Route::get('reset_password_with_token/{token}/{email}', [CustomForgotPasswordController::class, 'resetForm'])->name('reset');
Route::post('reset_password_with_token', [CustomForgotPasswordController::class, 'resetPassword'])->name('reset_password_with_token');

Route::get('', 'WelcomeController@home');
Route::get('home', 'WelcomeController@home');

Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {
    Route::get('home', 'Admin\HomeController@index')->name('home');
    Route::get('', 'Admin\HomeController@index')->name('home');
    Route::get('setayear', 'Admin\HomeController@setayear')->name('setayear');
    Route::post('setayear', 'Admin\HomeController@createBatch')->name('createacademicyear');
    Route::get('deletebatch/{id}', 'Admin\HomeController@deletebatch')->name('deletebatch');
    Route::get('sections', 'Admin\ProgramController@sections')->name('sections');
    Route::get('sub_units/{parent_id}', 'Admin\ProgramController@index')->name('units.index');
    Route::get('new_units/{parent_id}', 'Admin\ProgramController@create')->name('units.create');
    Route::get('units/{parent_id}/edit', 'Admin\ProgramController@edit')->name('units.edit');
    Route::resource('units', 'Admin\ProgramController')->except(['index', 'create', 'edit']);
    Route::get('units/{parent_id}/subjects', 'Admin\ProgramController@subjects')->name('units.subjects');
    Route::get('units/{parent_id}/student', 'Admin\ProgramController@students')->name('students.index');
    Route::get('fee', 'Admin\FeesController@fee')->name('fee');
    Route::get('print_fee', 'Admin\FeesController@printFee')->name('print_fee');
    Route::get('print_fee/{student_id}', 'Admin\FeesController@printStudentFee')->name('print_fee.student');
    Route::get('fee/classes', 'Admin\FeesController@classes')->name('fee.classes');
    Route::get('fee/drive', 'Admin\FeesController@drive')->name('fee.drive');
    Route::get('fee/collect', 'Admin\FeesController@collect')->name('fee.collect');
    Route::get('fee/daily_report', 'Admin\FeesController@daily_report')->name('fee.daily_report');
    Route::get('fee/{id}', 'Admin\FeesController@fee')->name('fee.list');
    Route::delete('fee/{id}', 'Admin\FeesController@delete')->name('fee.destroy');
    Route::get('fee/{class_id}/report', 'Admin\FeesController@report')->name('fee.report');
    Route::get('fee/{class_id}/student', 'Admin\FeesController@student')->name('fee.student');



    Route::get('scholarships', 'Scholarship\ScholarshipController@index')->name('scholarship.index');
    Route::get('scholarship/create', 'Scholarship\ScholarshipController@create')->name('scholarship.create');
    Route::post('scholarships', 'Scholarship\ScholarshipController@store')->name('scholarship.store');
    Route::get('scholarships/students_eligible', 'Scholarship\UserScholarshipController@students_eligible')->name('scholarship.eligible');
    Route::post('scholarships/students/{id}/award', 'Scholarship\UserScholarshipController@store')->name('scholarship.award');
    Route::get('scholarships/students/{id}/award', 'Scholarship\UserScholarshipController@create')->name('scholarship.award.create');
    Route::get('scholarships/scholars', 'Scholarship\UserScholarshipController@index')->name('scholarship.awarded_students');
    Route::post('scholarships/scholars', 'Scholarship\UserScholarshipController@getScholarsPerYear')->name('scholarship.scholars');
    Route::get('scholarships/{id}', 'Scholarship\ScholarshipController@show')->name('scholarship.show');
    Route::get('scholarships/{id}/edit', 'Scholarship\ScholarshipController@edit')->name('scholarship.edit');
    Route::delete('scholarships/{id}/', 'Scholarship\ScholarshipController@destroy')->name('scholarship.destroy');
    Route::put('scholarships/{id}/', 'Scholarship\ScholarshipController@update')->name('scholarship.update');

    Route::get('incomes', 'Admin\IncomeController@index')->name('income.index');
    Route::get('incomes/create', 'Admin\IncomeController@create')->name('income.create');
    Route::post('incomes', 'Admin\IncomeController@store')->name('income.store');
    Route::get('incomes/{id}/edit', 'Admin\IncomeController@edit')->name('income.edit');
    Route::put('incomes/{id}/', 'Admin\IncomeController@update')->name('income.update');
    Route::delete('incomes/{id}/delete', 'Admin\IncomeController@destroy')->name('income.destroy');
    Route::get('incomes/pay_income/create', 'Admin\PayIncomeController@create')->name('income.pay_income.create');
    Route::get('incomes/{id}', 'Admin\IncomeController@show')->name('income.show');


    Route::prefix('fee/{class_id}')->name('fee.')->group(function () {
        Route::resource('list', 'Admin\ListController');
    });
    Route::prefix('fee/{student_id}')->name('fee.student.')->group(function () {
        Route::resource('payments', 'Admin\PaymentController');
    });
    Route::get('units/{parent_id}/subjects/manage', 'Admin\ProgramController@manageSubjects')->name('units.subjects.manage');
    Route::post('units/{parent_id}/subjects/manage', 'Admin\ProgramController@saveSubjects')->name('units.subjects.manage');
    Route::resource('subjects', 'Admin\SubjectController');

    Route::get('classmaster', 'Admin\UserController@classmaster')->name('users.classmaster');
    Route::post('classmaster', 'Admin\UserController@saveClassmaster')->name('users.classmaster');
    Route::delete('classmaster', 'Admin\UserController@deleteMaster')->name('users.classmaster');
    Route::get('classmaster/create', 'Admin\UserController@classmasterCreate')->name('users.classmaster.create');


    Route::get('result/import', 'Admin\ResultController@import')->name('result.import');
    Route::post('result/import', 'Admin\ResultController@importPost')->name('result.import');
    Route::get('result/export', 'Admin\ResultController@export')->name('result.export');
    Route::post('result/export', 'Admin\ResultController@exportPost')->name('result.export');

    Route::get('users/{user_id}/subjects', 'Admin\UserController@createSubject')->name('users.subjects.add');
    Route::delete('users/{user_id}/subjects', 'Admin\UserController@dropSubject')->name('users.subjects.drop');
    Route::post('users/{user_id}/subjects', 'Admin\UserController@saveSubject')->name('users.subjects.save');

    Route::resource('users', 'Admin\UserController');
    Route::get('students/import', 'Admin\StudentController@import')->name('students.import');
    Route::post('students/import', 'Admin\StudentController@importPost')->name('students.import');
    Route::get('student/matricule', 'Admin\StudentController@matric')->name('students.matricule');
    Route::post('student/matricule', 'Admin\StudentController@matricPost')->name('students.matricule');
    Route::resource('student', 'Admin\StudentController')->except(['index']);
    Route::resource('result_release', 'Admin\ResultController');
});

Route::prefix('user')->name('user.')->middleware('isTeacher')->group(function () {
    Route::get('',  'Teacher\HomeController@index')->name('home');
    Route::get('class', 'Teacher\ClassController@index')->name('class');
    Route::get('class/rank', 'Teacher\ClassController@classes')->name('rank.class');
    Route::get('rank_student/{class}', 'Teacher\ClassController@rank')->name('class.rank_student');
    Route::get('student/{class_id}/detail', 'Teacher\ClassController@student')->name('student.show');
    Route::get('student/{class_id}', 'Teacher\ClassController@students')->name('class.student');
    Route::get('{class_id}/student/{term_id}/report_card/{student_id}', 'Teacher\ClassController@reportCard')->name('student.report_card');
    Route::get('subject', 'Teacher\SubjectController@index')->name('subject');
    Route::get('subject/{subject}/result', 'Teacher\SubjectController@result')->name('result');
    Route::post('subject/{subject}/result', 'Teacher\SubjectController@store')->name('store_result');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('', 'Student\HomeController@index')->name('home');
    Route::get('subject', 'Student\HomeController@subject')->name('subject');
    Route::get('result', 'Student\HomeController@result')->name('result');
    Route::get('fee', 'Student\HomeController@fee')->name('fee');
});

Route::get('section-children/{parent}', 'HomeController@children')->name('section-children');
Route::get('section-subjects/{parent}', 'HomeController@subjects')->name('section-subjects');
Route::get('student-search/{name}', 'HomeController@student')->name('student-search');
Route::get('student-fee-search', 'HomeController@fee')->name('student-fee-search');
Route::get('student_rank', 'HomeController@rank')->name('student_rank');

Route::get('mode/{locale}', function ($batch) {
    session()->put('mode', $batch);
    return redirect()->back();
})->name('mode');
