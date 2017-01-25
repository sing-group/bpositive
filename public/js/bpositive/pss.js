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


function PSS (sequences, models, movedIndexes, canvas) {
    this.sequences = sequences;
    this.models = models;
    this.movedIndexes = movedIndexes;
    this.canvas = canvas;

    this.getSequences = function() {
        return this.sequences;
    };

    this.getPSS = function (){

        //Create Model combobox
        var sel = $('<select class="form-control"/>');
        var model;

        //TODO: enable combo selection
        $.each(this.models, function(index, value){
            $('<option />', {value: index, text: index}).appendTo(sel);
            model = value;
        });
        $('#' + this.canvas).append(sel);


        var labelLength = 10, blockLength = 10, blocksPerLine = 9, labelTab = 3;
        var html = '';
        var k = 0;

        //TODO refactor
        if (blocksPerLine < 1) {
            blocksPerLine = 9;
        }

        var numBlocks = Math.floor(this.sequences[0].value.length / blockLength);
        if(this.sequences[0].value.length % blockLength > 0){
            numBlocks++;
        }

        do {
            $.each(this.sequences, function (index, sequence) {
                html += '<div style="width:' + (labelTab + labelLength) + 'em;float:left">' + sequence.name.substr(0, labelLength) + '</div>';

                for (var j = k*blocksPerLine*blockLength, currentBlock = 0, currentBlockPos = 1; j < sequence.value.length && currentBlock < blocksPerLine; j++, currentBlockPos++) {
                    var confidence = model[j + 1];
                    if (confidence) {
                        var style = '';

                        //TODO: parametrization
                        if (confidence.neb > 0.95 && confidence.beb > 0.95) {
                            style = "background-color:yellow; color:black";
                        } else if (confidence.neb > 0.95 && confidence.beb > 0.90) {
                            style = "background-color:red; color:white";
                        } else if (confidence.neb > 0.90 && confidence.beb > 0.95) {
                            style = "background-color:blue; color:white";
                        } else if (confidence.neb > 0.90 && confidence.beb > 0.90) {
                            style = "background-color:green; color:black";
                        }
                        html += '<span style="' + style + '">' + sequence.value[j] + '</span>';
                    }
                    else {
                        html += sequence.value[j];
                    }
                    if (currentBlockPos >= blockLength) {
                        html += ' ';
                        currentBlockPos = 0;
                        currentBlock++;
                    }
                }

                html += '<br />';
            });
            k++;
        }while(k < numBlocks / blocksPerLine);

        $('#' + this.canvas).append('<div style="font-family:monospace;">' + html +'</div>');


    };

    this.chunk_split = function(source, length){
        source.match(new RegExp('.{0,' + length + '}', 'g')).join(' ');
    };
}