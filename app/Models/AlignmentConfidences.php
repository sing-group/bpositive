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


class AlignmentConfidences
{
    private $sequences = array();
    private $models = array();
    private $movedIndexes = array();

    function __construct($sequences, $analysis, $omegaMap = ""){

        $this->sequences = $sequences;
        $this->movedIndexes = $this->getMovedIndexes($this->sequences);

        $posModel01 = strpos ($analysis, 'Model 0 vs 1');
        $posModel21 = strpos ($analysis, 'Model 2 vs 1');
        $posModel87 = strpos ($analysis, 'Model 8 vs 7');

        $modelsString = array();
        $modelsString['Model 0: one-ratio'] = substr($analysis, $posModel01, $posModel21 - $posModel01);
        $modelsString['Model 2: PositiveSelection'] = substr($analysis, $posModel21, $posModel87 - $posModel21);
        $modelsString['Model 8: beta&w>1'] = substr($analysis, $posModel87);

        foreach($modelsString as $key=>$model){
            $nebs = array();
            $bebs = array();
            $posNEB = strpos($model, 'Naive Empirical Bayes (NEB)');
            $posBEB = strpos($model, 'Bayes Empirical Bayes (BEB)');
            if($posNEB !== FALSE) {
                $NEB = substr($model, $posNEB, $posBEB - $posNEB);
                $nebs = $this->parseAnalysis($NEB);
            }

            if($posBEB !== FALSE) {
                $BEB = substr($model, $posBEB);
                $bebs = $this->parseAnalysis($BEB);
            }

            if(count($nebs) > 0 && count($bebs) > 0) {
                foreach($nebs as $k => $v) {
                    if(isset($bebs[$k])) {
                        $this->models[$key][$this->movedIndexes[$k]] = new Confidence($bebs[$k], $nebs[$k]);
                    }
                }
            }
        }

        if($omegaMap !== "") {
            $this->models['omegaMap'] = array();
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $omegaMap) as $line){
                if(preg_match("/^[0-9]+.+[0-9]+$/", $line)) {
                    preg_match_all("/([0-9]+\.?[0-9]*)/", $line, $values);
                    $values = $values[0];
                    if(count($values) > 4) {
                        $this->models['omegaMap'][$values[0] + 1] = new Confidence($values[4], $values[4]);
                    }
                    else {
                        error_log(print_r($values, true));
                    }
                }
            }
        }
    }

    private function getMovedIndexes($sequences){

        if(count($this->movedIndexes) > 0 || count($sequences) == 0){
            return $this->movedIndexes;
        }
        $seqLength = strlen($sequences[0]->value);
        for($i = 0, $dashIndex = 0; $dashIndex < $seqLength; $i++, $dashIndex++){
            do{
                $increased = false;

                foreach ($sequences as $seq){
                    $seqChar = $seq->value{$dashIndex};
                    if ($seqChar == '-' || $seqChar == 'o') {
                        $dashIndex++;
                        $increased = true;
                        break;
                    }
                }

            }while($increased && $dashIndex < $seqLength);
            if($dashIndex < $seqLength){
                $this->movedIndexes[$i+1] = $dashIndex+1;
            }
        }
        return $this->movedIndexes;
    }

    private function parseAnalysis($source){
        $result = array();

        preg_match_all('/\d+\s+\S\s+[0-9\.\*]+\s+[0-9\.\*]+/', $source, $matches);

        foreach($matches[0] as $match){
            $splits = preg_split('/\s+/',$match);
            if(count($splits) < 3){
                throw new \Exception('Wrong analysis format: ' . $match);
            }
            $result[$splits[0]] = preg_replace('/\*+/', '', $splits[2]);
        }

        return $result;
    }

    public function getJSONMovedIndexes(){
        return json_encode($this->movedIndexes);
    }
    public function getJSONSequences(){
        return json_encode($this->sequences);
    }

    public function getJSONModels(){
        return json_encode($this->models);
    }

    public function getNumModels(){
        return count($this->models);
    }


}
