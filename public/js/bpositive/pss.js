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


function PSS (sequences, models, scores, canvasName) {
    this.sequences = sequences;
    this.models = models;
    this.scores = scores;

    this.canvas = canvasName;
    this.cmbModel;
    this.divAlignment;

    this.labelLength = 10;
    this.blockLength = 10;
    this.blocksPerLine = 9;
    this.labelTab = 3;

    this.neb95beb95Background = 'yellow';
    this.neb95beb9095Background = 'red';
    this.neb9095beb95Background = 'blue';
    this.neb9095beb9095Background = 'green';
    this.neb95beb95Foreground = 'black';
    this.neb95beb9095Foreground = 'white';
    this.neb9095beb95Foreground = 'white';
    this.neb9095beb9095Foreground = 'black';
    this.scoresForeground = 'grey';

    this.fontSize = '14';

    this.showScores = true;

    var parent = this;

    this.changeStyles = function(neb95beb95Background, neb95beb9095Background, neb9095beb95Background, neb9095beb9095Background,
                                 neb95beb95Foreground, neb95beb9095Foreground, neb9095beb95Foreground, neb9095beb9095Foreground) {
        parent.neb95beb95Background = neb95beb95Background;
        parent.neb95beb9095Background = neb95beb9095Background;
        parent.neb9095beb95Background = neb9095beb95Background;
        parent.neb9095beb9095Background = neb9095beb9095Background;
        parent.neb95beb95Foreground = neb95beb95Foreground;
        parent.neb95beb9095Foreground = neb95beb9095Foreground;
        parent.neb9095beb95Foreground = neb9095beb95Foreground;
        parent.neb9095beb9095Foreground = neb9095beb9095Foreground;

        if(parent.cmbModel) {
            parent.cmbModel.change();
        }
    };

    this.getPSS = function (){

        var canvas = $('#' + parent.canvas);
        if(parent.models.length == 0){
            canvas.html('<div class="alert alert-warning">No Possitively Selected Sites</div>');
        }
        else{
            var form = $('<form class="navbar-form" />');
            var header = $('<div class="container-fluid" />').append(form);
            var navbar = $('<div class="navbar navbar-default" />').append(header);

            var groupCmbModel = $('<div class="form-group" />');
            parent.cmbModel = $('<select class="form-control"/>').appendTo(groupCmbModel).change(parent.updatePSS);
            $.each(parent.models, function(index, value){

                    $('<option/>', {value: index, text: index}).appendTo(parent.cmbModel);

            });

            var groupScores = $('<label class="checkbox-inline">Show scores</label>');
            $('<input type="checkbox" id="checkScores" ' + (parent.showScores ? "checked=\"checked\"":"") + ' />').change(parent.updateScores).prependTo(groupScores);

            form.append(groupCmbModel);
            form.append(parent.createCmbGroup('FontSize', 'Font size:',6, 30, parent.fontSize, parent.updateFontSize));
            form.append(parent.createCmbGroup('LabelLength', 'Label length:', 1, 20, parent.labelLength, parent.updateLabelLength));
            form.append(parent.createCmbGroup('LabelTab', 'Label tab:', 1, 20, parent.labelTab, parent.updateLabelTab));
            form.append(parent.createCmbGroup('BlockLength', 'Block length:', 1, 50, parent.blockLength, parent.updateBlockLength));
            form.append(parent.createCmbGroup('BlocksPerLine', 'Blocks per line:', 1, 50, parent.blocksPerLine, parent.updateBlocksPerLine));
            form.append(groupScores);
            canvas.html(navbar);
            parent.divAlignment = $('<div class="text-nowrap" style="overflow: auto"/>');
            canvas.append(parent.divAlignment);
            parent.cmbModel.change();
        }
    };

    this.createCmbGroup = function(basename, label, from, to, value, changeHandler){

        var group = $('<div class="form-group"><label for="cmb' + basename + '" class="control-label">' + label + '</label></div>');
        var cmb = $('<select id="cmb' + basename + '" class="form-control"/>').change(changeHandler).appendTo(group);
        for(var i = from; i <= to; i++){
            if(i == value){
                $('<option/>', {selected:'selected', value: i, text: i}).appendTo(cmb);
            }
            else {
                $('<option/>', {value: i, text: i}).appendTo(cmb);
            }
        };
        return group;
    };

    this.updateLabelLength = function(){
        parent.labelLength = $( this ).val();
        parent.updatePSS();
    };

    this.updateBlockLength = function(){
        parent.blockLength = $( this ).val();
        parent.updatePSS();
    };

    this.updateBlocksPerLine = function(){
        parent.blocksPerLine = $( this ).val();
        parent.updatePSS();
    };

    this.updateLabelTab = function(){
        parent.labelTab = $( this ).val();
        parent.updatePSS();
    };

    this.updateFontSize = function(){
        parent.fontSize = $( this ).val();
        parent.updatePSS();
    };

    this.updateScores = function(){
        parent.showScores = $( this ).is(':checked');
        parent.updatePSS();
    };

    this.updatePSS = function(){
        var html = '';
        var htmlScores = '';
        var k = 0;

        if (parent.blocksPerLine < 1) {
            parent.blocksPerLine = 9;
        }

        var numBlocks = Math.floor(parent.sequences[0].value.length / parent.blockLength);
        if(parent.sequences[0].value.length % parent.blockLength > 0){
            numBlocks++;
        }

        do {
            $.each(parent.sequences, function (index, sequence) {
                html += '<div style="width:' + (+parent.labelTab + +parent.labelLength) + 'em;float:left">' + sequence.name.substr(0, parent.labelLength) + '</div>';
                if(index === 0) {
                    htmlScores = '<div style="width:' + (+parent.labelTab + +parent.labelLength) + 'em;float:left">' + 'Scores'.substr(0, parent.labelLength) + '</div>';
                }

                for (var j = k*parent.blocksPerLine*parent.blockLength, currentBlock = 0, currentBlockPos = 1; j < sequence.value.length && currentBlock < parent.blocksPerLine; j++, currentBlockPos++) {
                    var confidence = parent.models[parent.cmbModel.val()][j + 1];
                    if (confidence) {
                        var style = '';

                        if (confidence.neb > 0.95 && confidence.beb > 0.95) {
                            style = "background-color:" + parent.neb95beb95Background + "; color:" + parent.neb95beb95Foreground;
                        } else if (confidence.neb > 0.95 && confidence.beb > 0.90) {
                            style = "background-color:" + parent.neb95beb9095Background + "; color:" + parent.neb95beb9095Foreground;
                        } else if (confidence.neb > 0.90 && confidence.beb > 0.95) {
                            style = "background-color:" + parent.neb9095beb95Background + "; color:" + parent.neb9095beb95Foreground;
                        } else if (confidence.neb > 0.90 && confidence.beb > 0.90) {
                            style = "background-color:" + parent.neb9095beb9095Background + "; color:" + parent.neb9095beb9095Foreground;
                        }
                        html += '<span style="' + style + '">' + sequence.value[j] + '</span>';
                    }
                    else {
                        html += sequence.value[j];
                    }
                    if(index === 0) {
                        htmlScores += parent.scores[j];
                    }
                    if (currentBlockPos >= parent.blockLength) {
                        html += ' ';
                        if(index === 0) {
                            htmlScores += ' ';
                        }
                        currentBlockPos = 0;
                        currentBlock++;
                    }
                }

                html += '<br />';
            });

            if(parent.showScores){
                html += '<span style="color:' + parent.scoresForeground + '">' + htmlScores + '</span><br />';
            }
            html += '<br />';

            k++;
        }while(k < numBlocks / parent.blocksPerLine);

        parent.divAlignment.html('<div style="font-family:monospace;font-size:' + parent.fontSize + 'px;">' + html +'</div>');
    };
}