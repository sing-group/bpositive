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

@section('title', 'Transcription')
{{--
@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection
--}}
@section('content')

    <div class="project-content">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#treeView" aria-controls="treeView" role="tab" data-toggle="tab">Tree View</a></li>
                <li role="presentation"><a href="#pss" aria-controls="pss" role="tab" data-toggle="tab">PSS</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="treeView">
                    <div id="svgCanvas">
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="pss">
                    <div id="pssCanvas" style="overflow:scroll">

                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="{{URL::asset('js/raphael-min.js')}}" ></script>
        <script type="text/javascript" src="{{URL::asset('js/jsphylosvg-min.js')}}"></script>
        <script type="text/javascript" src="{{URL::asset('js/bpositive/pss.js')}}"></script>
        <script type="text/javascript">
            //TODO: Refactor
            $(window).on('load', function () {
                var dataObjects = {!! $newicks !!};

                for(var i=0; i < dataObjects.length; i++ ){

                    var divName = 'svgCanvas' + i;
                    $("#svgCanvas").append('<div id="' + divName + '"></div>');

                    phylocanvas = new Smits.PhyloCanvas(
                        dataObjects[i],		// Newick or XML string
                        divName,	// Div Id where to render
                        800, 600		// Height, Width in pixels
                        //'circular'
                    );
                }

                pss = new PSS(
                    {!! $confidences->getJSONSequences()!!},
                    {!!$confidences->getJSONModels()!!},
                    {!!$confidences->getJSONMovedIndexes()!!},
                    'pssCanvas');
                pss.getPSS();
            })
        </script>
    </div>


@endsection