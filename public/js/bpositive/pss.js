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


function PSS (transcription, sequences, models, scores, canvasName, logo) {
    this.transcription = transcription;
    this.sequences = sequences;
    this.models = models;
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
            var modalProgress = $('<div class="modal fade" id="pleaseWaitModal" tabindex="-1" role="dialog" aria-labelledby="pleaseWaitModal">' +
                '<div class="modal-dialog" role="document">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h4 class="modal-title" id="myModalLabel">Creating PDF, please wait...</h4>' +
                        '</div>' +
                        '<div class="modal-body">' +
                            '<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" style="width:100%;"></div>' +
                        '</div>' +
                    '</div></div></div>');

            var groupScores = $('<label class="checkbox-inline">Show scores</label>');
            $('<input type="checkbox" id="checkScores" ' + (parent.showScores ? "checked=\"checked\"":"") + ' />').change(parent.updateScores).prependTo(groupScores);

            var btnPDF = $('<button type="button" class="btn btn-info form-control" data-toggle="modal" data-target="#pleaseWaitModal">Download as PDF</button>').click(parent.createPDF);

            form.append(groupCmbModel);
            form.append(btnDisplayCfg);
            form.append(btnPDF);
            modalBody.append(parent.createCmbGroup('FontSize', 'Font size:',6, 30, parent.fontSize, parent.updateFontSize));
            modalBody.append(parent.createCmbGroup('LabelLength', 'Label length:', 1, 30, parent.labelLength, parent.updateLabelLength));
            modalBody.append(parent.createCmbGroup('LabelTab', 'Label tab:', 1, 20, parent.labelTab, parent.updateLabelTab));
            modalBody.append(parent.createCmbGroup('BlockLength', 'Block length:', 1, 50, parent.blockLength, parent.updateBlockLength));
            modalBody.append(parent.createCmbGroup('BlocksPerLine', 'Blocks per line:', 1, 50, parent.blocksPerLine, parent.updateBlocksPerLine));
            modalBody.append(groupScores);
            canvas.html(navbar);
            parent.divAlignment = $('<div id="alignment" class="text-nowrap" style="overflow: auto;"/>');
            canvas.append(parent.divAlignment);
            canvas.append(modalDisplayCfg);
            canvas.append(modalProgress);
            parent.cmbModel.change();
        }
    };

    this.createPDF = function(){

        //$('#pleaseWaitModal').modal();
        /* Resolution problems when using pagesplit
        var doc = new jsPDF('l', 'pt', 'a4');
        var divAlign = $('#alignment');
        doc.addHTML(divAlign, 10, 10,{
            pagesplit: true
        }, function(){
            doc.save('pss.pdf');
        });
        */

        var pdf = new jsPDF('l', 'mm', 'a4');

        var divs = $('div.printAlign');

        //TODO: Image makes Acrobat unable to open PDF
        //var img = new Image();
        //img.onload = function() {
        parent.addPage(pdf, divs, 0, parent.transcription.name + ' - ' + parent.cmbModel.val(), this);
        //};
        //img.src = parent.logo;
    };

    this.addPage = function(pdf, items, index, name, logo){
        var doc = pdf;

        if(items.length == index){
            //doc.addImage(logo, 5, 5, 8, 8);
            doc.text(15, 11, name + ', page: ' + index);
            doc.save(name + '.pdf');
            $('#pleaseWaitModal').modal('hide');
            return;
        }
        if (index != 0) {
            //doc.addImage(logo, 5, 5, 8, 8);
            doc.text(15, 11, name + ', page: ' + index);
            doc.addPage();
        }

        pdf.addHTML(items[index], 10, 15, {}, function () {
            parent.addPage(doc, items, index + 1, name, logo);
        });

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
        }
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
            html += '<div class="printAlign" style="background-color: white">';
            $.each(parent.sequences, function (index, sequence) {

                html += '<div style="width:' + (+parent.labelTab + (+parent.labelLength/2+1)) + 'em;float:left">' + sequence.name.substr(0, parent.labelLength) + '</div>';
                if(index === 0) {
                    htmlScores = '<div style="width:' + (+parent.labelTab + (+parent.labelLength/2+1)) + 'em;float:left">' + 'Scores'.substr(0, parent.labelLength) + '</div>';
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
            html += '</div>';
            k++;
        }while(k < numBlocks / parent.blocksPerLine);

        parent.divAlignment.html('<div style="font-family:monospace;font-size:' + parent.fontSize + 'px;">' + html +'</div>');
    };
}