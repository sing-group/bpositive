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

    @foreach ($projects as $project)

        <div class="project">
            <div class="project_name col-md-4">
                <h1>{{$project->name}}</h1>
                <a href="transcriptions?id={{$project->id}}">
                    <button type="button"class="button_more btn btn-default btn-md"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                </a>
            </div>
            <div class="project_description col-md-8">
                {!!$project->description!!}
            </div>
            <div class="clear"></div>
        </div>

    @endforeach

@endsection