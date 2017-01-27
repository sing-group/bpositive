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

namespace App\Utils;


use Illuminate\Support\Facades\Storage;

class FileUtils
{


    public static function readFileFromTgz($pathToTgz, $pathToFile) {

        if (!Storage::disk('bpositive')->exists($pathToTgz)) {
            error_log('File does not exist');
            throw new \Exception('File does not exist.');
        }

        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir, $pathToFile, true); // extract all files
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $file_contents = file_get_contents($dir.'/'.$pathToFile);
        FileUtils::deleteDirectory($dir);
        //Storage::disk('bpositive')->deleteDirectory($dir); //TODO: not working

        return $file_contents;
    }

    public static function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!FileUtils::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

}
