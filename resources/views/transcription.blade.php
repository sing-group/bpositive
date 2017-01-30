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
        <div class="page-header">
          <div class="btn-toolbar pull-right">
            <div class="btn-group">
              <a class="btn btn-default" href="{{URL::previous()}}">Back</a>
            </div>
          </div>
          <h1>{{$transcription->name}}</h1>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">Notes</a></li>
            <li role="presentation"><a href="#log" aria-controls="log" role="tab" data-toggle="tab">Execution Log</a></li>
            <li role="presentation"><a href="#alnFile" aria-controls="alnFile" role="tab" data-toggle="tab">ALN File</a></li>
            <li role="presentation"><a href="#alnNucl" aria-controls="alnNucl" role="tab" data-toggle="tab">Aligned Nucl</a></li>
            <li role="presentation"><a href="#alnAmin" aria-controls="alnAmin" role="tab" data-toggle="tab">Aligned Amin</a></li>
            <li role="presentation"><a href="#tree" aria-controls="tree" role="tab" data-toggle="tab">Tree</a></li>
            @if(isset($newicks))
                <li role="presentation"><a href="#treeView" aria-controls="treeView" role="tab" data-toggle="tab">Tree View</a></li>
            @endif
            <li role="presentation"><a href="#psrf" aria-controls="psrf" role="tab" data-toggle="tab">PSRF</a></li>
            <li role="presentation"><a href="#codemlOutput" aria-controls="codemlOutput" role="tab" data-toggle="tab">Codeml Output</a></li>
            <li role="presentation"><a href="#codemlSummary" aria-controls="codemlSummary" role="tab" data-toggle="tab">Codeml Summary</a></li>
            <li role="presentation"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">Summary</a></li>
            @if(isset($confidences))
                <li role="presentation"><a href="#pss" aria-controls="pss" role="tab" data-toggle="tab">PSS</a></li>
            @endif
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="notes">
                <pre>{{$textFiles['notes']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="log">
                <pre>{{$textFiles['log']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="alnFile">
                <pre>{{$textFiles['alnFile']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="alnNucl">
                <pre>{{$textFiles['alnNucl']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="alnAmin">
                <pre>{{$textFiles['alnAmin']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tree">
                <pre>{{$textFiles['tree']}}</pre>
            </div>
            @if(isset($newicks))
                <div role="tabpanel" class="tab-pane fade" id="treeView">

                    <ul id="svgMenu" class="nav nav-tabs" role="tablist">
                    </ul>
                    <div id="svgCanvas" role="tabpanel" class="tab-content">

                    </div>
                </div>
            @endif
            <div role="tabpanel" class="tab-pane fade" id="psrf">
                <pre>{{$textFiles['psrf']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="codemlOutput">
                <pre>{{$textFiles['codemlOutput']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="codemlSummary">
                <pre>{{$textFiles['codemlSummary']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="summary">
                <pre>{{$textFiles['summary']}}</pre>
            </div>
            @if(isset($confidences))
                <div role="tabpanel" class="tab-pane fade" id="pss">
                    <div id="pssCanvas">

                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('endscripts')
    <script type="text/javascript" src="{{URL::asset('js/raphael-min.js')}}" ></script>
    <script type="text/javascript" src="{{URL::asset('js/jsphylosvg-min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/jspdf.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/html2pdf.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/bpositive/pss.js')}}"></script>
    <script type="text/javascript">
        //TODO: Refactor
        $(window).on('load', function () {
            @if(isset($newicks))
                var dataObjects = {!! $newicks !!};

                for(var i=0; i < dataObjects.length; i++ ){

                    var divName = 'svgCanvas' + i;

                    var svgCanvas = $("#svgCanvas");
                    if(i == 0){
                        $("#svgMenu").append('<li role="presentation" class="active"><a href="#tab' + divName + '" role="tab" data-toggle="tab">Phylogeny ' + i + '</a></li>');
                        svgCanvas.append('<div id="tab' + divName + '" class="tab-pane fade in active">' +
                            '<div class="navbar navbar-default"><div class="container-fluid"><form class="navbar-form"><a id="btn' + divName + '" type="button" class="btn btn-info form-control" download="Phylogeny'+ i + '.svg">Download as SVG</a></form></div></div>' +
                            '<div id="' + divName + '"></div>' +
                            '</div>');
                    }
                    else {
                        $("#svgMenu").append('<li role="presentation"><a href="#tab' + divName + '" role="tab" data-toggle="tab">Phylogeny ' + i + '</a></li>');
                        svgCanvas.append('<div id="tab' + divName + '" class="tab-pane fade in">' +
                            '<div class="navbar navbar-default"><div class="container-fluid"><form class="navbar-form"><a id="btn' + divName + '" type="button" class="btn btn-info form-control" download="Phylogeny'+ i + '.svg">Download as SVG</a></form></div></div>' +
                            '<div id="' + divName + '"></div>' +
                            '</div>');
                    }

                    var phylocanvas = new Smits.PhyloCanvas(
                        dataObjects[i],		// Newick or XML string
                        divName,	// Div Id where to render
                        800, 800		// Height, Width in pixels

                        //'circular'
                    );

                    //Gets SVG data and removes all non ASCII characters, because they are problematic for some viewers
                    $('#btn' + divName).attr('href', 'data:image/svg+xml;base64,' + btoa(phylocanvas.getSvgSource().replace(/[^\x00-\x7F]/g, "")));

                    //TODO: workaround for svg starting with an offset
                    var svg = $('#' + divName + ' svg');
                    svg.height(svg.height() + 200);
                    svg.addClass('center-block');

                }
            @endif

            @if(isset($confidences))
                var pss = new PSS(
                    {!!$transcription->getJSON()!!},
                    {!!$confidences->getJSONSequences()!!},
                    {!!$confidences->getJSONModels()!!},
                    {!!$scores!!},
                    'pssCanvas',
                    '{{URL::asset('images/bpositive.png')}}'
                );
                pss.getPSS();
            @endif
        })
    </script>
@endsection('endscripts')
