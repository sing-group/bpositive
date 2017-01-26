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

namespace App\Models;


use App\Utils\FileUtils;
use Illuminate\Support\Facades\DB;

class Transcription
{

    public $id;
    public $name;
    public $description;
    public $linkZip;
    public $linkPdf;
    public $deleted;
    public $projectId;
    public $creationDate;
    public $analyzed;
    public $positivelySelected;

    function __construct($src){
        $this->id = $src->id;
        $this->name = $src->name;
        $this->description = $src->description;
        $this->linkZip = $src->linkZip;
        $this->linkPdf = $src->linkPdf;
        $this->deleted = $src->deleted;
        $this->projectId = $src->projectId;
        $this->creationDate = $src->creationDate;
        $this->analyzed = $src->analyzed;
        $this->positivelySelected = $src->positivelySelected;

    }

    public static function all($id, $query = ''){

        $transriptions = DB::table('transcription')
            ->where('projectId', '=', $id)
            ->where('deleted', '=', '0')
            ->whereRaw('name LIKE \'%'.$query.'%\' OR description LIKE \'%'.$query.'%\'')
            ->orderBy('name')
            ->paginate(10);

        return $transriptions;
    }

    public static function get($id){

        $transcription = DB::table('transcription')
            ->where('deleted', '=', '0')
            ->where('id', '=', $id)
            ->first();

        return $transcription;
    }

    public static function fastaToSequences($source){

        $sequences = array();
        $result = array();
        preg_match_all('/^\>(.|\s)[^\>]+/m', $source, $sequences);

        foreach($sequences[0] as $sequence){
            array_push($result, new Sequence($sequence));
        }

        return $result;
    }

    public function getScores(){

        $scoresFile = FileUtils::readFileFromTgz('files/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/aligned.score_ascii');
        preg_match_all('/c[o-]n?\s+([0-9]|-)+/', $scoresFile, $scores);
        $scores = preg_replace('/c[o-]n?\s+/', '', $scores[0]);
        $scores = implode($scores);
        return $scores;
    }

    public function getConfidences(){

        $sequences = Transcription::fastaToSequences(FileUtils::readFileFromTgz('files/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/aligned.prot.fasta'));
        $confidences = new AlignmentConfidences($sequences, FileUtils::readFileFromTgz('files/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/allfiles/codeml/input.fasta.fasta.out.sum'));

        return $confidences;
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

    public function getNewicks(){
        $newicks = array();

        $file_contents = FileUtils::readFileFromTgz('files/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/tree.con');

        $trees = array();
        preg_match_all('/(tree con_50_majrule = \(.+\)\;)/', $file_contents, $trees);

        foreach($trees[0] as $tree){
            preg_match_all('/\(.+\)\;/', $tree, $newick);
            array_push($newicks, '{ newick: \'' . $newick[0][0]. '\'}');
        }

        return json_encode($newicks);
    }
}
