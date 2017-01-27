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

Route::get('/', 'Bpositive\ProjectController@all')->name('projects');

Route::get('/transcriptions', 'Bpositive\TranscriptionController@all')->name('transcriptions');
Route::get('/transcription', 'Bpositive\TranscriptionController@get')->name('transcription');
Route::get('/download/transcription', 'Bpositive\TranscriptionController@download')->name('downloadTranscription');