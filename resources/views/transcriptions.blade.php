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
          <h1>{{$project->name}} ({{$project->code}})</h1>
        </div>

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

        @if ($transcriptions->count() > 0)
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'name', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'name')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'asc' : 'desc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>Experiment
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'experiment', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'experiment')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'asc' : 'desc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>Result
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'analyzed', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'analyzed')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'desc' : 'asc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>View</th>
                    <th>Download</th>
                </tr>
                </thead>
                @foreach($transcriptions as $transcription)
                    <tr>
                        <td>{{$transcription->name}}</td>
                        <td>{{$transcription->experiment}}</td>
                        <td>
                            @if($transcription->analyzed == 0)
                                <span>Not Analyzed</span>
                            @elseif ($transcription->positivelySelected == 0)
                                <span class="text-danger">Analyzed</span>
                            @else
                                <span class="text-success">Positively Selected</span>
                            @endif
                        </td>
                        @if($transcription->analyzed != 0)
                            <td><a href="transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a></td>
                            <td><a href="download/transcription?id={{$transcription->id}}"><i class="glyphicon glyphicon-cloud-download" aria-hidden="true"></i></a></td>
                        @else
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                @endforeach
                <tfoot>
                <tr>
                    <th>Name
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'name', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'name')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'asc' : 'desc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>Experiment
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'experiment', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'experiment')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'asc' : 'desc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>Result
                        <a href="{{route('transcriptions', ['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => 'analyzed', 'orderType' => ($orderType === 'asc'? 'desc' : 'asc'), 'filters' => $filters])}}">
                            @if($orderBy === 'analyzed')
                                <i class="fa fa-sort-alpha-{{($orderType === 'asc'? 'desc' : 'asc')}}" aria-hidden="true"></i>
                            @else
                                <i class="fa fa-sort" aria-hidden="true"></i>
                            @endif
                        </a>
                    </th>
                    <th>View</th>
                    <th>Download</th>
                </tr>
                </tfoot>
            </table>
            {{$transcriptions->appends(['id' => $project->id, 'query' => $query, 'pagesize' => $pagesize, 'orderBy' => $orderBy, 'orderType' => $orderType, 'filters' => $filters])->links()}}
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
    </script>
@endsection('endscripts')