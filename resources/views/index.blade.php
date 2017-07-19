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

@section('title', 'Welcome')
{{--
@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection
--}}
@section('content')

    @foreach ($projects as $project)

        <div class="project">
            <div class="project_name col-md-4">
                <h1>{{$project->name}}</h1>
                {!! (\App\Models\Project::owns(Auth::user()->id, $project->id) ? '<p>(You are the owner)</p>': '') !!}
                @if ($project->public == 1)
                    <h4><a href="transcriptions?code={{$project->code}}">{{$project->code}}</a></h4>
                @elseif (Gate::allows('access-private', $project->id))
                    <h4>{{$project->code}}</h4>
                @endif
                @if ($project->public == 1 || Gate::allows('access-private', $project->id))
                    <div class="form-group">
                        {{ Form::open(['url' => 'transcriptions', 'method' => 'get', 'id' => 'openForm']) }}
                        {{ Form::hidden('id', $project->id) }}
                        {{ Form::button('<span class="glyphicon glyphicon-plus"></span> Open', ['type' => 'submit', 'class' => 'btn btn-default btn-md']) }}
                        {{ Form::close() }}
                    </div>
                @else
                    <h4>{{$project->code}}</h4>
                    <div class="form-group">
                        {{ Form::open(['action' => 'Bpositive\ProjectController@getPrivate', 'method' => 'post', 'id' => 'accessForm']) }}
                        {{ Form::hidden('id', $project->id) }}
                        {{ Form::hidden('state', 'accessPrivate') }}
                        {{ Form::button('<span class="glyphicon glyphicon-lock"></span> Access', ['type' => 'submit', 'class' => 'btn btn-default btn-md']) }}
                        {{ Form::close() }}
                    </div>
                @endif

                @if ($project->public == 0 && Gate::allows('make-public', $project->id))
                    <div class="form-group">
                        {{ Form::open(['action' => 'Bpositive\ProjectController@makePublic', 'method' => 'post', 'id' => 'publicForm']) }}
                        {{ Form::hidden('id', $project->id) }}
                        {{ Form::hidden('state', 'makePublic') }}
                        {{ Form::button('<span class="glyphicon glyphicon-globe"></span> Make public', ['type' => 'submit', 'class' => 'btn btn-primary btn-md']) }}
                        {{ Form::close() }}
                    </div>
                    <div class="form-group">
                        {{ Form::button('<span class="glyphicon glyphicon-lock"></span> Show password', ['type' => 'submit', 'class' => 'btn btn-info btn-md', 'id' => 'btnShPass'.$project->id]) }}
                    </div>
                    <div class="form-group" style="display:none;" id="grpHdPass{{$project->id}}">
                        <div class="form-group">
                            {{ Form::button('<span class="glyphicon glyphicon-lock"></span> Hide password', ['type' => 'submit', 'class' => 'btn btn-info btn-md', 'id' => 'btnHdPass'.$project->id]) }}
                        </div>

                        {{ Form::open(['action' => 'Bpositive\ProjectController@makePrivate', 'method' => 'post', 'id' => 'formChPass'.$project->id]) }}
                        <div class="form-group form-inline">
                            {{ Form::label('password', 'Password: ') }}
                            {{ Form::text('password', $project->privatePassword, ['required' => 'required', 'class' => 'form-control']) }}
                        </div>
                        {{ Form::hidden('id', $project->id) }}
                        {{ Form::hidden('state', 'makePrivate') }}
                        {{ Form::button('<span class="glyphicon glyphicon-globe"></span> Change password', ['type' => 'submit', 'class' => 'btn btn-warning btn-md']) }}
                        {{ Form::close() }}
                    </div>

                @endif

                @if ($project->public == 1 && Gate::allows('make-private', $project->id))
                    <div class="form-group">
                        {{ Form::button('<span class="glyphicon glyphicon-lock"></span> Make private', ['type' => 'submit', 'class' => 'btn btn-warning btn-md btnMkPrivate', 'id' => 'btnMkPrivate'.$project->id]) }}
                    </div>

                    {{ Form::open(['action' => 'Bpositive\ProjectController@makePrivate', 'method' => 'post', 'id' => 'formMkPrivate'.$project->id, 'style' => 'display:none;']) }}
                    <div class="form-group form-inline">
                        {{ Form::label('password', 'Password: ') }}
                        {{ Form::password('password', ['id' => 'password', 'required' => 'required', 'class' => 'form-control']) }}
                    </div>
                    {{ Form::hidden('id', $project->id) }}
                    {{ Form::hidden('state', 'makePrivate') }}
                    {{ Form::button('<span class="glyphicon glyphicon-globe"></span> Make private', ['type' => 'submit', 'class' => 'btn btn-warning btn-md']) }}
                    {{ Form::close() }}
                @endif
            </div>
            <div class="project_description col-md-8">
                {!!$project->description!!}
            </div>
            <div class="clear"></div>
        </div>

    @endforeach



@endsection

@section('endscripts')
    <script type="application/javascript">
        @foreach ($projects as $project)

            $('#btnMkPrivate{{$project->id}}').click({id: '{{$project->id}}'}, function(event){
                $('#btnMkPrivate{{$project->id}}').hide();
                $('#formMkPrivate{{$project->id}}').show();
            });

            $('#btnShPass{{$project->id}}').click({id: '{{$project->id}}'}, function(event){
                $('#btnShPass{{$project->id}}').hide();
                $('#grpHdPass{{$project->id}}').show();
            });

            $('#btnHdPass{{$project->id}}').click({id: '{{$project->id}}'}, function(event){
                $('#grpHdPass{{$project->id}}').hide();
                $('#btnShPass{{$project->id}}').show();
            });
        @endforeach
    </script>
@endsection
