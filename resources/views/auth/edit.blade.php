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
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <div class="btn-toolbar pull-right">
                    <div class="btn-group">
                        <a class="btn btn-default" href="/user/manage">Back</a>
                    </div>
                </div>
                <h1>Edit profile</h1>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ ($user->id === Auth::user()->id ? url('/user/saveOwn') : url('/user/save') ) }}">
                        {{ Form::hidden('id', $user->id) }}
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($user->id === Auth::user()->id)
                            <div class="form-group{{ $errors->has('old-password') ? ' has-error' : '' }}">
                                <label for="old-password" class="col-md-4 control-label">Old Password</label>

                                <div class="col-md-6">
                                    <input id="old-password" type="password" class="form-control" name="old-password">

                                    @if ($errors->has('old-password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('old-password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password">

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                </div>
                            </div>

                        @else
                            <div class="form-group">
                                <label for="role_id" class="col-md-4 control-label">Role</label>

                                <div class="col-md-6">
                                    <select id="role_id" class="form-control" name="role_id">
                                        <option value="{{\App\Providers\AuthServiceProvider::ADMIN_ROLE}}" {{($user->role_id == \App\Providers\AuthServiceProvider::ADMIN_ROLE?'selected':'')}}>Admin</option>
                                        <option value="{{\App\Providers\AuthServiceProvider::USER_ROLE}}" {{($user->role_id == \App\Providers\AuthServiceProvider::USER_ROLE?'selected':'')}}>User</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
