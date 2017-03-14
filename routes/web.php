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
Route::get('/project/getPrivate', 'Bpositive\ProjectController@getPrivate')->name('project_private');
Route::post('/project/makePublic', 'Bpositive\ProjectController@makePublic')->name('project_make_public');

Route::get('/transcriptions', 'Bpositive\TranscriptionController@all')->name('transcriptions');
Route::get('/transcription', 'Bpositive\TranscriptionController@get')->name('transcription');
Route::get('/transcription/name', 'Bpositive\TranscriptionController@findByName')->name('transcription_name');
Route::get('/download/transcription', 'Bpositive\TranscriptionController@download')->name('download_transcription');