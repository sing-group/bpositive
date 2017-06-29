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

namespace App\Http\Controllers\Bpositive;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Transcription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;

class ProjectManagerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Project Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles requests from pages where projects are managed
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }


    public function create(Request $request){

        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'files.*' => 'file',
        ]);

        DB::beginTransaction();

        $projectId = Project::create($request->get('name'), $request->get('description'));

        if($projectId ) {
            if(is_array($request->file('files'))) {
                foreach ($request->file('files') as $file) {

                    $transcription = Transcription::create($projectId, $file->getClientOriginalName(), $file);

                    if ($transcription == -1) {
                        DB::rollBack();
                        return view('project.create', [
                            'project' => $projectId,
                            'errors' => new MessageBag(['Error creating transcription:' . $file->getClientOriginalName()])
                        ]);
                    };
                }
            }
        }
        else{
            DB::rollBack();
            return view('project.create', [
                'project' => $projectId,
                'errors' => new MessageBag(['Error creating project'])
            ]);
        }

        DB::commit();
        return redirect()->route('projects');
    }

    public function showCreateForm(Request $request){
        return view('project.create');
    }

}
