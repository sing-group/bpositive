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

namespace App\Models;


use Illuminate\Support\Facades\DB;

class Project
{

    public static function all(){

        $projects = DB::table('project')
            ->where('deleted', '=', '0')
            ->get();

        return $projects;
    }

    public static function get($id){

        $project = DB::table('project')
            ->where('deleted', '=', '0')
            ->where('id', '=', $id)
            ->where('public', '=', '1')
            ->first();

        return $project;
    }

    public static function getByCode($code){

        $project = DB::table('project')
            ->where('deleted', '=', '0')
            ->where('code', '=', $code)
            ->where('public', '=', '1')
            ->first();

        return $project;
    }

    public static function getByTranscription($id, $public){

        $project = DB::table('project')
            ->select(DB::raw('project.*'))
            ->join('transcription', 'transcription.projectId', '=', 'project.id')
            ->where('project.deleted', '=', '0')
            ->where('transcription.id', '=', $id)
            ->where('project.public', '=', $public)
            ->first();

        return $project;
    }

    public static function setPublic($id, $value){

        DB::table('project')
            ->where('id', '=', $id)
            ->update(['public' => $value]);

    }

    public static function getPrivate($id){

        $project = DB::table('project')
            ->where('deleted', '=', '0')
            ->where('id', '=', $id)
            ->where('public', '=', '0')
            ->first();

        return $project;
    }
}
