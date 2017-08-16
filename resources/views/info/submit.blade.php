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
            <h1>How to submit a Dataset?</h1>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h2>
                    In order to submit a new Dataset, the researcher should request new access credentials to an Administrator:
                    <a href="mailto:jbvieira@ibmc.up.pt?subject=Request account to upload to Bpositive&cc=info@i3s.up.pt" target="_blank">request access</a>
                </h2>
                <h3>
                    Once the account is created, you will be able to login and upload new Datasets.
                </h3>
            </div>
        </div>
    </div>
@endsection
