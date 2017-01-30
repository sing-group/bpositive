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
use Illuminate\Support\MessageBag;

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
            'id' => 'required|numeric',
            'query' => 'alpha_num',
            'page' => 'numeric'
        ]);

        $project = Project::get($request->get('id'));
        $transcriptions = Transcription::all($request->get('id'), $request->get('query', ''));

        return view('transcriptions',[
            'project' => $project,
            'transcriptions' => $transcriptions,
            'query' => $request->get('query', '')
        ]);
    }

    public function get(Request $request){

        $this->validate($request, [
            'id' => 'required|numeric'
        ]);

        $transcription = new Transcription(Transcription::get($request->get('id')));

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
            return response()->download(Transcription::getTgzPath($request->get('id')));
        }
        catch(\Exception $e){
            $transcription = new Transcription(Transcription::get($request->get('id')));
            return view('transcription', [
                'transcription' => $transcription,
                'errors' => new MessageBag([$e->getMessage()])
            ]);

        }
    }


}
