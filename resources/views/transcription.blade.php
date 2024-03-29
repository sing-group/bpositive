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

@section('title', 'Transcription')

@section('startcss')
    <link href="{{URL::asset('css/phyd3.min.css')}}" rel="stylesheet">
@endsection
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
            <h1>{{$transcription->name}} - {{$transcription->experiment}}</h1>
        </div>

        <ul class="nav nav-tabs" role="tablist" id="topTabs">
            @if(isset($confidences))
                <li role="presentation" class="active"><a href="#pss" aria-controls="pss" role="tab" data-toggle="tab">PSS</a></li>
            @endif
            <li role="presentation"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">Summary</a></li>
            <li role="presentation"><a href="#log" aria-controls="log" role="tab" data-toggle="tab">Execution Log</a></li>
            @if($textFiles['alnFile'] !== "")
                <li role="presentation"><a href="#alnFile" aria-controls="alnFile" role="tab" data-toggle="tab">ALN File</a></li>
            @endif
            @if($textFiles['alnNucl'] !== "")
                <li role="presentation"><a href="#alnNucl" aria-controls="alnNucl" role="tab" data-toggle="tab">Aligned Nucl</a></li>
            @endif
            @if($textFiles['alnAmin'] !== "")
                <li role="presentation"><a href="#alnAmin" aria-controls="alnAmin" role="tab" data-toggle="tab">Aligned Amin</a></li>
            @endif
            @if($textFiles['phiPackLog'] !== "")
                <li role="presentation"><a href="#phiPackLog" aria-controls="tree" role="tab" data-toggle="tab">PhiPack Log</a></li>
            @endif
            @if($textFiles['tree'] !== "")
                <li role="presentation"><a href="#tree" aria-controls="tree" role="tab" data-toggle="tab">Tree</a></li>
            @endif
            @if(isset($newicks) && $textFiles['tree'] !== "")
                <li role="presentation"><a href="#treeView" id="treeViewTab" aria-controls="treeView" role="tab" data-toggle="tab">Tree View</a></li>
            @endif
            <li role="presentation"><a href="#psrf" aria-controls="psrf" role="tab" data-toggle="tab">PSRF</a></li>
            @if($textFiles['codemlOutput'] !== "")
                <li role="presentation"><a href="#codemlOutput" aria-controls="codemlOutput" role="tab" data-toggle="tab">Codeml Output</a></li>
            @endif
            @if($textFiles['codemlSummary'] !== "")
                <li role="presentation"><a href="#codemlSummary" aria-controls="codemlSummary" role="tab" data-toggle="tab">Codeml Summary</a></li>
            @endif
            @if($textFiles['omegaMapSummary'] !== "")
                <li role="presentation"><a href="#omegaMapSummary" aria-controls="tree" role="tab" data-toggle="tab">OmegaMap Summary</a></li>
            @endif
            @if($textFiles['fubarSummary'] !== "")
                <li role="presentation"><a href="#fubarSummary" aria-controls="tree" role="tab" data-toggle="tab">FUBAR Summary</a></li>
            @endif
            @if(count($textFiles['globalSummary']) > 0)
                <li role="presentation"><a href="#globalSummary" aria-controls="tree" role="tab" data-toggle="tab">Global Summary</a></li>
            @endif
            <li role="presentation"><a href="#notes" aria-controls="notes" role="tab" data-toggle="tab">Notes</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            @if(isset($confidences))
                <div role="tabpanel" class="tab-pane fade in active" id="pss">
                    <div id="pssCanvas">

                    </div>
                </div>
            @endif
            <div role="tabpanel" class="tab-pane fade" id="summary">
                @include('includes.download', ['name' => 'summary', 'data' => base64_encode($textFiles['summary'])])
                <pre>{{$textFiles['summary']}}</pre>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="log">
                @include('includes.download', ['name' => 'ExecutionLog', 'data' => base64_encode($textFiles['log'])])
                <pre>{{$textFiles['log']}}</pre>
            </div>
            @if($textFiles['alnFile'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="alnFile">
                    @include('includes.download', ['name' => 'ALNFile', 'data' => base64_encode($textFiles['alnFile'])])
                    <pre>{{$textFiles['alnFile']}}</pre>
                </div>
            @endif
            @if($textFiles['alnNucl'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="alnNucl">
                    @include('includes.download', ['name' => 'AlignedNucl', 'data' => base64_encode($textFiles['alnNucl'])])
                    <pre>{{$textFiles['alnNucl']}}</pre>
                </div>
            @endif
            @if($textFiles['alnAmin'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="alnAmin">
                    @include('includes.download', ['name' => 'AlignedAmin', 'data' => base64_encode($textFiles['alnAmin'])])
                    <pre>{{$textFiles['alnAmin']}}</pre>
                </div>
            @endif
            @if($textFiles['phiPackLog'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="phiPackLog">
                    @include('includes.download', ['name' => 'PhiPackLog', 'data' => base64_encode($textFiles['phiPackLog'])])
                    <pre>{{$textFiles['phiPackLog']}}</pre>
                </div>
            @endif
            @if($textFiles['tree'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="tree">
                    @include('includes.download', ['name' => 'Tree', 'data' => base64_encode($textFiles['tree'])])
                    <pre>{{$textFiles['tree']}}</pre>
                </div>
            @endif
            @if(isset($newicks) && $textFiles['tree'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="treeView">
                    <ul id="svgMenu" class="nav nav-tabs" role="tablist">
                    </ul>
                    <div id="svgCanvas" role="tabpanel" class="tab-content">
                    </div>
                </div>
            @endif
            <div role="tabpanel" class="tab-pane fade" id="psrf">
                @include('includes.download', ['name' => 'PSRF', 'data' => base64_encode($textFiles['psrf'])])
                <pre>{{$textFiles['psrf']}}</pre>
            </div>
            @if($textFiles['codemlOutput'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="codemlOutput">
                    @include('includes.download', ['name' => 'CodemlOutput', 'data' => base64_encode($textFiles['codemlOutput'])])
                    <pre>{{$textFiles['codemlOutput']}}</pre>
                </div>
            @endif
            @if($textFiles['codemlSummary'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="codemlSummary">
                    @include('includes.download', ['name' => 'CodemlSummary', 'data' => base64_encode($textFiles['codemlSummary'])])
                    <pre>{{$textFiles['codemlSummary']}}</pre>
                </div>
            @endif
            @if($textFiles['omegaMapSummary'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="omegaMapSummary">
                    @include('includes.download', ['name' => 'omegaMapSummary', 'data' => base64_encode($textFiles['omegaMapSummary'])])
                    <pre>{{$textFiles['omegaMapSummary']}}</pre>
                </div>
            @endif
            @if($textFiles['fubarSummary'] !== "")
                <div role="tabpanel" class="tab-pane fade" id="fubarSummary">
                    @include('includes.download', ['name' => 'fubarSummary', 'data' => base64_encode($textFiles['fubarSummary'])])
                    <pre>{{$textFiles['fubarSummary']}}</pre>
                </div>
            @endif
            @if(count($textFiles['globalSummary']) > 0)
                <div role="tabpanel" class="tab-pane fade" id="globalSummary">
                    <label for="globalSelect" class="control-label">Global file:</label>
                    <select id="globalSelect" class="form-control">
                        @foreach($textFiles['globalSummary'] as $name => $globalFile)
                            <option value="{{$name}}">{{$name}}</option>
                        @endforeach
                    </select>
                    @php
                        $first = true
                    @endphp
                    @foreach($textFiles['globalSummary'] as $name => $globalFile)
                        <div id="fglobal{{$name}}" class="{{ ($first ? $first = false : 'hidden') }}">
                            @include('includes.download', ['name' => 'globalSummary-' . $name, 'data' => base64_encode($globalFile)])
                            <pre>{{$globalFile}}</pre>
                        </div>
                    @endforeach
                </div>
            @endif
            <div role="tabpanel" class="tab-pane fade" id="notes">
                @include('includes.download', ['name' => 'Notes', 'data' => base64_encode($textFiles['notes'])])
                <pre>{{$textFiles['notes']}}</pre>
            </div>
        </div>
    </div>
@endsection

@section('endscripts')
    <script type="text/javascript" src="{{URL::asset('js/raphael-min.js')}}" ></script>
    <script type="text/javascript" src="{{URL::asset('js/jsphylosvg-min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/d3.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/phyd3.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/jspdf.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/html2pdf.js')}}"></script>
    <script type="text/javascript" src="{{URL::asset('js/bpositive/pss.js')}}"></script>
    <script type="text/javascript">
        //TODO: Refactor
        $(window).on('load', function () {
            $("#globalSelect").change(function () {
                $( "#globalSelect option:selected" ).each(function () {
                    $("#globalSummary [id^=fglobal]").addClass("hidden");
                    $("#fglobal" + $(this).text()).removeClass("hidden");
                });
            });
                    @if(isset($newicks))
            var dataObjects = {!! $newicks !!};

            for(var i=0; i < dataObjects.length; i++ ){

                var divName = 'svgCanvas' + i;
                var data = dataObjects[i];

                var svgCanvas = $("#svgCanvas");

                $("#svgMenu").append('<li role="presentation"><a href="#tab' + divName + '" role="tab" data-toggle="tab" data-newick="' + data + '" data-divname="' + divName + '">Phylogeny ' + i + '</a></li>');
                svgCanvas.append('<div id="tab' + divName + '" class="tab-pane fade in">' +
                    '<div class="navbar navbar-default"><div class="container-fluid" id="container' + divName + '">' +
                    '</div>' +
                    '</div>' +
                    '<div class="alert alert-info">Use your mouse to drag and zoom. Tip: CTRL + wheel = scale Y, ALT + wheel = scale X</div>' +

                    '<div id="' + divName + '"></div>' +
                    '</div>');



            }

            $('#topTabs').on('shown.bs.tab', function(event){
                if($(event.target).html() == $('#treeViewTab').html()) {
                    $('#svgMenu a:first').tab('show');
                }
            });
            $('#svgMenu').on('shown.bs.tab', function(event){
                $('#linkSVG').remove();
                $('#linkPNG').remove();
                $('#resetZoom').remove();
                $('#container' + $(event.target).data('divname')).append('<form class="navbar-form">' +
                    '<a class="btn btn-info form-control" download="Phylogeny'+ i + '.svg" id="linkSVG">Download as SVG</a>' +
                    '<a class="btn btn-info form-control" download="Phylogeny'+ i + '.png" id="linkPNG">Download as PNG</a>' +
                    '<a class="btn btn-default form-control" id="resetZoom">Reset zoom</a>' +
                    //'<div class="checkbox"><label><input id="lengthValues" type="checkbox"><span class="checkbox-material"><span class="check"></span></span> Show length values</label></div>' +
                    '</form>');

                //Workaround for zoom problem when saving to file
                jQuery.fn.d3Click = function () {
                    this.each(function (i, e) {
                        var evt = new MouseEvent("click");
                        e.dispatchEvent(evt);
                    });
                };


                $('#linkSVG').click(function () {
                    $('#resetZoom').d3Click();
                });
                $('#linkPNG').click(function () {
                    $("#resetZoom").d3Click();
                });

                var tree = phyd3.newick.parse($(event.target).data('newick'));
                phyd3.phylogram.build('#' + $(event.target).data('divname'), tree, {

                    showSupportValues: true,
                    showNodesType: 'all',
                    nodeHeight: 10,
                    lineupNodes: false
                });

                /*
                var phylocanvas = new Smits.PhyloCanvas(
                    $(event.target).data('newick'),		// Newick or XML string
                    $(event.target).data('divname'),	// Div Id where to render
                    800, 800		// Height, Width in pixels

                    //'circular'
                );
                 //TODO: workaround for svg starting with an offset
                 var svg = $('#' + $(event.target).data('divname') + ' svg');
                 svg.height(svg.height() + 200);
                 svg.addClass('center-block');

                 //Gets SVG data and removes all non ASCII characters, because they are problematic for some viewers
                 //$('#btn' + divName).attr('href', 'data:image/svg+xml;base64,' + btoa(phylocanvas.getSvgSource().replace(/[^\x00-\x7F]/g, "")));
                */
            });
                    @endif

                    @if(isset($confidences))
            var pss = new PSS(
                    {!!$transcription->getJSON()!!},
                    {!!$confidences->getJSONSequences()!!},
                    {!!$confidences->getJSONModels()!!},
                    {!!$confidences->getJSONMovedIndexes()!!},
                    {!!$scores!!},
                'pssCanvas',
                '{{URL::asset('images/bpositive.png')}}'
                );
            pss.getPSS();
            @endif
        });
    </script>
@endsection('endscripts')
