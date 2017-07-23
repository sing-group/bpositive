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

?>

@extends('layouts.bpositive')

@section('content')
    <div class="project-content">
        <div class="page-header">
            <div class="btn-toolbar pull-right">
                <div class="btn-group">
                    <a class="btn btn-default" href="/">Back</a>
                </div>
            </div>
            <h1>Manage projects</h1>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h5 class="pull-left">Projects</h5>
                {{ Form::open(['action' => 'Bpositive\ProjectManagerController@showCreateForm', 'method' => 'get']) }}
                {{ csrf_field() }}
                {{ Form::button('New', ['type' => 'submit', 'class' => 'btn btn-primary pull-right']) }}
                {{ Form::close() }}
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Creation Date</th>
                        <th>Public</th>
                        <th>Edit</th>
                        <th>Remove</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td><a href="/transcriptions?id={{$project->id}}">{{$project->code}}</a></td>
                            <td>{{$project->name}}</td>
                            <td>{{(isset($project->email)?$project->email:'')}}</td>
                            <td>{{$project->creationDate}}</td>
                            <td>{{($project->public?'Yes':'No')}}</td>
                            <td>
                                {{ Form::open(['action' => 'Bpositive\ProjectManagerController@edit', 'method' => 'get', 'class' => 'frmEdit']) }}
                                {{ csrf_field() }}
                                {{ Form::hidden('id', $project->id) }}
                                {{ Form::button('<span class="glyphicon glyphicon-edit"></span>', ['type' => 'submit', 'class' => 'btn btn-info']) }}
                                {{ Form::close() }}
                            </td>
                            <td>
                                {{ Form::open(['action' => 'Bpositive\ProjectManagerController@remove', 'method' => 'post', 'class' => 'frmDelete']) }}
                                {{ csrf_field() }}
                                {{ Form::hidden('id', $project->id) }}
                                {{ Form::button('<span class="glyphicon glyphicon-remove"></span>', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('endscripts')
    <script type="application/javascript">
        $('.frmDelete').submit(function () {
            var res = confirm('Do you want to delete project?');
            return res;
        });
    </script>
@endsection
