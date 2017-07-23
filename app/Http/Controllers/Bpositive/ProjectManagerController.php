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
use Illuminate\Support\Facades\Validator;
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

        $createdNames = [];

        DB::beginTransaction();

        $projectId = Project::create(Auth::user()->id, $request->get('name'), $request->get('description'));

        if($projectId) {
            if(is_array($request->file('files'))) {
                foreach ($request->file('files') as $file) {
                    if($file->isValid()) {
                        $names = array();
                        try {
                            $bundleNames = FileUtils::scanBundle($file);
                        }
                        catch (FileException $fe){
                            $request->flash();
                            return view('project.create')->withErrors([
                                'Error checking if file is a bundle:' . $file->getClientOriginalName(),
                                $fe->getMessage(),
                            ]);
                        }

                        if ($bundleNames) {
                            try {
                                $bundleNames = FileUtils::storeBundleAs($file, 'files/' . $projectId);
                            }
                            catch (FileException $fe){
                                DB::rollBack();
                                FileUtils::deleteDirectory($file, 'files/' . $projectId);
                                $request->flash();
                                return view('project.create')->withErrors([
                                    'Error extracting bundle file:' . $file->getClientOriginalName(),
                                    $fe->getMessage(),
                                ]);
                            }
                            $names = array_merge($names, $bundleNames);

                        } else {
                            try {
                                $path = FileUtils::storeAs($file, 'files/' . $projectId);
                            }
                            catch (FileException $fe){
                                DB::rollBack();
                                FileUtils::deleteDirectory($file, 'files/' . $projectId);
                                $request->flash();
                                return view('project.create')->withErrors([
                                    'Error extracting project file:' . $file->getClientOriginalName(),
                                    $fe->getMessage(),
                                ]);
                            }
                            if ($file->getMimeType() == 'application/zip' ) {
                                try {
                                    FileUtils::zipToTgz($path);
                                }
                                catch (FileException $fe){
                                    DB::rollBack();
                                    FileUtils::deleteDirectory($file, 'files/' . $projectId);
                                    $request->flash();
                                    return view('project.create')->withErrors([
                                        'Error extracting zip file:' . $file->getClientOriginalName(),
                                        $fe->getMessage(),
                                    ]);
                                }
                                $name = str_replace('.zip', '', $file->getClientOriginalName());
                            }
                            else{
                                $name = str_replace('.tar.gz', '', $file->getClientOriginalName());
                            }
                            array_push($names, $name);
                        }
                        $createdNames = array_merge($createdNames, $names);
                        foreach ($names as $transcriptionName){
                            try {
                                Transcription::create($projectId, $transcriptionName);
                            } catch (FileException $fe) {
                                DB::rollBack();
                                $request->flash();
                                return view('project.create')->withErrors([
                                    'Error creating transcription: \'' . $file->getClientOriginalName() . '\'',
                                    $fe->getMessage(),
                                ]);
                            };

                        }
                    }
                    else{
                        DB::rollBack();
                        $request->flash();
                        return view('project.create')->withErrors([
                            "Upload of file '" . $file->getClientOriginalName(). "' invalid.",
                        ]);
                    }
                }
            }
        }
        else{
            DB::rollBack();
            $request->flash();
            return view('project.create', [
                'errors' => new MessageBag(['Error creating project']),
            ]);
        }

        DB::commit();

        $project = Project::getByAdmin($projectId);

        $results = [
            'Project ' . $project->code . ' created successfully',
        ];
        if(count($createdNames) > 0){
            $results[]= 'Created ' . count($createdNames) . ' results: ' . implode(',', $createdNames);
        }
        return redirect()->route('project_manage', [
            'results' => $results
        ]);
    }

    public function showCreateForm(Request $request){
        return view('project.create');
    }

    public function all(Request $request){
        $this->validate($request, [
            'results' => 'array',
        ]);

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE) {
            $projects = Project::all();
        }
        else{
            $projects = Project::getAllByUser(Auth::user()->id);
        }
        $params = [
            'projects' => $projects,
            'results' => $request->get('results'),
        ];
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
            'uploadErrors' => 'array',
            'results' => 'array',
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

        if($request->has('uploadErrors')){
            $uploadErrors = $request->get('uploadErrors');
        }
        else{
            $uploadErrors = [];
        }

        if($request->has('results')){
            $uploadResults = $request->get('results');
        }
        else{
            $uploadResults = [];
        }

        //print_r($uploadErrors);

        $transcriptions = Transcription::all($request->get('id'), $query, $filters, $searchType, $pagesize, $orderBy, $orderType);
        return view('project.edit',[
            'project' => $project,
            'transcriptions' => $transcriptions,
            'query' => $query,
            'pagesize' => $pagesize,
            'orderBy' => $orderBy,
            'orderType' => $orderType,
            'filters' => $filters,
            'searchType' => $searchType,
            'results' => $uploadResults,
        ])->withErrors($uploadErrors);
    }

    public function save(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'description' => 'required|string',
            'bundle' => 'in:1',
            'files.*' => 'file|mimetypes:application/zip,application/x-gzip',
            'update' =>'boolean'
        ]);


        if($validator->fails()){
            return redirect()->route('project_edit_form', [
                'id' => $request->get('id'),
                'uploadErrors' => $validator->errors()->all(),
            ]);
        }

        $bundleNames = FALSE;

        //Check if files need to be overwritten
        $updatedNames = [];
        $createdNames = [];

        if(is_array($request->file('files'))){

            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $names = array();
                    try {
                        $bundleNames = FileUtils::scanBundle($file);
                    }
                    catch (FileException $fe){
                        return redirect()->route('project_edit_form', [
                            'id' => $request->get('id'),
                            'uploadErrors' => [
                                'Error checking for bundle file:' . $file->getClientOriginalName(),
                                $fe->getMessage(),
                            ]
                        ]);
                    }

                    //This is a standalone project
                    if (!$bundleNames) {
                        if ($file->getMimeType() == 'application/zip') {
                            $name = str_replace('.zip', '', $file->getClientOriginalName());
                        } else {
                            $name = str_replace('.tar.gz', '', $file->getClientOriginalName());
                        }
                        array_push($names, $name);
                    }
                    else if(is_array($bundleNames)){
                        $names = array_merge($names, $bundleNames);
                    }
                    else {
                        throw new \Exception("Something weird happend checking files");
                    }

                    foreach ($names as $name){
                        if(Transcription::all($request->get('id'), $name, '', 'exact' )->total() === 0){
                            $createdNames[] = $name;
                        }
                        else{
                            $updatedNames[] = $name;
                        }
                    }
                }
            }
            if(!$request->has('update') && count($updatedNames) > 0){
                $warnings = [
                    'Saving this project will update ' . count($updatedNames) . ' and create ' . count($createdNames) . ' results.',
                    'Will update: ' . implode(', ', $updatedNames),

                ];
                if(count($createdNames) > 0){
                    $warnings[] = 'Will create: ' . implode(', ', $createdNames);
                }

                $warnings[] = 'Check \'update any existing results with the same name\' and select the files to upload again to proceed.';

                $request->flash();
                return redirect()->route('project_edit_form', [
                    'id' => $request->get('id'),
                    'uploadErrors' => $warnings
                ]);
            }
        }

        DB::beginTransaction();

        Project::save($request->get('id'), $request->get('name'), $request->get('description'));

        if(is_array($request->file('files'))) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $names = array();
                    try {
                        $bundleNames = FileUtils::scanBundle($file);
                    }
                    catch (FileException $fe){
                        $request->flash();
                        return redirect()->route('project_edit_form', [
                            'id' => $request->get('id'),
                            'uploadErrors' => [
                                'Error checking if file is a bundle:' . $file->getClientOriginalName(),
                                $fe->getMessage(),
                            ]
                        ]);
                    }

                    if ($bundleNames) {
                        try {
                            $bundleNames = FileUtils::storeBundleAs($file, 'files/' . $request->get('id'), $request->has('update'));
                        }
                        catch (FileException $fe){
                            DB::rollBack();
                            FileUtils::deleteDirectory($file, 'files/' . $request->get('id'));
                            $request->flash();
                            return redirect()->route('project_edit_form', [
                                'id' => $request->get('id'),
                                'uploadErrors' => [
                                    'Error extracting bundle file:' . $file->getClientOriginalName(),
                                    $fe->getMessage(),
                                ]
                            ]);
                        }
                        $names = array_merge($names, $bundleNames);

                    } else {
                        try {
                            $path = FileUtils::storeAs($file, 'files/' . $request->get('id'), $request->has('update'));
                        }
                        catch (FileException $fe) {
                            DB::rollBack();
                            FileUtils::deleteDirectory($file, 'files/' . $request->get('id'));
                            $request->flash();
                            return redirect()->route('project_edit_form', [
                                'id' => $request->get('id'),
                                'uploadErrors' => [
                                    'Error extracting project file:' . $file->getClientOriginalName(),
                                    $fe->getMessage(),
                                ]
                            ]);
                        }
                        if ($file->getMimeType() == 'application/zip') {
                            try {
                                FileUtils::zipToTgz($path);
                            }
                            catch (FileException $fe){
                                DB::rollBack();
                                FileUtils::deleteDirectory($file, 'files/' . $request->get('id'));
                                $request->flash();
                                return redirect()->route('project_edit_form', [
                                    'id' => $request->get('id'),
                                    'uploadErrors' => [
                                        'Error extracting zip file:' . $file->getClientOriginalName(),
                                        $fe->getMessage(),
                                    ]
                                ]);
                            }
                            $name = str_replace('.zip', '', $file->getClientOriginalName());
                        } else {
                            $name = str_replace('.tar.gz', '', $file->getClientOriginalName());
                        }
                        array_push($names, $name);
                    }
                    foreach ($names as $transcriptionName) {
                        try {
                            Transcription::createIfNotExists($request->get('id'), $transcriptionName);

                        } catch (FileException $fe) {
                            DB::rollBack();
                            FileUtils::deleteDirectory($file, 'files/' . $request->get('id'));
                            $request->flash();
                            return redirect()->route('project_edit_form', [
                                'id' => $request->get('id'),
                                'uploadErrors' => [
                                    'Error saving transcription:' . $file->getClientOriginalName(),
                                    $fe->getMessage(),
                                ]
                            ]);
                        };

                    }
                } else {
                    DB::rollBack();
                    throw new FileException("Upload of file '" . $file->getClientOriginalName() . "' invalid.");
                }
            }
        }

        DB::commit();
        $project = Project::getByAdmin($request->get('id'));

        $results = [
            'Project ' . $project->code . ' updated successfully',
        ];
        if(count($createdNames) > 0){
            $results[]= 'Created ' . count($createdNames) . ' results: ' . implode(',', $createdNames);
        }
        if(count($updatedNames) > 0){
            $results[]= 'Updated ' . count($updatedNames) . ' results: ' . implode(',', $updatedNames);
        }
        return redirect()->route('project_edit_form', [
            'id' => $request->get('id'),
            'results' => $results
        ]);
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
