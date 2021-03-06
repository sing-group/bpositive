<?php

/*
 * B+
 * Copyright (C) 2017 Jorge Vieira, José Sousa, Miguel Reboiro-Jato,
 * Noé Vázquez, Bárbara Amorim, Cristina P. Vieira, André Torres, Hugo
 * López-Fernández, and Florentino Fdez-Riverola
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
Route::post('/project/getPrivate', 'Bpositive\ProjectController@getPrivate')->name('project_private');
Route::post('/project/makePublic', 'Bpositive\ProjectController@makePublic')->name('project_make_public');
Route::post('/project/makePrivate', 'Bpositive\ProjectController@makePrivate')->name('project_make_private');
Route::post('/project/accessPrivate', 'Bpositive\ProjectController@accessPrivate')->name('project_access_private');

Route::get('/project/create', 'Bpositive\ProjectManagerController@showCreateForm')->name('project_create_form');
Route::post('/project/create', 'Bpositive\ProjectManagerController@create')->name('project_create');
Route::get('/project/manage', 'Bpositive\ProjectManagerController@all')->name('project_manage');
Route::post('/project/remove', 'Bpositive\ProjectManagerController@remove')->name('project_remove');
Route::post('/project/edit', 'Bpositive\ProjectManagerController@edit')->name('project_edit_form');
Route::get('/project/edit', 'Bpositive\ProjectManagerController@edit');
Route::post('/project/save', 'Bpositive\ProjectManagerController@save')->name('project_edit');

Route::get('/transcriptions', 'Bpositive\TranscriptionController@all')->name('transcriptions');
Route::get('/transcription', 'Bpositive\TranscriptionController@get')->name('transcription');
Route::get('/transcription/name', 'Bpositive\TranscriptionController@findByName')->name('transcription_name');
Route::get('/download/transcription', 'Bpositive\TranscriptionController@download')->name('download_transcription');
Route::get('/download/transcriptions', 'Bpositive\TranscriptionController@downloadMultiple')->name('download_transcriptions');
Route::post('/transcription/remove', 'Bpositive\TranscriptionController@remove')->name('transcription_remove');
Route::post('/transcription/removeAll', 'Bpositive\TranscriptionController@removeAll')->name('transcription_remove_all');

Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('logout');
Route::post('/logout', 'Auth\LoginController@logout');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('/user/register', 'Auth\RegisterController@register');
Route::get('/user/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::get('/user/manage', 'Auth\UserController@all')->name('userManage');
Route::post('/user/remove', 'Auth\UserController@remove')->name('userRemove');
Route::post('/user/edit', 'Auth\UserController@edit')->name('userEdit');
Route::get('/user/edit', 'Auth\UserController@edit');
Route::post('/user/editOwn', 'Auth\UserController@editOwn')->name('userEditOwn');
Route::post('/user/save', 'Auth\UserController@save')->name('userSave');
Route::post('/user/saveOwn', 'Auth\UserController@saveOwn')->name('userSaveOwn');

Route::get('/info/submit', 'Bpositive\InfoController@showInfoSubmit')->name('info_submit');

