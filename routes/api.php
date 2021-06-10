<?php

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

//Public route
Route::get('email/verify/{id}/{hash}', 'Api\Teacher\ApiVerificationController@verify')->name('verification.verify.api');
Route::get('email/resend', 'Api\Teacher\ApiVerificationController@resend')->name('verification.resend.api');
Route::get('time', 'Api\ApiTimeServer@index')->name('time_now');

//For teacher
Route::group(['prefix' => 'teacher', 'middleware' => ['cors', 'json.response', 'api'], 'namespace' => 'Api\Teacher'], function () {
    // Route quiz
    Route::group(['prefix' => 'quiz', 'middleware' => 'auth:api'], function () {
        Route::post('edit_quiz/{id}', 'QuizController@editQuiz')->name('api.edit-quiz');
        Route::post('create_quiz', 'QuizController@createQuiz')->name('api.create-quiz');
        Route::get('list_quiz', 'QuizController@showAllQuiz')->name('api.all-quiz');
        Route::post('delete_quiz/{id}', 'QuizController@deleteQuiz')->name('api.delete-quiz');
    });

    //Route question
    Route::group(['prefix' => 'question', 'middleware' => 'auth:api'], function () {
        Route::get('list_question', 'QuizController@showAllQuestion')->name('api.all-question');
        Route::post('create_question/{id}', 'QuizController@createQuestion')->name('api.create-question');
        Route::post('edit_question/{id}', 'QuizController@editQuestion')->name('api.edit-question');
        Route::post('delete_question/{id}', 'QuizController@deleteQuestion')->name('api.delete-question');
    });

    //Route auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'ApiAuthController@login')->name('api.login');
        Route::post('signup', 'ApiAuthController@signup')->name('api.signup');
        Route::post('logout', 'ApiAuthController@logout')->name('api.logout');
        Route::post('email/password', 'ApiAuthController@forgot')->name('api.forgot-password');
        Route::get('/', 'ApiUserController@index')->name('api.user-info');
        Route::put('update', 'ApiUserController@update')->name('api.user-update');
    });

    // For student
    Route::group(['middleware' => ['auth:api', 'verified']], function () {
        //Route Room
        Route::group(['prefix' => 'room'], function () {
            Route::get('list/{search?}/{orderBy?}/{type?}', 'ApiRoomController@index')->name('api.room-list');
            Route::post('create', 'ApiRoomController@store')->name('api.room-store');
            Route::post('{room}/share', 'ApiRoomController@share')->name('api.room-share');
            Route::post('launch', 'ApiRoomController@launchRoom')->name('api.room-launch');
            Route::post('{room}/stop-launch', 'ApiRoomController@stopLaunchRoom')->name('api.room-stop');
            Route::post('{room}/delete', 'ApiRoomController@delete')->name('api.room-delete');
        });
    });


});

// For student
Route::group(['prefix' => 'student', 'middleware' => ['cors', 'json.response', 'api'], 'namespace' => 'Api\Student'], function () {
    // Route user
    Route::get('join-room/{id}', 'ApiRoomController@joinRoom')->name('api.room-join');
    Route::post('join-room', 'ApiRoomController@joinRoomByName')->name('api.room-join-by-name');

    Route::group(['middleware' => 'room-auth'], function () {
        Route::post('register', 'ApiRoomController@register')->name('api.room-register');
    });
});

Route::get('/quiz-test', 'Api\Teacher\ApiQuizController@index');

//Route::get("/all-user", function (){
////    return new \App\Http\Resources\UserCollection(\App\User::with("rooms")->where("id", 1)->first());
//    return \App\Http\Resources\UserCollection::collection(\App\User::with("rooms")->get());
//});
