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


use App\Utils\FileUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Project
{

    public static function all(){

        $projects = DB::table('project')->select(DB::raw('project.*, users.email'))
            ->leftJoin('users_projects', 'project.id', '=', 'users_projects.projectId')
            ->leftJoin('users', 'users.id', '=', 'users_projects.userId')
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

    public static function getByAdmin($id){

        $project = DB::table('project')
            ->where('id', '=', $id)
            ->first();

        return $project;
    }

    public static function getByUser($userId, $projectId){

        $projects = DB::table('project')
            ->join('users_projects', 'project.id', '=', 'users_projects.projectId')
            ->where('project.deleted', '=', '0')
            ->where('users_projects.userId', '=', $userId)
            ->where('project.id', '=', $projectId)
            ->first();

        return $projects;
    }

    public static function getByCode($code){

        $project = DB::table('project')
            ->where('deleted', '=', '0')
            ->where('code', '=', $code)
            ->where('public', '=', '1')
            ->first();

        return $project;
    }

    public static function getAllByUser($id){

        $projects = DB::table('project')->select(DB::raw('project.*, users.email'))
            ->leftJoin('users_projects', 'project.id', '=', 'users_projects.projectId')
            ->leftJoin('users', 'users.id', '=', 'users_projects.userId')
            ->where('project.deleted', '=', '0')
            ->where('users_projects.userId', '=', $id)
            ->get();

        return $projects;
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

    public static function setPrivate($id, $password){

        DB::statement('update project set public = 0, privatePassword = ? where id = ?',[$password, $id]);

    }

    public static function getPrivate($id){

        $project = DB::table('project')
            ->where('deleted', '=', '0')
            ->where('id', '=', $id)
            ->where('public', '=', '0')
            ->first();

        return $project;
    }

    public static function create($userId, $name, $description){

        $year = Carbon::now()->year;
        $code = $year.str_pad('1', 6, '0', STR_PAD_LEFT);
        $last_project_code = DB::table('project')
            ->where('code', 'like', 'BP'.$year.'%')
            ->orderBy('code', 'desc')
            ->pluck('code')
            ->first();

        if($last_project_code) {
            preg_match('/[0-9]+/', $last_project_code, $last_id_code);
            $code = $last_id_code[0] + 1;
        }

        $project = DB::table('project')
            ->insertGetId([
                'name' => $name,
                'description' => $description,
                'deleted' => 0,
                'public' => 0,
                'code' => 'BP'.$code,
            ]);

        DB::table('users_projects')
            ->insert([
                'projectId' => $project,
                'userId' => $userId,
            ]);

        return $project;
    }

    public static function delete($id){

        DB::table('project')
            ->where('id', '=', $id)
            ->update(['deleted' => 1]);

        //FileUtils::deleteDirectory('files/'.$id.'/');
    }

    public static function deleteByUser($id, $userId){

        DB::table('project')
            ->join('users_projects', 'project.id', '=', 'users_projects.projectId')
            ->where('project.id', '=', $id)
            ->where('users_projects.userId', '=', $userId)
            ->update(['project.deleted' => 1]);

        //FileUtils::deleteDirectory('files/'.$id.'/');
    }

    public static function save($id, $name, $description){

        DB::table('project')
            ->where('id', '=', $id)
            ->update([
                'name' => $name,
                'description' => $description,
            ]);

    }

    public static function owns($userId, $projectId){

        $project = DB::table('users_projects')
            ->where('userId', '=', $userId)
            ->where('projectId', '=', $projectId)
            ->first();

        return !is_null($project);
    }

}
