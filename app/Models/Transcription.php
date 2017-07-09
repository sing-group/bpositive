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

namespace App\Models;


use App\Utils\FileUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public static function all($id, $queryString = '', $filters, $searchType, $pagesize = 10, $orderBy = 'name', $orderType = 'asc'){

        $transriptions = DB::table('transcription')
            ->select(DB::raw('transcription.*'))
            ->join('project', 'transcription.projectId', '=', 'project.id')
            ->where('transcription.projectId', '=', $id)
            ->where('project.deleted', '=', '0')
            ->where('transcription.deleted', '=', '0')
            ->when($searchType, function ($query) use ($queryString, $searchType){
                switch ($searchType) {
                    case 'regexp':
                        if($queryString == ''){
                            $queryString = '.';
                        }
                        $query->whereRaw('(transcription.name REGEXP \''.$queryString.'\')');
                        break;
                    case 'exact':
                        $query->whereRaw('(transcription.name = \''.$queryString.'\')');
                        break;
                    default:
                        $query->whereRaw('(transcription.name LIKE \'%'.$queryString.'%\')');
                        break;
                }
                return $query;
            }, function ($query) use ($queryString){
                return $query->whereRaw('(transcription.name LIKE \'%'.$queryString.'%\')');
            })
            ->when(is_array($filters), function ($query) use ($filters){
                foreach ($filters as $filter){
                    switch ($filter) {
                        case 'pss':
                            $query->where('transcription.positivelySelected', 1);
                            break;
                        case 'analyzed':
                            $query->where('transcription.analyzed', 1);
                            break;
                        case 'notAnalyzed':
                            $query->where('transcription.analyzed', 0);
                            break;
                    }
                }
                return $query;
            })
            ->orderBy($orderBy, $orderType)
            ->paginate($pagesize);

        return $transriptions;
    }

    public static function get($id){

        $transcription = DB::table('transcription')
            ->select(DB::raw('transcription.*'))
            ->join('project', 'transcription.projectId', '=', 'project.id')
            ->where('project.deleted', '=', '0')
            ->where('project.public', '=', '1')
            ->where('transcription.deleted', '=', '0')
            ->where('transcription.id', '=', $id)
            ->first();

        return $transcription;
    }

    public static function getPrivate($id){

        $transcription = DB::table('transcription')
            ->select(DB::raw('transcription.*'))
            ->join('project', 'transcription.projectId', '=', 'project.id')
            ->where('project.deleted', '=', '0')
            ->where('project.public', '=', '0')
            ->where('transcription.deleted', '=', '0')
            ->where('transcription.id', '=', $id)
            ->first();

        return $transcription;
    }

    public static function getByAdmin($id){

        $transcription = DB::table('transcription')
            ->select(DB::raw('transcription.*'))
            ->join('project', 'transcription.projectId', '=', 'project.id')
            ->where('transcription.id', '=', $id)
            ->first();

        return $transcription;
    }

    public static function getByUser($userId, $transcriptionId){

        $transcription = DB::table('transcription')
            ->select(DB::raw('transcription.*'))
            ->join('project', 'transcription.projectId', '=', 'project.id')
            ->join('users_projects', 'transcription.projectId', '=', 'users_projects.projectId')
            ->where('users_projects.userId', '=', $userId)
            ->where('transcription.id', '=', $transcriptionId)
            ->first();

        return $transcription;
    }

    public static function getTgzPath($id, $public){

        $transcription = null;
        if($public == '1') {
            $transcription = Transcription::get($id);
        }
        else{
            $transcription = Transcription::getPrivate($id);
        }


        return Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().'files/'.$transcription->projectId.'/'.$transcription->linkZip.'.tar.gz';

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

        $scoresFile = FileUtils::readFileFromTgz('files/'.$this->projectId.'/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/aligned.score_ascii');
        preg_match_all('/c[o-]n?\s+([0-9]|-)+/', $scoresFile, $scores);
        $scores = preg_replace('/c[o-]n?\s+/', '', $scores[0]);
        $scores = implode($scores);
        return $scores;
    }

    public function getConfidences(){

        $sequences = Transcription::fastaToSequences(FileUtils::readFileFromTgz('files/'.$this->projectId.'/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/aligned.prot.fasta'));
        $confidences = new AlignmentConfidences($sequences, FileUtils::readFileFromTgz('files/'.$this->projectId.'/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/allfiles/codeml/input.fasta.fasta.out.sum'));

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

        $file_contents = FileUtils::readFileFromTgz('files/'.$this->projectId.'/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/tree.con');

        $trees = array();
        preg_match_all('/(tree con_50_majrule = \(.+\)\;)/', $file_contents, $trees);

        $matches = array();
        preg_match_all('/^\s+(\d+)\s([^,;]+)(\,)?$/m', $file_contents, $matches);
        $translations = array_combine($matches[1], $matches[2]);


        foreach($trees[0] as $tree){
            preg_match_all('/\(.+\)\;/', $tree, $newick);
            $newick = $newick[0][0];
            foreach ($translations as $key => $value){
                $newick = str_replace('('.$key.':', '('.$value.':', $newick);
                $newick = str_replace(','.$key.':', ','.$value.':', $newick);
            }
            array_push($newicks,  $newick);
        }

        return json_encode($newicks);
    }

    public function getJSON(){
        return json_encode($this);
    }

    public function getPlainTextFiles(){
        $files = array();
        $basePath = $this->name.'/ClustalW2/';

        $files['notes'] = $basePath.'notes.txt';
        $files['log'] = $basePath.'output.log';
        $files['alnFile'] = $basePath.'aligned.prot.aln';
        $files['alnNucl'] = $basePath.'aligned.fasta';
        $files['alnAmin'] = $basePath.'aligned.prot.fasta';
        $files['tree'] = $basePath.'tree.con';
        $files['psrf'] = $basePath.'mrbayes.log.psrf';
        $files['codemlOutput'] = $basePath.'codeml.out';
        $files['codemlSummary'] = $basePath.'codeml.sum';
        $files['experiment'] = $basePath.'experiment.conf';

        $files_contents = FileUtils::readFilesFromTgz('files/'.$this->projectId.'/'.$this->linkZip.'.tar.gz', $files);

        $files_contents['summary'] = "--- EXPERIMENT NOTES\n\n"
            .$files_contents['notes']
            ."\n\n\n --- EXPERIMENT PROPERTIES\n\n"
            .$files_contents['experiment']
            . "\n\n\n --- PSRF SUMMARY\n\n"
            .$files_contents['psrf']
            . "\n\n\n --- CODEML SUMMARY\n\n"
            .$files_contents['codemlSummary'];

        return $files_contents;
    }

    public static function create($projectId, $name, $file){

        $path = $file->storeAs('bpositive/files/'.$projectId, $file->getClientOriginalName());

        if($path) {
            $link = str_replace('.tar.gz', '', $file->getClientOriginalName());
            $transcription = DB::table('transcription')
                ->insertGetId([
                    'projectId' => $projectId,
                    'name' => $link,
                    'description' => '',
                    'linkZip' => $link,
                    'linkPdf' => $link,
                    'deleted' => 0,
                    'analyzed' => 1,
                    'positivelySelected' => 1,
                ]);
            return $transcription;
        }

        return -1;
    }

    public static function delete($id){

        $transcription = Transcription::getByAdmin($id);

        if($transcription) {
            DB::table('transcription')
                ->where('id', '=', $transcription->id)
                ->delete();

            FileUtils::deleteFile('files/' . $transcription->projectId . '/' . $transcription->linkZip . '.tar.gz');
        }

        return $transcription->projectId;
    }

    public static function deleteByUser($id, $userId){

        $transcription = Transcription::getByUser($userId, $id);
        if($transcription){
            Transcription::delete($id);
            FileUtils::deleteFile('files/'.$transcription->projectId.'/'.$transcription->linkZip . '.tar.gz');
        }
        return $transcription->projectId;

    }
}
