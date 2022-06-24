<?php

use Illuminate\Http\Request;

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

Route::post('/login', 'LoginController@login');
Route::post('/reset-password', 'LoginController@resetPassword');
Route::middleware('auth:api')->get('/refresh-token', 'LoginController@refresh');
Route::middleware('auth:api')->get('/logout', 'LoginController@logout');
Route::middleware('auth:api')->get('/catalogue', 'CatalogueController@index');
Route::middleware('auth:api')->get('/unit/{unit}', 'UnitController@get');
Route::middleware('auth:api')->get('/unit/{unit}/pronounciation', 'UnitController@getPronounciation');
Route::middleware('auth:api')->get('/unit/{unit}/guidedspeech', 'UnitController@getGuidedSpeech');
Route::middleware('auth:api')->post('/unit/{unit}/guidedspeech-report', 'ReportController@guidedSpeechReport');
Route::middleware('auth:api')->post('/unit/{unit}/reading-report', 'ReportController@readingReport');
Route::middleware('auth:api')->post('/unit/{unit}/quiz-report', 'ReportController@quizReport');
Route::middleware('auth:api')->post('/select-translation', 'TranslationController@select');
Route::middleware('auth:api')->get('/overall-progress', 'ReportController@getOverallProgress');