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


class Sequence
{
    public $name;
    public $value;

    function __construct($sequence){
        preg_match('/^\>.+\s+/', $sequence, $sequenceName);
        $this->name = preg_replace('/\s+|\>/', '', $sequenceName[0]);
        $this->value = preg_replace('/^\>.+\s+|\s+/', '', $sequence);
    }

    public function getNumBlocks(){
        return substr_count($this->value, ' ') ;
    }



}
