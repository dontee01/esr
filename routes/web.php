<?php

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

Route::get('/test', function () {
	// echo date('N');
    return view('homeee');
});

//Route::get('/mlm', 'ExploreController@test');

Route::get('/tos', function () {
    // echo date('N');
    return view('terms');
});


// Route::get('/test', 'UserController@index');



Route::get('/', 'UserController@index');

Route::get('/login', 'UserController@login_page');

Route::post('/login', 'UserController@login');

Route::post('/notification-subscribe', 'UserController@email_subscriptions');


Route::get('/register/{ref?}', 'UserController@register_page');

Route::post('/register', 'UserController@register');



Route::get('/profile', 'ExploreController@profile');

Route::post('/profile/update', 'ExploreController@profile_update');

Route::get('/account/verify/{username}', 'UserController@send_verification_email');

Route::get('/account/activate/{token}', 'UserController@verify_email');

Route::get('/account/change-password', 'ExploreController@change_password_page');

Route::post('/account/change-password', 'ExploreController@change_password');

Route::post('/account/change-pin', 'ExploreController@change_pin');


// Route::get('/account/topup', 'ExploreController@topup_page');

Route::post('/account/topup', 'ExploreController@topup');

Route::post('/account-withdraw', 'ExploreController@cash_out')->name('account-withdraw');

// Route::post('/account/top-up-confirm', 'ExploreController@topup_confirm');



Route::get('/forgot', 'UserController@forgot_page');

Route::post('/forgot', 'UserController@forgot');

Route::get('/reset/activate/{token}', 'UserController@reset_page');

Route::post('/reset-update', 'UserController@reset');



Route::get('/logout', 'UserController@logout');




Route::any('/dashboard', 'ExploreController@dashboard');

Route::any('/get-states', 'ExploreController@get_states');

Route::get('/add-member', 'ExploreController@add_member_page');

Route::post('/add-member', 'ExploreController@add_member');

Route::post('/complete-registration', 'ExploreController@complete_registration');

Route::get('/history/transaction', 'ExploreController@transaction_history');

Route::get('/history/transfer', 'ExploreController@transfer_history');

Route::get('/history/requests', 'ExploreController@request_history');

Route::get('/history/gifts', 'ExploreController@gift_history');

Route::get('/history/withdrawal', 'ExploreController@withdrawal_history');

Route::get('/history/funding', 'ExploreController@funding_history');

Route::post('/funding-request', 'ExploreController@funding_request');

Route::post('/withdrawal-request', 'ExploreController@withdrawal_request');

Route::post('/transfer/init', 'ExploreController@transfer_init');

Route::post('/request/init', 'ExploreController@request_init');

Route::post('/transfer/complete', 'ExploreController@transfer_complete');

Route::post('/gift/generate', 'ExploreController@gift_generate');


Route::get('/how-to', 'ExploreController@how_to');





Route::get('/statistics', 'ExploreController@statistics');

Route::get('/referrals', 'ExploreController@referrals');

Route::get('/downlines', 'ExploreController@downlines');

Route::get('/tree', 'ExploreController@test');

Route::get('/income/referral', 'ExploreController@income_referrals');

Route::get('/income/matrix', 'ExploreController@income_matrix');





Route::get('/subscribe/{game_name}', 'SubscriptionController@subscribe')->name('subscribe');








Route::get('/tester-questions/add/', 'TesterController@questions')->name('tester-questions/add');

Route::post('/set-question-bank', 'TesterController@set_question')->name('set-question-bank');

Route::get('/tester-questions/show/{id?}', 'TesterController@questions_list');

Route::post('/tester-questions/show', 'TesterController@question_edit');

Route::get('/console-survey', 'TesterController@survey')->name('console-survey');


// Route::get('/survey', 'SurveyController@survey_page');

// Route::post('/survey', 'SurveyController@survey');


Route::get('/pmf', 'SurveyController@pmf_page');

Route::post('/pmf', 'SurveyController@pmf');




Route::post('/pay', 'ExploreController@redirectToGateway')->name('pay'); // Laravel 5.1.17 and above

Route::get('/payment/callback', 'ExploreController@handleGatewayCallback');



Route::get('/email/blaster', 'EmailController@blaster');

Route::post('/email/blast', 'EmailController@blast');


Route::get('/email/extract', 'EmailController@extract_contact');



// //////////////////////CONSOLE ////////////////////////////////


Route::group(['prefix' => 'console', 'namespace' => 'Console'], function () {
    Route::get('/', 'UserController@login_page');
    
    Route::get('/login', 'UserController@login_page');

    Route::post('/login', 'UserController@login');

    Route::post('/', 'UserController@login');

    Route::get('/dashboard', 'ExploreController@dashboard');

    Route::get('/funding-requests', 'ExploreController@funding_requests');

    Route::post('/funding/approve', 'ExploreController@funding_approve');

    Route::post('/funding/reject', 'ExploreController@funding_reject');

    Route::get('/withdrawal-requests', 'ExploreController@withdrawal_requests');

    Route::post('/withdrawal/approve', 'ExploreController@withdrawal_approve');

    Route::post('/withdrawal/reject', 'ExploreController@withdrawal_reject');

});

// //////////////////////CRON JOB(SCHEDULER)////////////////////////////////

Route::get('/sys/fund-transfer', 'BotController@fund_transfer');

Route::get('/sys/worker-move', 'BotController@worker_move');

Route::get('/sys/worker-pair', 'BotController@worker_pair');

Route::get('/sys/worker-postponed', 'BotController@worker_process_postponed');

Route::get('/sys/worker-refund', 'BotController@worker_process_refund');

Route::get('/sys/worker-unmatched', 'BotController@worker_process_unmatched');

Route::get('/sys/worker-bookings', 'BotController@worker_process_bookings');
