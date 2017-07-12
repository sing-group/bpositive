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

use App\Exceptions\FileException;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Transcription;
use App\Providers\AuthServiceProvider;
use App\Utils\FileUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

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
        $this->middleware('auth');
    }


    public function create(Request $request){

        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'bundle' => 'in:1',
            'files.*' => 'file|mimetypes:application/zip,application/x-gzip',
        ]);

        DB::beginTransaction();

        $projectId = Project::create(Auth::user()->id, $request->get('name'), $request->get('description'));

        if($projectId) {
            if(is_array($request->file('files'))) {
                foreach ($request->file('files') as $file) {
                    if($file->isValid()) {
                        $names = array();
                        if ($request->has('bundle') && $request->get('bundle') == 1) {
                            $bundleNames = FileUtils::storeBundleAs($file, 'files/' . $projectId);
                            $names = array_merge($names, $bundleNames);

                        } else {
                            $path = FileUtils::storeAs($file, 'files/' . $projectId);
                            if ($file->getMimeType() == 'application/zip' ) {
                                FileUtils::zipToTgz($path);
                                $name = str_replace('.zip', '', $file->getClientOriginalName());
                            }
                            else{
                                $name = str_replace('.tar.gz', '', $file->getClientOriginalName());
                            }
                            array_push($names, $name);
                        }
                        foreach ($names as $transcriptionName){
                            try {
                                Transcription::create($projectId, $transcriptionName);
                            } catch (FileException $fe) {
                                error_log(print_r($fe->getMessage(), true));
                                DB::rollBack();
                                return view('project.create', [
                                    'project' => $projectId,
                                    'errors' => new MessageBag([
                                        'Error creating transcription: \'' . $file->getClientOriginalName() . '\'',
                                        $fe->getMessage(),
                                    ])
                                ]);
                            };

                        }
                    }
                    else{
                        throw new FileException("Upload of file '" . $file->getClientOriginalName(). "' invalid.");
                    }
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
        return redirect()->route('project_manage');
    }

    public function showCreateForm(Request $request){
        return view('project.create');
    }

    public function all(Request $request){

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE) {
            $projects = Project::all();
        }
        else{
            $projects = Project::getAllByUser(Auth::user()->id);
        }
        $params = ['projects' => $projects];
        return view('project.manage', $params);
    }

    public function edit(Request $request){
        $this->validate($request, [
            'id' => 'required|numeric',
            'query' => 'string',
            'page' => 'numeric',
            'pagesize' => 'numeric',
            'orderBy' => Rule::in(['name', 'description', 'analyzed', 'positivelySelected']),
            'orderType' => Rule::in(['asc', 'desc']),
            'filters.*' => Rule::in(['pss', 'analyzed', 'notAnalyzed', 'all']),
            'searchType' => Rule::in(['contains', 'regexp', 'exact']),
        ]);

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE) {
            $project = Project::getByAdmin($request->get('id'));
        }
        else{
            $project = Project::getByUser(Auth::user()->id, $request->get('id'));
        }

        $query = $request->get('query', '');
        $pagesize = $request->get('pagesize', '10');
        $orderBy = $request->get('orderBy', 'name');
        $orderType = $request->get('orderType', 'asc');
        $filters = $request->get('filters');
        $searchType = $request->get('searchType');

        $transcriptions = Transcription::all($request->get('id'), $query, $filters, $searchType, $pagesize, $orderBy, $orderType);
        return view('project.edit',[
            'project' => $project,
            'transcriptions' => $transcriptions,
            'query' => $query,
            'pagesize' => $pagesize,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'filters' => $filters,
            'searchType' => $searchType
        ]);
    }

    public function save(Request $request){
        $this->validate($request, [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'description' => 'required|string',
            'files.*' => 'file|mimetypes:application/zip,application/x-gzip',
        ]);

        Project::save($request->get('id'), $request->get('name'), $request->get('description'));

        if(is_array($request->file('files'))) {
            foreach ($request->file('files') as $file) {

                $transcription = Transcription::create($request->get('id'), $file->getClientOriginalName(), $file);

                if ($transcription == -1) {
                    DB::rollBack();
                    return view('project.create', [
                        'project' => $request->get('id'),
                        'errors' => new MessageBag(['Error creating transcription:' . $file->getClientOriginalName()])
                    ]);
                };
            }
        }

        return redirect()->route('project_manage');
    }

    public function remove(Request $request){
        $this->validate($request, [
            'id' => 'required|numeric',
        ]);

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE) {
            Project::delete($request->get('id'));
        }
        else{
            Project::deleteByUser($request->get('id'), Auth::user()->id );
        }


        return redirect()->route('project_manage');
    }

}
