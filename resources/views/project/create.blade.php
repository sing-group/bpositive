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
            <h1>Create new Dataset</h1>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/project/create') }}" enctype="multipart/form-data" id="formCreate">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-4 control-label">Name</label>

                        <div class="col-md-6">
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus />

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description" class="col-md-4 control-label">Description</label>

                        <div class="col-md-6">
                            <textarea id="description" class="form-control" name="description" autofocus>{{ old('description') }}</textarea>

                            @if ($errors->has('description'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('files') ? ' has-error' : '' }}">
                        <label for="files" class="col-md-4 control-label">Files</label>

                        <div class="col-md-6">
                            <input id="files" type="file" class="form-control" name="files[]" accept=".tar.gz, application/tar+gzip, .zip, application/zip" autofocus multiple/>

                            @if ($errors->has('files'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('files') }}</strong>
                                </span>
                            @endif
                        </div>
                        <span class="modalIcon">
                            <span class="glyphicon glyphicon-question-sign text-primary"></span>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"
                                    >
                                Create
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @include('includes.progress')
    @include('includes.modalInfo', ['modalInfo' => 'Uploaded files can be ADOPS project files in ".zip" or ".tar.gz" format or a compressed file (".zip" or ".tar.gz") with the folders of the ADOPS projects inside in the first level.'])
    </div>
@endsection
