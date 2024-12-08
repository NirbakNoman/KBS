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

Route::post('login', 'Api\AuthController');

Route::middleware(['auth:api'])->group(function () {

    Route::apiResource('users','Api\UserController');
    Route::apiResource('roles','Api\RoleController');
    Route::apiResource('permissions', 'Api\PermissionController');
    Route::apiResource('categories','Api\CategoryController');
    Route::apiResource('articles','Api\ArticleController');
    Route::apiResource('faqs','Api\FaqController');
    Route::apiResource('quiz-forms','Api\QuizFormController');
    Route::apiResource('quiz-form-fields','Api\QuizFormFieldController');
    Route::apiResource('quizzes','Api\QuizController');

});


