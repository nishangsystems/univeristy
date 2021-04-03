<?php
use App\Http\Controllers;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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

Route::get('','WelcomeController@home');
Route::get('home','WelcomeController@home');

Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {
    Route::get('home','Admin\HomeController@index')->name('home');
    Route::get('','Admin\HomeController@index')->name('home');
    Route::get('setayear','Admin\HomeController@setayear')->name('setayear');
    Route::get('setsem','Admin\HomeController@setsem')->name('setsem');
    Route::post('setayear','Admin\HomeController@createBatch')->name('createacademicyear');
    Route::post('setsem','Admin\HomeController@createsem')->name('createsem');
    Route::get('deletebatch/{id}','Admin\HomeController@deletebatch')->name('deletebatch');

    Route::get('sub_units/{parent_id}', 'Admin\ProgramController@index')->name('units.index');
    Route::get('new_units/{parent_id}', 'Admin\ProgramController@create')->name('units.create');
    Route::get('units/{parent_id}/edit', 'Admin\ProgramController@edit')->name('units.edit');
    Route::resource('units', 'Admin\ProgramController')->except(['index','create','edit']);

    Route::get('all_subjects/{exam_id}/{flag}', 'Admin\SubjectsController@index')->name('subjects.index');
    Route::get('new_subjects/{exam_id}/{flag}', 'Admin\SubjectsController@create')->name('subjects.create');
    Route::resource('subjects', 'Admin\SubjectsController')->except(['index','create']);

});

Route::prefix('teacher')->name('teacher.')->middleware('isTeacher')->group(function () {
    Route::get('','Teacher\HomeController@index')->name('home');

});

Route::prefix('student')->name('student.')->group(function () {


});

Route::get('locale/{locale}', function ($locale){
    Session::put('locale', $locale);
  return redirect()->back();
})->name('locale');
