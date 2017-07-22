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
                    <a class="btn btn-default" href="/project/manage">Back</a>
                </div>
            </div>
            <h1>Edit project</h1>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/project/save') }}" enctype="multipart/form-data">
                    {{ Form::hidden('id', $project->id) }}
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" value="{{ (old('name') ? old('name') : $project->name )}}" required autofocus>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="description" class="col-md-4 control-label">Description</label>

                        <div class="col-md-6">
                            <textarea id="description" class="form-control" name="description" required autofocus>{{ (old('description') ? old('description') : $project->description) }}</textarea>

                            @if ($errors->has('description'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('files') ? ' has-error' : '' }}">
                        <label for="files" class="col-md-4 control-label">Add files</label>

                        <div class="col-md-6">
                            <input id="files" type="file" class="form-control" name="files[]" accept=".tar.gz, application/tar+gzip, .zip, application/zip" autofocus multiple/>

                            @if ($errors->has('files'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('files') }}</strong>
                                </span>
                            @endif
                        </div>
                        <span class="modalIcon" onclick="$('#modalInfo').modal('show');">
                            <span class="glyphicon glyphicon-question-sign text-primary"></span>
                        </span>
                    </div>

                    <div class="form-group{{ $errors->has('update') ? ' has-error' : '' }}">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-6">
                            <label for="update" class="form-check-label control-label">
                                <input name="update" type="checkbox" value="1">
                                Update any existing results with the same name
                            </label>
                        </div>
                        @if ($errors->has('update'))
                            <span class="help-block">
                                <strong>{{ $errors->first('update') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"
                                    data-toggle="modal" data-target="#modalProgressBar" data-backdrop="static" data-keyboard="false">
                                Save
                            </button>
                        </div>
                    </div>
                    {{ Form::close() }}

                    <div class="navbar navbar-default">
                        <div class="container-fluid">
                            {{ Form::open(['class' => '', 'method' => 'get', 'id' => 'queryForm']) }}
                            <div class="">
                                <ul class="nav navbar-nav navbar-left">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Filters <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-form" id="filters">
                                            <li>
                                                <a>
                                                    {{ Form::radio('filters[]', 'pss', (is_array($filters) ? in_array('pss', $filters): false), ['id' => 'filtersPSS']) }}
                                                    {{ Form::label('filtersPSS', 'Positively Selected') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a>
                                                    {{ Form::radio('filters[]', 'analyzed', (is_array($filters) ? in_array('analyzed', $filters): false), ['id' => 'filtersAnalyzed']) }}
                                                    {{ Form::label('filtersAnalyzed', 'Analyzed') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a>
                                                    {{ Form::radio('filters[]', 'notAnalyzed', (is_array($filters) ? in_array('notAnalyzed', $filters): false), ['id' => 'filtersNotAnalyzed']) }}
                                                    {{ Form::label('filtersNotAnalyzed', 'Not Analyzed') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a>
                                                    {{ Form::radio('filters[]', 'all', (is_array($filters) ? in_array('all', $filters): true), ['id' => 'filtersAll'], ['id' => 'filtersAll']) }}
                                                    {{ Form::label('filtersAll', 'All') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <div class="navbar-form navbar-right">
                                    <div class="input-group">
                        <span class="input-group-addon">
                            {{ Form::label('pagesize', 'Rows by page ') }}
                        </span>
                                        <span class="input-group-btn">
                            <span class="btn-group">
                                {{ Form::select('pagesize', array('10' => '10', '25' => '25', '50' => '50', '100' => '100'), $pagesize, ['class' => 'form-control']) }}
                            </span>
                        </span>
                                    </div>
                                    <div class="input-group">
                        <span class="input-group-addon">
                            {{ Form::label('search', 'Search ') }}
                        </span>
                                        {{ Form::input('search', 'query', $value = $query, ['class' => 'form-control', 'placeholder' => 'Type your query here', 'id' => 'querySearch']) }}

                                        <span class="input-group-btn">
                            <span class="btn-group">
                                {{ Form::select('searchType', array('contains' => 'contains', 'regexp' => 'regexp', 'exact' => 'exact'), $searchType, ['class' => 'form-control', 'id' => 'searchType']) }}
                            </span>
                            <span class="btn-group">
                                {{ Form::button('<span class="glyphicon glyphicon-remove"></span>', ['type' => 'button', 'class' => 'btn btn-default btn-block', 'id' => 'resetSearch']) }}
                            </span>
                            <span class="btn-group">
                                {{ Form::button('<span class="glyphicon glyphicon-search"></span>', ['type' => 'submit', 'class' => 'btn btn-primary btn-block']) }}
                            </span>
                        </span>
                                    </div>
                                </div>
                            </div>
                            {{ Form::hidden('id', $project->id) }}
                            {{ Form::hidden('orderBy', $orderBy) }}
                            {{ Form::hidden('orderType', $orderType) }}
                            {{ Form::close() }}
                        </div>
                    </div>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <!--th>Description</th-->
                            <th>Creation Date</th>
                            <!--th>View</th-->
                            <th>Download</th>
                            <th>Analyzed</th>
                            <th>Positively Selected</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transcriptions as $transcription)
                            <tr>
                                <td>{{$transcription->id}}</td>
                                <td>{{$transcription->name}}</td>
                                <!--td>{{$transcription->description}}</td-->
                                <td>{{$transcription->creationDate}}</td>
                                <!--td><a href="../transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a></td-->
                                <td><a href="../download/transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i></a></td>
                                <td>{{($transcription->analyzed?'Yes':'No')}}</td>
                                <td>{{($transcription->positivelySelected?'Yes':'No')}}</td>
                                <td>
                                    {{ Form::open(['action' => 'Bpositive\TranscriptionController@remove', 'method' => 'post', 'class' => 'frmDelete']) }}
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{$transcription->id}}" />
                                    {{ Form::button('<span class="glyphicon glyphicon-remove"></span>', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$transcriptions->appends(['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => $orderBy, 'orderType' => $orderType, 'filters' => $filters])->links()}}
                    <div class="alert alert-info">Showing {{$transcriptions->firstItem()}} to {{$transcriptions->lastItem()}} of {{$transcriptions->total()}} entries.</div>

                </form>
            </div>
        </div>
        @include('includes.progress')
        @include('includes.modalInfo', ['modalInfo' => 'Uploaded files can be ADOPS project files in ".zip" or ".tar.gz" format or a compressed file (".zip" or ".tar.gz") with the folders of the ADOPS projects inside in the first level.'])
    </div>
@endsection
@section('endscripts')
    <script type="text/javascript">
        $(window).on('load', function () {
            $('#pagesize').on('change', function(e) {
                $(this).closest('form').submit();
            });
            $('#filters :radio').on('change', function(e) {
                $(this).closest('form').submit();
            });
            $('#resetSearch').on('click', function(e) {
                $('#querySearch').val('');
                $('#filtersAll').prop("checked", true);
                $('#searchType').val('contains');
                $(this).closest('form').submit();
            });

            $('#querySearch').autocomplete({
                source: function(request, response) {
                    var url = '{{URL::route('transcription_name')}}';

                    $.getJSON(url, {
                        id: {{$project->id}},
                        query: request.term.split(/,\s*/).pop(),
                        searchType: $('#searchType option:selected').val(),
                        'filter[]': $('input[name="filters[]"]:checked').val()
                    }, response);
                },
                minLength: 1,
                select: function(event, ui) {
                    $('#querySearch').val(ui.item.value);
                    $('#searchType').val('exact');
                    $('#queryForm').submit();
                }
            })
        });
        $('.frmDelete').submit(function () {
            var res = confirm('Do you want to delete transcription?');
            return res;
        });
    </script>
@endsection('endscripts')

