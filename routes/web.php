<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\admin\StockController;
use App\Http\Controllers\Auth\CustomForgotPasswordController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
Route::get('registration', [Controller::class, 'registration'])->name('registration');
Route::post('check_matricule', [Controller::class, 'check_matricule'])->name('check_matricule');
Route::post('createAccount', [Controller::class, 'createAccount'])->name('createAccount');
Route::post('logout', [CustomLoginController::class, 'logout'])->name('logout');

Route::post('reset_password_with_token/password/reset', [CustomForgotPasswordController::class, 'validatePasswordRequest'])->name('reset_password_without_token');
Route::get('reset_password_with_token/{token}/{email}', [CustomForgotPasswordController::class, 'resetForm'])->name('reset');
Route::post('reset_password_with_token', [CustomForgotPasswordController::class, 'resetPassword'])->name('reset_password_with_token');

Route::get('', 'WelcomeController@home');
Route::get('home', 'WelcomeController@home');

Route::prefix('admin')->name('admin.')->middleware('isAdmin')->group(function () {


    Route::get('', 'Admin\HomeController@index')->name('home');
    Route::get('home', 'Admin\HomeController@index')->name('home');
    Route::get('background_image', 'Admin\HomeController@set_background_image')->name('set_background_image');
    Route::post('background_image', 'Admin\HomeController@save_background_image');
    Route::get('course/date_line', 'Admin\HomeController@courses_date_line')->name('course.date_line');
    Route::post('course/date_line', 'Admin\HomeController@save_courses_date_line');
    Route::get('setayear', 'Admin\HomeController@setayear')->name('setayear');
    Route::post('setayear/{id}', 'Admin\HomeController@setAcademicYear')->name('createacademicyear');
    Route::get('setsemester', 'Admin\HomeController@setsemester')->name('setsemester');
    Route::post('setsemester/{id}', 'Admin\HomeController@postsemester')->name('postsemester');
    Route::get('deletebatch/{id}', 'Admin\HomeController@deletebatch')->name('deletebatch');
    Route::get('sections', 'Admin\ProgramController@sections')->name('sections');
    Route ::get('sub_units_of/{id}', 'Admin\ProgramController@subunitsOf')->name('subunits');

    Route::get('sub_units/{parent_id}', 'Admin\ProgramController@index')->name('units.index');
    Route::get('new_units/{parent_id}', 'Admin\ProgramController@create')->name('units.create');
    Route::get('units/{parent_id}/edit', 'Admin\ProgramController@edit')->name('units.edit');
    Route::resource('units', 'Admin\ProgramController')->except(['index', 'create', 'edit']);
    Route::get('units/{program_level_id}/subjects', 'Admin\ProgramController@subjects')->name('units.subjects');
    Route::get('sections/{section_id}/subjects/{id}', 'Admin\ClassSubjectController@edit')->name('edit.class_subjects');
    Route::get('sections/{section_id}/subjects/{id}/delete', 'Admin\ClassSubjectController@delete')->name('delete.class_subjects');
    Route::put('sections/{section_id}/subjects/{id}', 'Admin\ClassSubjectController@update')->name('units.class_subjects.update');


    Route::get('units/{parent_id}/subjects/manage', 'Admin\ProgramController@manageSubjects')->name('units.subjects.manage_class_subjects');
    Route::post('units/{parent_id}/subjects/manage', 'Admin\ProgramController@saveSubjects')->name('units.subjects.manage');

    Route::get('units/{parent_id}/student', 'Admin\ProgramController@students')->name('students.index');

    Route::get('/class_list', 'Admin\ProgramController@program_levels_list')->name('class.list');
    Route::get('programs/assign_level', 'Admin\ProgramController@assign_program_level')->name('programs.set_levels');
    Route::post('programs/assign_level', 'Admin\ProgramController@store_program_level');
    Route::get('programs/{id}/levels', 'Admin\ProgramController@program_levels')->name('programs.levels');
    Route::get('programs/{id}/levels/{level_id}/add', 'Admin\ProgramController@add_program_level')->name('programs.levels.add');
    Route::get('programs/{id}/levels/{levle_id}/drop', 'Admin\ProgramController@drop_program_level')->name('programs.levels.drop');
    Route::get('programs/index', 'Admin\ProgramController@program_index')->name('programs.index');
    
    Route::get('fee', 'Admin\FeesController@fee')->name('fee');
    Route::get('fee/situation', 'Admin\FeesController@fee_situation')->name('fee.situation');
    Route::get('fee/situation/list', 'Admin\FeesController@fee_situation_list')->name('fee.situation.list');
    Route::get('fee/fee_list', 'Admin\FeesController@fee_list');
    Route::get('print_fee', 'Admin\FeesController@printFee')->name('print_fee');
    Route::get('print_fee/{student_id}', 'Admin\FeesController@printStudentFee')->name('print_fee.student');
    Route::get('fee/classes', 'Admin\FeesController@classes')->name('fee.classes');
    Route::get('fee/drive', 'Admin\FeesController@drive')->name('fee.drive');
    Route::get('fee/drive/listing', 'Admin\FeesController@drive_listing')->name('fee.drive_listing');
    Route::get('fee/collect', 'Admin\FeesController@collect')->name('fee.collect');
    Route::get('fee/daily_report', 'Admin\FeesController@daily_report')->name('fee.daily_report');
    Route::get('fee/{id}', 'Admin\FeesController@fee')->name('fee.list');
    Route::delete('fee/{id}', 'Admin\FeesController@delete')->name('fee.destroy');
    Route::get('fee/{class_id}/report', 'Admin\FeesController@report')->name('fee.report');
    Route::get('fee/{class_id}/student', 'Admin\FeesController@student')->name('fee.student');
    Route::get('import_fee/', 'Admin\FeesController@import')->name('import_fee');
    Route::post('import_fee/', 'Admin\FeesController@import_save');
    Route::post('fee/undo_import/{import_reference}', 'Admin\FeesController@import_undo')->name('fee.import.undo');

    Route::get('sections/{id}', 'Admin\PayIncomeController@getSections')->name('getSections');
    Route::get('classes/{id}', 'Admin\PayIncomeController@getClasses')->name('getClasses');
    Route::get('search/students/{name}', 'Admin\PayIncomeController@searchStudent')->name('searchStudent');
    Route::get('search/students/', 'Admin\PayIncomeController@get_searchStudent')->name('get_searchStudent');
    Route::get('search/users/', 'Admin\PayIncomeController@get_searchUser')->name('get_searchUser');

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

    Route::put('scholarships/{id}/', 'Scholarship\ScholarshipController@update')->name('scholarship.update');

    Route::get('incomes', 'Admin\IncomeController@index')->name('income.index');
    Route::get('incomes/create', 'Admin\IncomeController@create')->name('income.create');
    Route::post('incomes', 'Admin\IncomeController@store')->name('income.store');
    Route::get('incomes/{id}/edit', 'Admin\IncomeController@edit')->name('income.edit');
    Route::put('incomes/{id}/', 'Admin\IncomeController@update')->name('income.update');
    Route::delete('incomes/{id}/delete', 'Admin\IncomeController@destroy')->name('income.destroy');
    Route::get('incomes/pay_income/create', 'Admin\PayIncomeController@create')->name('income.pay_income.create');
    Route::get('incomes/pay_income/create_cash', 'Admin\PayIncomeController@create_cash')->name('income.pay_income.create_cash');
    Route::get('incomes/pay_income/create_cash/save', 'Admin\PayIncomeController@save_create_cash')->name('income.pay_income.save_cash');
    Route::get('incomes/pay_income/collect/{class_id}/{student_id}', 'Admin\PayIncomeController@collect')->name('income.pay_income.collect');
    Route::get('incomes/{id}', 'Admin\IncomeController@show')->name('income.show');
    Route::post('incomes/collect_income/{class_id}/{student_id}', 'Admin\PayIncomeController@store')->name('pay_income.store');
    Route::get('incomes/paid_income/list', 'Admin\PayIncomeController@index')->name('pay_income.index');
    Route::get('incomes/{student_id}/paid_income/{pay_income_id}/delete', 'Admin\PayIncomeController@delete_income')->name('income.delete');
    Route::get('{student_id}/incomes/{pay_income_id}/print_reciept', 'Admin\PayIncomeController@print')->name('income.print_reciept');
    Route::post('incomes/pay_income/list', 'Admin\PayIncomeController@getPayIncomePerClassYear')->name('pay_income.per_year');


    Route::get('expenses', 'Admin\Expense\ExpenseController@index')->name('expense.index');
    Route::get('expenses/create', 'Admin\Expense\ExpenseController@create')->name('expense.create');
    Route::post('expenses', 'Admin\Expense\ExpenseController@store')->name('expense.store');
    Route::get('expenses/{id}', 'Admin\Expense\ExpenseController@show')->name('expense.show');
    Route::get('expenses/{id}/edit', 'Admin\Expense\ExpenseController@edit')->name('expense.edit');
    Route::put('expenses/{id}/', 'Admin\Expense\ExpenseController@update')->name('expense.update');
    Route::delete('expenses/{id}/delete', 'Admin\Expense\ExpenseController@destroy')->name('expense.destroy');

    Route::prefix('fee/{class_id}')->name('fee.')->group(function () {
        Route::resource('list', 'Admin\ListController');
    });
    Route::prefix("fee/{student_id}")->name('fee.student.')->group(function () {
        Route::resource('payments', 'Admin\PaymentController');
        Route::get('payments/{item_id}/print', 'Admin\PaymentController@print')->name('payments.print');
    });


    Route::resource('subjects', 'Admin\SubjectController');
    Route::post('subjects/create/next', 'Admin\SubjectController@next')->name('courses.create_next');
    Route::get('subjects/create/{background}/{semester}', 'Admin\SubjectController@_create')->name('courses._create');
    Route::get('classmaster', 'Admin\UserController@classmaster')->name('users.classmaster');
    Route::post('classmaster', 'Admin\UserController@saveClassmaster')->name('users.classmaster');
    Route::delete('classmaster', 'Admin\UserController@deleteMaster')->name('users.classmaster');
    Route::get('classmaster/create', 'Admin\UserController@classmasterCreate')->name('users.classmaster.create');


    Route::get('result/import', 'Admin\ResultController@import')->name('result.import');
    Route::post('result/import', 'Admin\ResultController@importPost')->name('result.import');
    Route::get('result/export', 'Admin\ResultController@export')->name('result.export');
    Route::post('result/export', 'Admin\ResultController@exportPost')->name('result.export');
    Route::get('result/report', 'Admin\ResultController@report')->name('result.report');
    Route::post('result/report', 'Admin\ResultController@report_show')->name('result.report.show');

    Route::get('users/{user_id}/subjects', 'Admin\UserController@createSubject')->name('users.subjects.add');
    Route::delete('users/{user_id}/subjects', 'Admin\UserController@dropSubject')->name('users.subjects.drop');
    Route::post('users/{user_id}/subjects', 'Admin\UserController@saveSubject')->name('users.subjects.save');

    Route::resource('users', 'Admin\UserController');
    Route::get('students/init_promotion', 'Admin\StudentController@initialisePromotion')->name('students.init_promotion');
    Route::get('students/promotion', 'Admin\StudentController@promotion')->name('students.promotion');
    // Route::post('students/promote', 'Admin\StudentController@pend_promotion')->name('students.promote');
    Route::post('students/promote', 'Admin\StudentController@promote')->name('students.promote');
    Route::get('students/promotion/approve/{promotion_id?}', 'Admin\StudentController@trigger_approval')->name('students.trigger_approval');
    Route::post('students/promotion/approve', 'Admin\StudentController@approvePromotion')->name('students.approve_promotion');
    Route::get('students/promotion/cancelPromotion/{promotion_id}', 'Admin\StudentController@cencelPromotion')->name('students.cancel_promotion');
    Route::get('students/init_demotion', 'Admin\StudentController@initialiseDemotion')->name('students.init_demotion');
    Route::get('students/demotion', 'Admin\StudentController@demotion')->name('students.demotion');
    Route::get('students/demote/{promotion_id}', 'Admin\StudentController@demote')->name('students.demote');
    Route::get('demotion_target/{id}', 'Admin\StudentController@unitDemoteTarget')->name('demotion_target');
    Route::get('promotion_target/{id}', 'Admin\StudentController@unitTarget')->name('promotion_target');
    Route::get('promotion_batch/{id}', 'Admin\StudentController@promotionBatch')->name('promotion_batch');
    Route::get('students/import', 'Admin\StudentController@import')->name('students.import');
    Route::post('students/import', 'Admin\StudentController@importPost')->name('students.import');
    Route::post('students/clear', 'Admin\StudentController@clearStudents')->name('students.clear');
    Route::get('student/matricule', 'Admin\StudentController@matric')->name('students.matricule');
    Route::post('student/matricule', 'Admin\StudentController@matricPost')->name('students.matricule');
    Route::resource('student', 'Admin\StudentController');
    Route::post('students', 'Admin\StudentController@getStudentsPerClass')->name('getStudent.perClassYear');
    Route::get('result/bypass/{student_id?}', 'Admin\StudentController@studentResultBypass')->name('result.bypass');
    Route::get('result/bypass/{student_id}/set', 'Admin\StudentController@setStudentResultBypass')->name('result.bypass.set');
    Route::resource('result_release', 'Admin\ResultController');


    Route::get('boarding_fee/create', 'Admin\BoardingFeeController@create')->name('boarding_fee');
    Route::post('boarding_fee', 'Admin\BoardingFeeController@store')->name('boarding_fee.store');
    // Route::post('boarding_fee/{id}/installments', 'Admin\BoardingFeeController@addInstallments')->name('boarding_fee.installments.store');
    // Route::get('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@editBoardingPaymentInstallment')->name('boarding_fee.installments.edit');
    // Route::put('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@updateBoardingPaymentInstallment')->name('boarding_fee.installments.update');
    // Route::delete('boarding_fee/{id}/installments/{installment_id}', 'Admin\BoardingFeeController@deleteBoardingPaymentInstallment')->name('boarding_fee.installments.destroy');
    Route::get('boarding_fee', 'Admin\BoardingFeeController@index')->name('boarding_fee.index');
    Route::get('boarding_fee/{id}/edit', 'Admin\BoardingFeeController@edit')->name('boarding_fee.edit');
    Route::get('boarding_fee/{id}/installments', 'Admin\BoardingFeeController@createInstallments')->name('boarding_fee.installments');
    Route::put('boarding_fee/{id}', 'Admin\BoardingFeeController@update')->name('boarding_fee.update');
    Route::delete('boarding_fee/{id}', 'Admin\BoardingFeeController@destroy')->name('boarding_fee.destroy');
    Route::get('total_boarding_fee/{id}/',  'Admin\CollectBoardingFeeController@totalBoardingAmount')->name('getTotalBoardingAmount');
    Route::get('sub-units/{parent_id}','Admin\ProgramController@getSubUnits')->name('getSubUnits');


    Route::get('collect/boarding_fee/{class_id}/{student_id}', 'Admin\CollectBoardingFeeController@collect')->name('collect_boarding_fee.collect');
    Route::get('collect/boarding_fee', 'Admin\CollectBoardingFeeController@create')->name('collect_boarding_fee.create');
    Route::post('collect/boarding_fee/{class_id}/{student_id}', 'Admin\CollectBoardingFeeController@store')->name('collect_boarding_fee.store');
    Route::get('collected/boarding_fees/', 'Admin\CollectBoardingFeeController@index')->name('collect_boarding_fee.index');
    Route::get('collected/boarding_fees/{student_id}/{id}/edit', 'Admin\CollectBoardingFeeController@edit')->name('collect_boarding_fee.edit');
    Route::put('collected/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@update')->name('collect_boarding_fee.update');
    Route::get('collected/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@show')->name('collect_boarding_fee.show');
    Route::post('collected/boarding_fees', 'Admin\CollectBoardingFeeController@getBoardingFeePerYear')->name('boarding_fees_year');
    Route::post('collect/boarding_fees/{student_id}/{id}', 'Admin\CollectBoardingFeeController@collectBoardingFeeDetails')->name('boarding_fees_details');
    Route::get('students/{student_id}/boarding_fees/{id}/print', 'Admin\CollectBoardingFeeController@printBoardingFee')->name('boarding_fee.print');


    Route::resource('roles','Admin\RolesController');
    Route::get('permissions', 'Admin\RolesController@permissions')->name('roles.permissions');
    Route::get('assign_role', 'Admin\RolesController@rolesView')->name('roles.assign');
    Route::post('assign_role', 'Admin\RolesController@rolesStore')->name('roles.assign.post');
    Route::get('school/debts', 'Admin\SchoolDebtsController@index')->name('debts.schoolDebts');
    Route::post('school/debts', 'Admin\SchoolDebtsController@getStudentsWithDebts')->name('debts.getStudentWithDebts');
    Route::get('school/debts/{id}', 'Admin\SchoolDebtsController@getStudentDebts')->name('debts.showDebts');
    Route::post('school/debts/{id}', 'Admin\SchoolDebtsController@collectStudentDebts')->name('debts.collectDebts');
    
    Route::get('course_registration/date_line/{campus}/{semester}', 'Admin\HomeController@course_date_line')->name('courses.registration.date_line');
    Route::get('programs/settings', 'Admin\HomeController@program_settings')->name('program_settings');
    Route::post('programs/settings', 'Admin\HomeController@post_program_settings');
    
    Route::prefix('statistics')->name('stats.')->group(function(){
        Route::get('sudents', 'Admin\StatisticsController@students')->name('students');
        Route::get('fees', 'Admin\StatisticsController@fees')->name('fees');
        Route::get('results', 'Admin\StatisticsController@results')->name('results');
        Route::get('income', 'Admin\StatisticsController@income')->name('income');
        Route::get('expenditure', 'Admin\StatisticsController@expenditure')->name('expenditure');
        Route::get('fees/{class_id}', 'Admin\StatisticsController@unitFees')->name('unit-fees');
        Route::get('ie_report', 'Admin\StatisticsController@ie_report')->name('ie_report');
        Route::get('ie_report/monthly', 'Admin\StatisticsController@ie_monthly_report')->name('ie.report');
    });
    Route::prefix('campuses')->name('campuses.')->group(function(){
        Route::get('/', 'Admin\CampusesController@index')->name('index');
        Route::get('/create', 'Admin\CampusesController@create')->name('create');
        Route::get('/edit/{id}', 'Admin\CampusesController@edit')->name('edit');
        Route::post('/store', 'Admin\CampusesController@store')->name('store');
        Route::post('/update/{id}', 'Admin\CampusesController@update')->name('update');
        Route::get('/update/{id}', 'Admin\CampusesController@delete')->name('delete');
        Route::get('/{id}/programs', 'Admin\CampusesController@programs')->name('programs');
        Route::get('/{id}/programs/{program_id}/set_fee', 'Admin\CampusesController@set_program_fee')->name('set_fee');
        Route::get('/{id}/programs/{program_id}/add', 'Admin\CampusesController@add_program')->name('add_program');
        Route::get('/{id}/programs/{program_id}/drop', 'Admin\CampusesController@drop_program')->name('drop_program');
        Route::post('/{id}/programs/{program_id}/set_fee', 'Admin\CampusesController@save_program_fee');
    });
    Route::prefix('schools')->name('schools.')->group(function(){
        Route::get('/', 'Admin\SchoolsController@index')->name('index');
        Route::get('/create', 'Admin\SchoolsController@create')->name('create');
        Route::get('/edit/{id}', 'Admin\SchoolsController@edit')->name('edit');
        Route::get('/preview/{id}', 'Admin\SchoolsController@preview')->name('preview');
        Route::post('/store', 'Admin\SchoolsController@store')->name('store');
        Route::post('/update/{id}', 'Admin\SchoolsController@update')->name('update');
        Route::get('/update/{id}', 'Admin\SchoolsController@delete')->name('delete');
    });
    
    Route::get('grading/set_type/{program_id}', 'Admin\ProgramController@set_program_grading_type')->name('grading.set_type');
    Route::post('grading/set_type/{program_id}', 'Admin\ProgramController@save_program_grading_type')->name('grading.post_type');

    Route::prefix('semesters')->name('semesters.')->group(function(){
        Route::get('{program_id}', 'Admin\ProgramController@semesters')->name('index');
        Route::get('create/{program_id}', 'Admin\ProgramController@create_semester')->name('create');
        Route::get('edit/{program_id}/{id}', 'Admin\ProgramController@edit_semester')->name('edit');
        Route::get('delete/{id}', 'Admin\ProgramController@delete_semester')->name('delete');
        Route::post('store/{program_id}', 'Admin\ProgramController@store_semester')->name('store');
        Route::post('update/{program_id}/{id}', 'Admin\ProgramController@update')->name('update');
        Route::get('set_type/{program_id}', 'Admin\ProgramController@set_program_semester_type')->name('set_type');
        Route::post('set_type/{program_id}', 'Admin\ProgramController@post_program_semester_type');
    });

    Route::prefix('imports')->name('imports.')->group(function(){
        Route::get('import_ca', 'Admin\ImportCenter@import_ca')->name('import_ca');
        Route::post('import_ca', 'Admin\ImportCenter@import_ca_save');
        Route::get('clear_ca', 'Admin\ImportCenter@clear_ca')->name('clear_ca');
        Route::post('clear_ca', 'Admin\ImportCenter@clear_ca_save');
        Route::get('import_exam', 'Admin\ImportCenter@import_exam')->name('import_exam');
        Route::post('import_exam', 'Admin\ImportCenter@import_exam_save');
        Route::get('clear_exam', 'Admin\ImportCenter@clear_exam')->name('clear_exam');
        Route::post('clear_exam', 'Admin\ImportCenter@clear_exam_save');
        Route::get('clear_fee', 'Admin\ImportCenter@clear_fee')->name('clear_fee');
        Route::post('clear_fee', 'Admin\ImportCenter@clear_fee_save');
    });

    
    
    Route::prefix('stock')->name('stock.')->group(function(){
        Route::get('/', 'Admin\StockController@index')->name('index');
        Route::get('/create', 'Admin\StockController@create')->name('create');
        Route::get('/save', 'Admin\StockController@save')->name('save');
        Route::get('/report/{id}/{year?}', 'Admin\StockController@report')->name('report');
        Route::get('/report/{id}/{year}/print', 'Admin\StockController@print_report')->name('report.print');
        Route::get('/edit/{id}', 'Admin\StockController@edit')->name('edit');
        Route::get('/update/{id}', 'Admin\StockController@update')->name('update');
        Route::get('/receive/{id}', 'Admin\StockController@receive')->name('receive');
        Route::get('/receive/{id}/cancel', 'Admin\StockController@cancel_receive')->name('cancel_receive');
        Route::get('/accept/{id}', 'Admin\StockController@accept')->name('accept');
        Route::get('/share/{id}', 'Admin\StockController@send')->name('share');
        Route::get('/share/{id}/cancel', 'Admin\StockController@cancel_send')->name('cancel_share');
        Route::get('/send/{id}', 'Admin\StockController@__send')->name('send');
        Route::get('/delete/{id}', 'Admin\StockController@delete')->name('delete');
        Route::prefix('/campus/{campus_id}')->name('campus.')->group(function(){
            Route::get('/index', 'Admin\StockController@campus_index')->name('index');
            Route::get('/receive/{id}', 'Admin\StockController@campus_receive')->name('receive');
            Route::get('/accept/{id}', 'Admin\StockController@campus_accept')->name('accept');
            Route::get('/giveout/{id}', 'Admin\StockController@campus_giveout')->name('giveout');
            Route::get('/give/{id}', 'Admin\StockController@post_campus_giveout')->name('give');
            Route::get('/restore/{id}', 'Admin\StockController@restore')->name('restore');
            Route::get('/return/{id}', 'Admin\StockController@__restore')->name('return');
            Route::get('/student_stock/delete/{id}', 'Admin\StockController@delete_student_stock')->name('student_stock.delete');
        });
    });

    Route::get('extra-fee/{student_id?}', [AdminHomeController::class, 'extraFee'])->name('extra-fee');
    Route::get('extra-fee/{student_id}/save', [AdminHomeController::class, 'extraFeeSave'])->name('extra-fee.save');
    Route::get('set_letter_head', [AdminHomeController::class, 'set_letter_head'])->name('set_letter_head');
    Route::post('set_letter_head/save', [AdminHomeController::class, 'save_letter_head'])->name('save_letter_head');

});

Route::name('user.')->prefix('user')->middleware('isTeacher')->group(function () {
    Route::get('',  'Teacher\HomeController@index')->name('home');
    Route::get('class_list/{department_id}/{campus_id?}',  'Teacher\ClassController@program_levels_list')->name('class_list');
    Route::get('course_list',  'Teacher\ClassController@program_courses')->name('course_list');
    Route::get('class', 'Teacher\ClassController@index')->name('class');
    Route::get('students/init_promotion', 'Admin\StudentController@teacherInitPromotion')->name('students.init_promotion');
    Route::get('students/promote', 'Admin\StudentController@teacherPromotion')->name('students.promotion');
    Route::post('students/promote', 'Admin\StudentController@pend_promotion')->name('students.promote');
    Route::get('class/rank', 'Teacher\ClassController@classes')->name('rank.class');
    Route::get('class/master_sheet', 'Teacher\ClassController@master_sheet')->name('master_sheet');
    Route::get('rank_student/{class}', 'Teacher\ClassController@rank')->name('class.rank_student');
    Route::get('student/{class_id}/detail', 'Teacher\ClassController@student')->name('student.show');
    Route::get('student/{class_id}', 'Teacher\ClassController@students')->name('class.student');
    Route::get('{class_id}/student/{term_id}/report_card/{student_id}', 'Teacher\ClassController@reportCard')->name('student.report_card');
    Route::get('subject', 'Teacher\SubjectController@index')->name('subject');
    Route::get('subject/{subject}/result', 'Teacher\SubjectController@result')->name('result');
    Route::post('subject/{subject}/result', 'Teacher\SubjectController@store')->name('store_result');
    Route::get('subjects/notes/{class_id}/{id}', 'Teacher\SubjectNotesController@show')->name('subject.show');
    Route::get('subjects/students/{class_id}/{course_id}', 'Teacher\SubjectController@course_list')->name('subject.students');
    Route::put('subjects/notes/{id}', 'Teacher\SubjectNotesController@publish_notes')->name('subject.note.publish');
    Route::post('subjects/notes/{class_id}/{id}', 'Teacher\SubjectNotesController@store')->name('subject.note.store');
    Route::delete('subjects/notes/{id}', 'Teacher\SubjectNotesController@destroy')->name('subject.note.destroy');
    Route::get('{user_id}/subjects', 'Teacher\UserController@createSubject')->name('teacher.subjects.add');
    Route::post('{user_id}/teacher_subjects', 'Teacher\UserController@dropSubject')->name('teacher.subjects.drop');
    Route::post('{user_id}/subjects', 'Teacher\UserController@saveSubject')->name('teacher.subjects.save');
    Route::resource('teacher', 'Teacher\UserController');
    Route::prefix('programs')->name('programs.')->group(function(){
        Route::get('{department_id}', 'Teacher\HomeController@program_index')->name('index');
        Route::get('{program_id}/levels', 'Teacher\HomeController@program_levels')->name('levels');
        Route::get('{program_level_id}/courses', 'Teacher\HomeController@unit_courses')->name('courses');
        Route::get('{program_level_id}/manage_courses', 'Teacher\HomeController@manage_courses')->name('manage_courses');
        Route::post('{program_level_id}/manage_courses', 'Teacher\HomeController@save_courses')->name('save_courses');
    });
    Route::get('{program_level_id}/class_courses', 'Teacher\HomeController@manage_courses')->name('edit.class_courses');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('', 'Student\HomeController@index')->name('home');
    Route::get('edit_profile', 'Student\HomeController@edit_profile')->name('edit_profile');
    Route::post('update_profile', 'Student\HomeController@update_profile')->name('update_profile');
    Route::get('subject', 'Student\HomeController@subject')->name('subject');
    Route::get('result/ca', 'Student\HomeController@result')->name('result.ca');
    Route::post('result/ca', 'Student\HomeController@ca_result');
    Route::get('result/ca/download', 'Student\HomeController@ca_result_download');
    Route::get('result/exam', 'Student\HomeController@result')->name('result.exam');
    Route::post('result/exam', 'Student\HomeController@exam_result');
    Route::get('result/exam/download', 'Student\HomeController@exam_result_download');
    Route::get('fee/tution', 'Student\HomeController@fee')->name('fee.tution');
    Route::get('fee/others', 'Student\HomeController@other_incomes')->name('fee.other_incomes');
    Route::get('subjects/{id}/notes', 'Student\HomeController@subjectNotes')->name('subject.notes');
    Route::get('boarding_fees/details', 'Student\HomeController@boarding')->name('boarding');
    Route::post('boarding_fees/details/', 'Student\HomeController@getBoardingFeesYear')->name('boarding_fees_details');
    Route::prefix('courses')->name('courses.')->group(function(){
        Route::get('registration', 'Student\HomeController@course_registration')->name('registration');
        Route::post('registration', 'Student\HomeController@register_courses');
        Route::get('registered', 'Student\HomeController@registered_courses')->name('registered');
        Route::get('form_b', 'Student\HomeController@form_b')->name('form_b');
        Route::get('drop', 'Student\HomeController@drop_course')->name('drop');
        Route::get('add', 'Student\HomeController@add_course')->name('add');
    });
    Route::get('note/index/{course_id}', 'Student\HomeController@course_notes')->name('note.index');
    Route::get('assignment/index/{course_id}', 'Student\HomeController@assignment')->name('assignment.index');
    Route::get('notification/index/{course_id}', 'Student\HomeController@notification')->name('notification.index');
    Route::get('notification/show/{course_id}', 'Student\HomeController@show_notification')->name('notification.show');
    Route::prefix('notification')->name('notification.')->group(function(){
        Route::get('/', 'Student\HomeController@_notifications_index')->name('home');
        Route::get('/class/{class_id}/{campus_id}', 'Student\HomeController@_class_notifications')->name('class');
        Route::get('/department/{department_id}/{campus_id}', 'Student\HomeController@_department_notifications')->name('department');
        Route::get('/program/{program_id}/{campus_id}', 'Student\HomeController@_program_notifications')->name('program');
        Route::get('/school/{campus_id?}', 'Student\HomeController@_school_notifications')->name('school');
        Route::get('/view/{id}', 'Student\HomeController@_program_notifications_show')->name('view');
    });
    Route::prefix('material')->name('material.')->group(function(){
        Route::get('/', 'Student\HomeController@_notifications_index')->name('home');
        Route::get('/class/{class_id}/{campus_id}', 'Student\HomeController@_class_material')->name('class');
        Route::get('/department/{department_id}/{campus_id}', 'Student\HomeController@_department_material')->name('department');
        Route::get('/program/{program_id}/{campus_id}', 'Student\HomeController@_program_material')->name('program');
        Route::get('/school/{campus_id?}', 'Student\HomeController@_school_material')->name('school');
    });
    Route::get('resit/registration', 'Student\HomeController@resit_registration')->name('resit.registration');
    Route::post('resit/registration', 'Student\HomeController@register_resit');
    Route::get('resit/registered_courses', 'Student\HomeController@registered_resit_courses')->name('resit.registered_courses');
    Route::get('registered_courses/{year?}/{semester?}/{student?}', 'Student\HomeController@registerd_courses')->name('registered_courses');
    Route::get('class-subjects/{level}', 'Student\HomeController@class_subjects')->name('class-subjects');
    Route::get('search_course', 'Student\HomeController@search_course')->name('search_course');
    Route::get('courses/download/{year}/{semester}', 'Student\HomeController@download_courses')->name('courses.download');
    Route::get('stock/report/{year}', 'Student\HomeController@stock_report')->name('stock.report');
});

Route::get('section-children/{parent}', 'HomeController@children')->name('section-children');
Route::get('section-subjects/{parent}', 'HomeController@subjects')->name('section-subjects');
Route::get('student-search/{name}', 'HomeController@student')->name('student-search');
Route::get('student-search', 'HomeController@student_get')->name('student-search-get');
Route::get('search-all-students/{name}', 'HomeController@searchStudents')->name('search-all-students');
Route::get('search-all-students', 'HomeController@searchStudents_get')->name('get-search-all-students');
Route::get('student-fee-search', 'HomeController@fee')->name('student-fee-search');
Route::get('student_rank', 'HomeController@rank')->name('student_rank');
Route::post('student_rank', 'HomeController@rankPost')->name('student_rank');

Route::prefix('course/notification')->name('course.notification.')->group(function(){
    Route::get('{course_id}', 'Teacher\SubjectController@notifications')->name('index');
    Route::get('{course_id}/create', 'Teacher\SubjectController@create_notification')->name('create');
    Route::post('{course_id}/save', 'Teacher\SubjectController@save_notification')->name('save');
    Route::get('{course_id}/edit/{id}', 'Teacher\SubjectController@edit_notification')->name('edit');
    Route::post('{course_id}/update/{id}', 'Teacher\SubjectController@update_notification')->name('update');
    Route::get('{course_id}/delete/{id}', 'Teacher\SubjectController@drop_notification')->name('drop');
    Route::get('{course_id}/show/{id}', 'Teacher\SubjectController@show_notification')->name('show');
});

Route::name('faqs.')->prefix('faqs')->group(function(){
    Route::get('', 'FAQsController@index')->name('index');
    Route::get('create', 'FAQsController@create')->name('create');
    Route::post('create', 'FAQsController@save')->name('save');
    Route::get('edit/{id}', 'FAQsController@edit')->name('edit');
    Route::get('publish/{id}', 'FAQsController@publish')->name('publish');
    Route::get('download/{id}', 'FAQsController@download')->name('download');
    Route::post('update/{id}', 'FAQsController@update')->name('update');
    Route::get('show/{id}', 'FAQsController@show')->name('show');
    Route::get('delete/{id}', 'FAQsController@drop')->name('drop');
});

Route::name('material.')->prefix('{layer}/{layer_id}/material/{campus_id?}')->group(function(){
    Route::get('', 'MaterialController@index')->name('index');
    Route::get('create', 'MaterialController@create')->name('create');
    Route::post('create', 'MaterialController@save')->name('save');
    Route::get('edit/{id}', 'MaterialController@edit')->name('edit');
    Route::get('download/{id}', 'MaterialController@download')->name('download');
    Route::post('update/{id}', 'MaterialController@update')->name('update');
    Route::get('show/{id}', 'MaterialController@show')->name('show');
    Route::get('delete/{id}', 'MaterialController@drop')->name('drop');
});

// ALTERNATIVE NOTIFICATIONS AND MATERIAL APPRAOCH
Route::name('notifications.')->prefix('{layer}/{layer_id}/notifications/{campus_id?}')->group(function(){
    Route::get('/', 'NotificationsController@index')->name('index');
    Route::get('/create', 'NotificationsController@create')->name('create');
    Route::post('/create', 'NotificationsController@save')->name('save');
    Route::get('/delete/{id}', 'NotificationsController@drop')->name('drop');
    Route::get('/edit/{id}', 'NotificationsController@edit')->name('edit');
    Route::post('/update/{id}', 'NotificationsController@update')->name('update');
    Route::get('/show/{id}', 'NotificationsController@show')->name('show');
});

Route::get('search/students/boarders/{name}', 'HomeController@getStudentBoarders')->name('getStudentBoarder');

Route::get('/campuses/{id}/programs', function(Request $request){
    $order = \App\Models\SchoolUnits::orderBy('name', 'ASC')->pluck('id')->toArray();
    $resp = DB::table('campus_programs')->where('campus_id', '=', $request->id)
                ->join('program_levels', 'program_levels.id', '=', 'campus_programs.program_level_id')
                ->get(['program_levels.*']);
    // $resp = \App\Models\CampusProgram::where('campus_id', $request->id)->get();
    // $resp = \App\Models\CampusProgram::where('campus_id', $request->id)->orderBy(function($model) use ($order){
    //     return array_search($model->getKey(), $order);
    // });
    $data = [];
    foreach ($resp as $key => $value) {

        $value->program = \App\Models\SchoolUnits::find($value->program_id)->name;
        $value->level = \App\Models\Level::find($value->level_id)->level;
        $data[] = $value;
    }

    return $data;
})->name('campus.programs');
Route::get('semesters/{background}', function(Request $request){
    return \App\Models\Semester::where('background_id', $request->background)->get();
})->name('semesters');
Route::get('campus/{campus}/program_levels', [Controller::class, 'sorted_campus_program_levels'])->name('campus.program_levels');
Route::get('getColor/{label}', [HomeController::class, 'getColor'])->name('getColor');
Route::get('mode/{locale}', function ($batch) {
    session()->put('mode', $batch);

    return redirect()->back();
})->name('mode');
