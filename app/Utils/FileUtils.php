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

namespace App\Utils;


use App\Exceptions\FileException;
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
            $phar->extractTo($dir, $pathToFile, true);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $file_contents = file_get_contents($dir.'/'.$pathToFile);
        FileUtils::deleteDirectory($dir);
        //Storage::disk('bpositive')->deleteDirectory($dir); //TODO: not working

        return $file_contents;
    }

    public static function readFilesFromTgz($pathToTgz, $mapOfFiles) {

        if (!Storage::disk('bpositive')->exists($pathToTgz)) {
            error_log('File does not exist');
            throw new \Exception('File does not exist.');
        }

        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir); // extract all files
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $result = array();
        foreach ($mapOfFiles as $name => $path) {
            $contents = file_get_contents($dir . '/' . $path);
            if($contents === FALSE){
                error_log('File does not exist: ' . $dir . '/' . $path);
                throw new \Exception('File does not exist: ' . $dir . '/' . $path);
            }
            $result[$name] = $contents;
        }
        FileUtils::deleteDirectory($dir);
        //Storage::disk('bpositive')->deleteDirectory($dir); //TODO: not working

        return $result;
    }

    public static function checkFilesFromTgz($pathToTgz, $mapOfFiles) {

        if (!Storage::disk('bpositive')->exists($pathToTgz)) {
            error_log('File does not exist');
            throw new \Exception('File does not exist.');
        }

        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir); // extract all files
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new FileException($e->getMessage());
        }

        //Check subdirectories
        $dirs = glob($dir.'/*', GLOB_ONLYDIR);
        if(count($dirs)>1){
            throw new FileException('The file has more than one subdirectory: ' . $pathToTgz);
        }

        foreach ($mapOfFiles as $name => $path) {
            if(file_exists($dir . '/' . $path) === FALSE){
                error_log('File does not exist: ' . $dir . '/' . $path);
                throw new FileException('File does not exist: ' . $dir . '/' . $path);
            }
        }
        FileUtils::deleteDirectory($dir);

        return TRUE;
    }


    public static function storeAs($file, $path) {

        if (Storage::disk('bpositive')->exists($path.$file->getClientOriginalName())) {
            throw new FileException('File already exists: '  . $path.$file->getClientOriginalName());
        }

        try {
            return Storage::disk('bpositive')->putFileAs($path, $file, $file->getClientOriginalName());

        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public static function storeBundleAs($file, $path) {

        try {

            $dir = '/tmp/'.uniqid();

            $bundlePath = Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().Storage::disk('bpositive')->putFileAs($dir.'/bundle', $file, $file->getClientOriginalName());

            if ($file->getMimeType() == 'application/zip' ) {
                $zip = new \ZipArchive();
                $zip->open($bundlePath);
                $zip->extractTo($dir.'/extracted');
                $zip->close();
            }
            else{
                $phar = new \PharData($bundlePath);
                $phar->extractTo($dir.'/extracted');
            }

            Storage::disk('bpositive')->deleteDirectory($dir);


            Storage::disk('bpositive')->makeDirectory($path.'/');

            $subdirs = glob($dir.'/extracted'.'/*', GLOB_ONLYDIR);
            $names = array();
            foreach ($subdirs as $subdir){
                $info = pathinfo($subdir);
                $names[] = $info['filename'];
                $tar = Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $path.'/'.$info['filename'].'.tar';

                if(file_exists($tar.'.gz')){
                    FileUtils::deleteDirectory($tar.'.gz');
                }
                $pharTranscription = new \PharData($tar);
                $pharTranscription->buildFromDirectory($dir.'/extracted', '/'.$info['filename'].'\//');
                $pharTranscription->compress(\Phar::GZ);
                unlink($tar);
            }


            FileUtils::deleteDirectory($dir);

            return $names;

        } catch (\Exception $e) {
            FileUtils::deleteDirectory($dir);
            Storage::disk('bpositive')->deleteDirectory($path.'/');
            error_log($e->getMessage());
            throw new FileException($e->getMessage());
        }
    }

    public static function zipToTgz($pathToReadZip) {

        if (!Storage::disk('bpositive')->exists($pathToReadZip)) {
            error_log('File does not exist');
            throw new FileException('File does not exist.'  . $pathToReadZip);
        }


        $tgz = str_replace('.zip', '.tar.gz', Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$pathToReadZip);

        if(file_exists($tgz)){
            FileUtils::deleteDirectory($tgz);
        }
        try {

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$pathToReadZip);
            $phar->convertToData(\Phar::TAR, \Phar::GZ);

            Storage::disk('bpositive')->delete($pathToReadZip);
            return $phar->getMetadata();

        } catch (\Exception $e) {
            FileUtils::deleteDirectory($phar->getPath());
            error_log($e->getMessage());
            throw new FileException($e->getMessage());
        }
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

    public static function deleteFile($file){
        return unlink(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$file);
    }

}
