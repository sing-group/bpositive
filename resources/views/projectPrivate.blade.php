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

@section('title', 'Wellcome')
{{--
@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection
--}}
@section('content')
    <div class="page-header">
        <div class="btn-toolbar pull-right">
            <div class="btn-group">
                <a class="btn btn-default" href="/">Back</a>
            </div>
        </div>
        <h1>Make public {{$project->name}} ({{$project->code}})</h1>
    </div>
    <div class="project">
        <div class="project_name col-md-4">
            <h1>{{$project->name}}</h1>
            <h4><a href="transcriptions?code={{$project->code}}">{{$project->code}}</a></h4>
            @if ($project->public == 1)
                <p>Project is already public.</p>
            @else
                {{ Form::open(['action' => 'Bpositive\ProjectController@makePublic', 'method' => 'post', 'id' => 'publicForm']) }}

                <div class="form-group form-inline">
                    {{ Form::label('password', 'Password: ') }}
                    {{ Form::password('password', ['id' => 'password', 'required' => 'required', 'class' => 'form-control']) }}
                </div>
                {{ Form::hidden('id', $project->id) }}
                {{ Form::button('<span class="glyphicon glyphicon-globe"></span> Make public', ['type' => 'submit', 'class' => 'btn btn-primary btn-block']) }}

                {{ Form::close() }}
            @endif
        </div>
        <div class="project_description col-md-8">
            {!!$project->description!!}
        </div>
        <div class="clear"></div>
    </div>


@endsection