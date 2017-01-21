<?php

/*
 * B+
 * Copyright (C) 2017 Jorge Vieira, José Sousa, Miguel Reboiro-Jato,
 * Noé Vázquez, Bárbara Amorim, Cristina P. Vieira, André Torres, and
 * Florentino Fdez-Riverola
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

    <div class="project-content">
        <h1>{{$project->name}}</h1>
        {{ Form::open(['class' => 'form-inline', 'method' => 'get']) }}
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">
                    {{ Form::label('search', 'Search ') }}
                </span>
                {{ Form::input('search', 'query', $value = $query, ['class' => 'form-control', 'placeholder' => 'Type your query here']) }}
                <span class="input-group-btn">
                    {{ Form::button('<span class="glyphicon glyphicon-search"></span>', ['type' => 'submit', 'class' => 'btn btn-primary btn-block']) }}
                </span>
            </div>
        </div>
        {{ Form::hidden('id', $project->id) }}
        {{ Form::close() }}
        @if ($transcriptions->count() > 0)
            @foreach($transcriptions as $transcription)
                <li>{{$transcription->name}}</li>
            @endforeach
            {{$transcriptions->appends(['id' => $project->id])->links()}}
            <div class="alert alert-info">Showing {{$transcriptions->firstItem()}} to {{$transcriptions->lastItem()}} of {{$transcriptions->total()}} entries.</div>
        @elseif($query)
            <div class="alert alert-info">There are no results for this query.</div>
        @else
            <div class="alert alert-info">There are no transcriptions in this project.</div>
        @endif
    </div>


@endsection