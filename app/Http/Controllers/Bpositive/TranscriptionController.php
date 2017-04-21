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
use Illuminate\Http\Request;
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

    }

    public function all(Request $request){

        $this->validate($request, [
            'id' => 'required_without:code|numeric',
            'code' => 'required_without:id|string|regex:/^BP\d{10}$/',
            'query' => 'string',
            'page' => 'numeric',
            'pagesize' => 'numeric',
            'orderBy' => Rule::in(['name', 'description', 'analyzed', 'positivelySelected']),
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
            if($project == null && ($request->session()->get('allowPrivateAccessToId') == $request->get('id') || Gate::allows('access-private'))){
                $project = Project::getPrivate($request->get('id'));
            }
        }
        else{
            $project = Project::getByCode($request->get('code'));
        }

        if($project == null){
            throw new PrivateException();
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
            if($request->session()->get('allowPrivateAccessToId') == $project->id || Gate::allows('access-private')){
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
                'errors' => new MessageBag([$e->getMessage()])
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
                if($request->session()->get('allowPrivateAccessToId') == $project->id || Gate::allows('access-private')){
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
            if($request->session()->get('allowPrivateAccessToId') == $request->get('id') || Gate::allows('access-private')){
                $project = Project::getPrivate($request->get('id'));
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
}
