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


function PSS (transcription, sequences, models, movedIndexes, scores, canvasName, logo) {
    this.transcription = transcription;
    this.sequences = sequences;
    this.models = models;
    this.movedIndexes = movedIndexes;
    this.scores = scores;
    this.logo = logo;

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

    this.showScores = false;
    this.showIndexes = false;

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
            canvas.html('<div class="alert alert-warning">No Positively Selected Sites</div>');
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

            var btnDisplayCfg = $('<button type="button" class="btn btn-default form-control" data-toggle="modal" data-target="#modalDisplayCfg">Display configuration</button>');
            var modalDisplayCfg = $('<div class="modal fade" id="modalDisplayCfg" tabindex="-1" role="dialog" aria-labelledby="modaldisplaycfg"/>');
            var modalDialog = $('<div class="modal-dialog" role="document" />').appendTo(modalDisplayCfg);
            var modalContent = $(
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel">Display configuration</h4>' +
                    '</div>' +
                '</div>').appendTo(modalDialog);
            var modalBody = $('<div class="modal-body"><p>Changes will be applied inmediatelly to the PSS view.</p>').appendTo(modalContent);
            var modalFooter = $(
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                '</div>').appendTo(modalContent);
            var modalProgress = $('<div class="modal fade" id="pleaseWaitModal" tabindex="-1" role="dialog" aria-labelledby="pleaseWaitModal" data-backdrop="static" data-keyboard="false">' +
                '<div class="modal-dialog" role="document">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h4 class="modal-title" id="myModalLabel">Creating file, please wait...</h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" style="width:100%;"></div>' +
                        '</div>' +
                    '</div></div></div>');

            var groupScores = $('<div class="form-group" />');
            var labelScores = $('<label class="checkbox-inline">Show scores</label>').appendTo(groupScores);
            $('<input type="checkbox" id="checkScores" ' + (parent.showScores ? "checked=\"checked\"":"") + ' />').change(parent.updateScores).prependTo(labelScores);

            var groupIndexes = $('<div class="form-group" />');
            var labelIndexes = $('<label class="checkbox-inline">Show indexes</label>').appendTo(groupIndexes);
            $('<input type="checkbox" id="checkIndexes" ' + (parent.showIndexes ? "checked=\"checked\"":"") + ' />').change(parent.updateIndexes).prependTo(labelIndexes);

            var btnPDF = $('<button type="button" class="btn btn-info form-control">Download as PDF</button>').click(parent.createPDF);
            var btnPNG = $('<button type="button" class="btn btn-info form-control">Download as PNG</button>').click(parent.createPNG);

            form.append(groupCmbModel);
            form.append(btnDisplayCfg);
            form.append(btnPDF);
            form.append(btnPNG);
            modalBody.append(parent.createCmbGroup('fontSize', 'Font size:',6, 30, parent.fontSize));
            modalBody.append(parent.createCmbGroup('labelLength', 'Label length:', 1, 30, parent.labelLength));
            modalBody.append(parent.createCmbGroup('labelTab', 'Label tab:', 1, 20, parent.labelTab));
            modalBody.append(parent.createCmbGroup('blockLength', 'Block length:', 1, 50, parent.blockLength));
            modalBody.append(parent.createCmbGroup('blocksPerLine', 'Blocks per line:', 1, 50, parent.blocksPerLine));

            modalBody.append(groupScores);
            modalBody.append(groupIndexes);

            modalBody.append(parent.createColorPicker('neb95beb95', 'NEB 95% - BEB 95%', parent.neb95beb95Foreground, parent.neb95beb95Background));
            modalBody.append(parent.createColorPicker('neb95beb9095', 'NEB 95% - BEB 90-95%', parent.neb95beb9095Foreground, parent.neb95beb9095Background));
            modalBody.append(parent.createColorPicker('neb9095beb95', 'NEB 90-95% - BEB 95%', parent.neb9095beb95Foreground, parent.neb9095beb95Background));
            modalBody.append(parent.createColorPicker('neb9095beb9095', 'NEB 90-95% - BEB 90-95%', parent.neb9095beb9095Foreground, parent.neb9095beb9095Background));

            canvas.html(navbar);
            parent.divAlignment = $('<div id="alignment" class="text-nowrap" style="overflow: auto;"/>');
            canvas.append(parent.divAlignment);
            canvas.append(modalDisplayCfg);
            canvas.append(modalProgress);
            parent.cmbModel.change();
        }
    };

    this.createPNG = function () {
        $('#pleaseWaitModal').modal();
        html2canvas($('#alignment'), {
            onrendered: function(canvas) {
                //document.body.appendChild(canvas);
                var imgData = canvas.toDataURL();
                var blob = parent.dataURLtoBlob(imgData);
                var a = document.createElement("a");
                document.body.appendChild(a);
                a.style = "display: none";
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = parent.transcription.name + ' - ' + parent.cmbModel.val() + '.png';
                a.click();
                setTimeout(function(){
                    window.URL.revokeObjectURL(url);
                }, 3000);
                $('#pleaseWaitModal').modal('hide');
            }
        });
    };

    this.dataURLtoBlob = function(dataurl) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {type:mime});
    };

    this.createPDF = function(){

        $('#pleaseWaitModal').modal();

        html2canvas($('#alignment'), {
            onrendered: function(canvas) {
                var imgData = canvas.toDataURL('image/jpeg');
                var margin = 10;
                var doc = new jsPDF('p', 'pt', [(canvas.width+(margin*2))*0.75, (canvas.height+(margin*2))*0.75]);
                doc.addImage(imgData, 'JPG', margin, margin);
                doc.save(parent.transcription.name + ' - ' + parent.cmbModel.val() + '.pdf');

                $('#pleaseWaitModal').modal('hide');
            }
        });

    };

    this.createColorPicker = function(basename, label, foreground, background){


        var group = $('<div class="input-group" />');
        var lbl = $('<span class="input-group-addon width-50" style="color:' + foreground +';background-color:' + background + '">' + label + '</span>').appendTo(group);

        $('<span class="input-group-addon btn">Foreground</span>').appendTo(group)
            .colorpicker({
                color: foreground,
                format: 'rgb'
            }).on('changeColor', function(e){
                parent[basename + 'Foreground'] = e.color.toString('rgba');
                lbl.css("color", e.color.toString('rgba'));
                parent.updatePSS();
            });
        $('<span class="input-group-addon btn">Background</span>').appendTo(group)
            .colorpicker({
                color: background,
                format: 'rgb'
            }).on('changeColor', function(e){
                parent[basename + 'Background'] = e.color.toString('rgba');
                lbl.css("background-color", e.color.toString('rgba'));
                parent.updatePSS();
            });
        return group;
    };

    this.createCmbGroup = function(basename, label, from, to, value){

        var group = $('<div class="form-group"><label for="cmb' + basename + '" class="control-label">' + label + '</label></div>');
        var cmb = $('<select id="cmb' + basename + '" class="form-control"/>').appendTo(group).change(function(){
            parent[basename] = $( this ).val();
            parent.updatePSS();
        });
        for(var i = from; i <= to; i++){
            if(i == value){
                $('<option/>', {selected:'selected', value: i, text: i}).appendTo(cmb);
            }
            else {
                $('<option/>', {value: i, text: i}).appendTo(cmb);
            }
        }
        return group;
    };

    this.updateScores = function(){
        parent.showScores = $( this ).is(':checked');
        parent.updatePSS();
    };

    this.updateIndexes = function(){
        parent.showIndexes = $( this ).is(':checked');
        parent.updatePSS();
    };

    this.updatePSS = function(){
        var html = '';
        var htmlSequence = '';
        var htmlScores = '';
        var htmlIndexes = '';
        var k = 0;

        if (parent.blocksPerLine < 1) {
            parent.blocksPerLine = 9;
        }

        var numBlocks = Math.floor(parent.sequences[0].value.length / parent.blockLength);
        if(parent.sequences[0].value.length % parent.blockLength > 0){
            numBlocks++;
        }

        do {
            html += '<div class="printAlign" style="background-color: white">';
            $.each(parent.sequences, function (index, sequence) {
                var labelWidth = (+parent.labelTab + (+parent.labelLength/2+1));
                var firstIndex = true;
                htmlSequence += '<div style="width:' + labelWidth + 'em;float:left">' + sequence.name.substr(0, parent.labelLength) + '</div>';
                if(index === 0) {
                    htmlScores = '<div style="width:' + labelWidth + 'em;float:left">' + 'Scores'.substr(0, parent.labelLength) + '</div>';
                    htmlIndexes = '<div style="width:' + labelWidth + 'em;float:left">' + 'Indexes'.substr(0, parent.labelLength) + '</div>';
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
                        htmlSequence += '<span style="' + style + '">' + sequence.value[j] + '</span>';
                    }
                    else {
                        htmlSequence += sequence.value[j];
                    }

                    if(parent.showIndexes && index === 0 && parent.blockLength > 5) {
                        $.each(parent.movedIndexes, function (l, movedIndex) {
                            if (movedIndex-1 == j) {
                                if (l != 1 && l % 5 != 0) {
                                    htmlIndexes += '&nbsp;';
                                }
                                else if(firstIndex){
                                    firstIndex = false;
                                    htmlIndexes += l.toString();
                                }
                                else{
                                    htmlIndexes = htmlIndexes.substr(0, htmlIndexes.length - (Math.abs(l-5).toString().length-1) * 6) + l.toString();
                                }
                                return false;
                            }
                            else if (movedIndex-1 > j) {
                                htmlIndexes += '&nbsp;';
                                return false;
                            }

                        });
                    }

                    if(index === 0) {
                        htmlScores += parent.scores[j];
                    }
                    if (currentBlockPos >= parent.blockLength) {
                        htmlSequence += ' ';
                        if(index === 0) {
                            htmlScores += ' ';
                            htmlIndexes += '&nbsp;';
                        }
                        currentBlockPos = 0;
                        currentBlock++;
                    }
                }

                htmlSequence += '<br />';
            });

            if(parent.showIndexes){
                html += '<span style="color:' + parent.scoresForeground + '">' + htmlIndexes + '</span><br />';
            }
            html += htmlSequence;
            htmlSequence = '';
            htmlIndexes = '';
            if(parent.showScores){
                html += '<span style="color:' + parent.scoresForeground + '">' + htmlScores + '</span><br />';
            }
            html += '<br />';
            html += '</div>';
            k++;
        }while(k < numBlocks / parent.blocksPerLine);

        parent.divAlignment.html('<div style="font-family:monospace;font-size:' + parent.fontSize + 'px;">' + html +'</div>');
    };
}