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

use App\Exceptions\PrivateException;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Transcription;
use App\Providers\AuthServiceProvider;
use App\Utils\FileUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;

class TranscriptionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Transcription Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles requests from pages where transcriptions are managed
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('remove');
    }

    public function all(Request $request){

        $this->validate($request, [
            'id' => 'required_without:code|numeric',
            'code' => 'required_without:id|string|regex:/^BP\d{10}$/',
            'query' => 'string',
            'page' => 'numeric',
            'pagesize' => 'numeric',
            'orderBy' => Rule::in(['name', 'description', 'experiment', 'analyzed', 'positivelySelected']),
            'orderType' => Rule::in(['asc', 'desc']),
            'filters.*' => Rule::in(['pss', 'analyzed', 'notAnalyzed', 'all']),
            'searchType' => Rule::in(['contains', 'regexp', 'exact']),
        ]);

        $query = $request->get('query', '');
        $pagesize = $request->get('pagesize', '10');
        $orderBy = $request->get('orderBy', 'name');
        $orderType = $request->get('orderType', 'asc');
        $filters = $request->get('filters');
        $searchType = $request->get('searchType');

        $project = null;
        if($request->has('id')) {
            $project = Project::get($request->get('id'));
            if($project == null && ($request->session()->get('allowPrivateAccessToId') == $request->get('id') || Gate::allows('access-private', $request->get('id')))){
                $project = Project::getPrivate($request->get('id'));
            }
        }
        else{
            $project = Project::getByCode($request->get('code'));
        }

        if ($project == null) {
            $aux = null;
            if ($request->has('id')) {
                $aux = Project::getByAdmin($request->get('id'));
            } else if ($request->has('code')) {
                $aux = Project::getByCodeAdmin($request->get('code'));
            }

            if ($aux != null) {
                return view('projectPrivate', [
                    'project' => $aux,
                    'state' => 'accessPrivate'
                ]);
            } else {
                throw new PrivateException();
            }
        }

        $transcriptions = Transcription::all($project->id, $query, $filters, $searchType, $pagesize, $orderBy, $orderType);

        return view('transcriptions',[
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

    public function get(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $transcription = null;
        $project = Project::getByTranscription($request->get('id'), '1');
        if(!$project){
            $project = Project::getByTranscription($request->get('id'), '0');
            if($request->session()->get('allowPrivateAccessToId') == $project->id || Gate::allows('access-private', $project->id)){
                $transcription = new Transcription(Transcription::getPrivate($request->get('id')));
            }
        }
        else{
            $transcription = new Transcription(Transcription::get($request->get('id')));
        }

        if($project == null || $transcription == null){
            throw new PrivateException();
        }

        try {
            return view('transcription', [
                'transcription' => $transcription,
                'newicks' => $transcription->getNewicks(),
                'confidences' => $transcription->getConfidences(),
                'scores' => json_encode($transcription->getScores()),
                'textFiles' => $transcription->getPlainTextFiles()
            ]);
        }
        catch(\Exception $e){
            return view('transcription', [
                'transcription' => $transcription,
                'errors' => new MessageBag([$e->getMessage()]),
                'textFiles' => $transcription->getPlainTextFiles()
            ]);

        }
    }

    public function download(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        try {
            $project = Project::getByTranscription($request->get('id'), '1');
            if(!$project){
                $project = Project::getByTranscription($request->get('id'), '0');
                if($request->session()->get('allowPrivateAccessToId') == $project->id || Gate::allows('access-private', $project->id)){
                    return response()->download(Transcription::getTgzPath($request->get('id'), '0'));
                }
            }
            else {
                return response()->download(Transcription::getTgzPath($request->get('id'), '1'));
            }
        } catch (\Exception $e) {
            $transcription = new Transcription(Transcription::get($request->get('id')));
            return view('transcription', [
                'transcription' => $transcription,
                'errors' => new MessageBag([$e->getMessage()])
            ]);

        }
        throw new PrivateException();
    }

    public function downloadMultiple(Request $request){

        $this->validate($request, [
            'transcriptions.*' => 'required|numeric'
        ]);

        $transcriptions = $request->get("transcriptions");

        $results = [];
        $paths = [];
        try {
            $baseName = '';
            foreach ($transcriptions as $transcription) {

                $project = Project::getByTranscription($transcription, '1');
                if (!$project) {
                    $project = Project::getByTranscription($transcription, '0');
                    if ($request->session()->get('allowPrivateAccessToId') == $project->id || Gate::allows('access-private', $project->id)) {
                        $results[] = Transcription::getByAdmin($transcription);
                        $paths[] = Transcription::getTgzPath($transcription, '0');
                    }
                } else {
                    $results[] = Transcription::get($transcription);
                    $paths[] = Transcription::getTgzPath($transcription, '1');
                }

                foreach ($results as $result){
                    if($baseName == ''){
                        $baseName = $result->name;
                    }
                    else if($baseName != $result->name){
                        throw new \Exception('Selected projects are not compatible');
                    }

                }

            }

            if(count($paths)) {
                return response()->download(FileUtils::getTgz($paths, $baseName))->deleteFileAfterSend(true);
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function findByName(Request $request) {
        $this->validate($request, [
            'id' => 'required|numeric',
            'query' => 'string',
            'filters.*' => Rule::in(['pss', 'analyzed', 'notAnalyzed', 'all']),
            'searchType' => Rule::in(['contains', 'regexp', 'exact']),
            'pagesize' => 'numeric'
        ]);

        $query = $request->get('query');
        $filters = $request->get('filters');
        $searchType = $request->get('searchType');
        $pagesize = $request->get('pagesize', '20');

        $project = null;
        if($request->has('id')) {
            if($request->session()->get('allowPrivateAccessToId') == $request->get('id') || Gate::allows('access-private', $request->get('id'))){
                $project = Project::getByAdmin($request->get('id'));
            }
            else {
                $project = Project::get($request->get('id'));
            }
        }

        if($project == null){
            throw new PrivateException();
        }

        $transcriptions = Transcription::all($project->id, $query, $filters, $searchType, $pagesize);

        $results = array();
        foreach ($transcriptions as $transcription) {
            $results[] = [ 'id' => $transcription->id, 'value' => $transcription->name ];
        }

        return response()->json($results);
    }

    public function remove(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE) {
            $projectId = Transcription::delete($request->get('id'));
        }
        else{
            $projectId = Transcription::deleteByUser($request->get('id'), Auth::user()->id );
        }

        return redirect()->route('project_edit_form',[
            'id' => $projectId,
        ]);
    }

    public function removeAll(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $project = Project::getByAdmin($request->get('id'));
        if($project->public) {
            return redirect()->route('project_edit_form', [
                'id' => $project->id,
                'results' => ["You are not allowed to delete this dataset because it is public"],
            ]);
        }

        if(Auth::user()->role_id == AuthServiceProvider::ADMIN_ROLE || Project::owns(Auth::user()->user_id, $project->id)) {

            $count = Transcription::deleteByProject($project->id);
            return redirect()->route('project_edit_form',[
                'id' => $request->get('id'),
                'results' => ['Deleted ' . $count . ' projects']
            ]);
        }

        return redirect()->route('project_edit_form',[
            'id' => $request->get('id'),
            'results' => ["You are not allowed to delete this dataset"],
        ]);
    }
}
