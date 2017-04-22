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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;

class ProjectController extends Controller
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

    }

    public function all(Request $request){

        $projects = Project::all();
        $params = ['projects' => $projects];
        if($request->has('errors')){
            array_push($params, new MessageBag([$request->get('errors')]));
        }

        return view('index', $params);
    }

    public function getPrivate(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric',
            'state' => 'in:accessPrivate,makePublic'
        ]);

        if($request->get('state') === 'makePublic'){
            if(Gate::denies('make-public')){
                return redirect()->route('projects', [
                    'errors' => 'Not Authorized'
                ]);
            }
        }

        $project = Project::getPrivate($request->get('id'));

        return view('projectPrivate',[
            'project' => $project,
            'state' => $request->get('state')
        ]);
    }

    public function makePublic(Request $request){

        if(Gate::denies('make-public')){
            return redirect()->route('projects', [
                'errors' => 'Not Authorized'
            ]);
        }

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        Project::setPublic($request->get('id'), 1);

        return redirect()->route('projects');
    }

    public function makePrivate(Request $request){

        if(Gate::denies('make-private')){
            return redirect()->route('projects', [
                'errors' => 'Not Authorized'
            ]);
        }

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        Project::setPublic($request->get('id'), 0);

        return redirect()->route('projects');
    }

    public function accessPrivate(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric',
            'password' => 'required|string',
            'state' => 'in:accessPrivate'
        ]);

        $project = Project::getPrivate($request->get('id'));
        if($project->privatePassword != hash('sha512', $request->get('password')) ){
            return view('projectPrivate', [
                'project' => $project,
                'errors' => new MessageBag(['Wrong password']),
                'state' => $request->get('state')
            ]);

        }
        else {
            $request->session()->put('allowPrivateAccessToId', $project->id);
        }

        return redirect()->route('transcriptions', ['id' => $project->id]);
    }


}
