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

    public function getNewicks(){
        $newicks = array();

        $file_contents = FileUtils::readFileFromTgz('files/'.$this->linkZip.'.tar.gz', $this->name.'/ClustalW2/tree.con');

        $trees = array();
        preg_match_all('/(tree con_50_majrule = \(.+\)\;)/', $file_contents, $trees);

        foreach($trees[0] as $tree){
            preg_match_all('/\(.+\)\;/', $tree, $newick);
            array_push($newicks, '{ newick: \'' . $newick[0][0]. '\'}');
        }

        return $newicks;
    }
}
