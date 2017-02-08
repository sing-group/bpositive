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

@section('title', 'Transcriptions')
{{--
@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection
--}}
@section('content')

    <div class="project-content">
        <div class="page-header">
          <div class="btn-toolbar pull-right">
            <div class="btn-group">
              <a class="btn btn-default" href="/">Back</a>
            </div>
          </div>
          <h1>{{$project->name}}</h1>
        </div>

        <div class="navbar navbar-default">
            <div class="container-fluid">
                {{ Form::open(['class' => 'navbar-form navbar-right', 'method' => 'get', 'autocomplete' => 'on']) }}
                <div class="form-group">
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
                        {{ Form::input('search', 'query', $value = $query, ['class' => 'form-control', 'placeholder' => 'Type your query here', 'id' => 'querySearch', 'autocomplete' => 'on']) }}

                        <span class="input-group-btn">
                            <span class="btn-group">
                                {{ Form::button('<span class="glyphicon glyphicon-remove"></span>', ['type' => 'button', 'class' => 'btn btn-default btn-block', 'id' => 'resetSearch']) }}
                            </span>
                            <span class="btn-group">
                                {{ Form::button('<span class="glyphicon glyphicon-search"></span>', ['type' => 'submit', 'class' => 'btn btn-primary btn-block']) }}
                            </span>
                        </span>
                    </div>
                </div>
                {{ Form::hidden('id', $project->id) }}
                {{ Form::close() }}
            </div>
        </div>

        @if ($transcriptions->count() > 0)
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Result</th>
                    <th>View</th>
                    <th>Download</th>
                </tr>
                </thead>
                @foreach($transcriptions as $transcription)
                    <tr>
                        <td>{{$transcription->name}}</td>
                        <td>
                            @if($transcription->analyzed == 0)
                                <span>Not Analyzed</span>
                            @elseif ($transcription->positivelySelected == 0)
                                <span class="text-danger">Analyzed</span>
                            @else
                                <span class="text-success">Positively Selected</span>
                            @endif
                        </td>
                        <td><a href="transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a></td>
                        <td><a href="download/transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i></a></td>
                    </tr>
                @endforeach
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Result</th>
                    <th>View</th>
                    <th>Download</th>
                </tr>
                </tfoot>
            </table>
            {{$transcriptions->appends(['id' => $project->id, 'pagesize' => $pagesize])->links()}}
            <div class="alert alert-info">Showing {{$transcriptions->firstItem()}} to {{$transcriptions->lastItem()}} of {{$transcriptions->total()}} entries.</div>
        @elseif($query)
            <div class="alert alert-info">There are no results for this query.</div>
        @else
            <div class="alert alert-info">There are no transcriptions in this project.</div>
        @endif
    </div>
@endsection

@section('endscripts')
    <script type="text/javascript">
        //TODO: Refactor
        $(window).on('load', function () {
            $('#pagesize').on('change', function(e) {
                $(this).closest('form').submit();
            });
            $('#resetSearch').on('click', function(e) {
                $('#querySearch').val('');
            });
        });
    </script>
@endsection('endscripts')