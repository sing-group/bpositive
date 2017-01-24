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
        $.each(this.models, function(index, value){
            $('<option />', {value: index, text: index}).appendTo(sel);
            model = value;
        });
        $('#' + this.canvas).append(sel);


        var labelLength = 10, blockLength = 10, blocksPerLine = 9, labelTab = 3;
        var html = '';
        $.each(this.sequences, function(index, sequence){
            html += '<p>';
            html += sequence.name.substr(0, labelLength);
            for(var i = 0; i < labelTab; i++){
                html += '&nbsp;';
            }
            //TODO format

            //TODO MODEL

            for(var j = 0; j < sequence.value.length; j++){
                var confidence = model[j+1];
                if(confidence){
                    var style = '';

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
            }
            html += sequence.value;
            html += '</p>'
        });

        $('#' + this.canvas).append('<div class="font-family: monospace;"' + html +'</div>');


    };

    this.chunk_split = function(source, length){
        source.match(new RegExp('.{0,' + length + '}', 'g')).join(' ');
    }
    this.getModel = function(){

    }
    /*
     function __construct($sequence, $labelLength, $blockLength){
     preg_match('/^\>.+\s+/', $sequence, $sequenceName);
     $sequenceName = substr(preg_replace('/\s+/', '', $sequenceName[0]), 0, $labelLength);
     $sequenceValue = preg_replace('/^\>.+\s+|\s+/', '', $sequence);
     $sequenceValue = chunk_split($sequenceValue, $blockLength, ' ');
     $this->name = $sequenceName;
     $this->value = $sequenceValue;
     }

     public function formatedSequencesToString($formattedSequences, $blockLength = 10, $blocksPerLine = 9, $labelTab = 3, $newLine = '<br />'){
     $result = '';

     if($blocksPerLine < 1){
     $blocksPerLine = 9;
     }

     if(count($formattedSequences) > 0){
     $i = 0;
     do{
     $numBlocks = 0;
     foreach($formattedSequences as $formattedSequence) {
     if($numBlocks === 0) {
     $numBlocks = $formattedSequence->getNumBlocks();
     }
     $result .= $formattedSequence->name . str_pad('', $labelTab*6, '&nbsp;');
     $result .= substr($formattedSequence->value, $i*($blockLength+1)*$blocksPerLine, ($blockLength+1)*$blocksPerLine);
     $result .= $newLine;
     }
     $result .= $newLine;
     $i++;
     }
     while($i < $numBlocks / $blocksPerLine);

     }

     return $result;
     }
     */
}