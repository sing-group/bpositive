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
            error_log('[readFileFromTgz]: File does not exist: ' . $pathToTgz);
            throw new \Exception('File does not exist: ' . $pathToTgz);
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
            error_log('[readFilesFromTgz]: File does not exist: ' . $pathToTgz);
            throw new \Exception('File does not exist: ' . $pathToTgz);
        }

        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir); // extract all files
        } catch (\Exception $e) {
            error_log('[readFilesFromTgz]: '.$e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $result = array();
        foreach ($mapOfFiles as $name => $path) {
            if(substr($path, -1) === '*') {
                $result[$name] = array();
                $subfiles = glob($dir.'/'.$path);
                foreach ($subfiles as $subfile){
                    $info = pathinfo($subfile);
                    $result[$name][$info['basename']] = file_get_contents($subfile);
                }
            }
            else if(file_exists($dir . '/' . $path)) {
                $contents = file_get_contents($dir . '/' . $path);
                if ($contents === FALSE) {
                    error_log('readFilesFromTgz File does not exist: ' . $dir . '/' . $path);
                    throw new \Exception('File does not exist: ' . $dir . '/' . $path);
                }
                $result[$name] = $contents;
            }
            else {
                $result[$name] = "";
            }
        }
        FileUtils::deleteDirectory($dir);
        //Storage::disk('bpositive')->deleteDirectory($dir); //TODO: not working

        return $result;
    }

    public static function checkFilesFromTgz($pathToTgz, $mapOfFiles) {

        if (!Storage::disk('bpositive')->exists($pathToTgz)) {
            error_log('[checkFilesFromTgz]: File does not exist: ' . $pathToTgz);
            throw new \Exception('File does not exist: ' . $pathToTgz);
        }

        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir); // extract all files
        } catch (\Exception $e) {
            error_log('[checkFilesFromTgz]: ' . $e->getMessage());
            throw new FileException($e->getMessage());
        }

        //Check subdirectories
        $dirs = glob($dir.'/*', GLOB_ONLYDIR);
        if(count($dirs)>1){
            throw new FileException('The file has more than one subdirectory: ' . $pathToTgz);
        }

        foreach ($mapOfFiles as $name => $path) {
            if(file_exists($dir . '/' . $path) === FALSE){
                error_log('[checkFilesFromTgz2]: File does not exist: ' . $dir . '/' . $path);
                throw new FileException('File does not exist: ' . $dir . '/' . $path);
            }
        }
        FileUtils::deleteDirectory($dir);

        return TRUE;
    }

    public static function getTgz($sources, $name) {

        try {
            $dir = '/tmp/' . uniqid() ;

            foreach ($sources as $source) {

                if (!file_exists($source)) {
                    error_log('[getTgz]: File does not exist: ' . $source);
                    throw new \Exception('File does not exist: ' . $source);
                }

                $phar = new \PharData($source);
                $phar->extractTo($dir . '/extracted', null, true);
                unset($phar);
            }

            $tar = $dir . '/' . $name . '.tar';
            if(file_exists($tar)){
                FileUtils::deleteDirectory($tar);
            }
            if(file_exists($tar . '.gz')){
                FileUtils::deleteDirectory($tar . '.gz');
            }

            $result = new \PharData($tar);
            $result->buildFromDirectory($dir . '/extracted');
            $result->compress(\Phar::GZ);

            unset($result);
            \Phar::unlinkArchive($tar);
            FileUtils::deleteDirectory($dir . '/extracted');

            return $tar . '.gz';

        } catch (\Exception $e) {
            error_log('[getTgz]: ' . $e->getMessage());
            throw new FileException($e->getMessage());
        }

    }

    public static function scanExperiments($pathToTgz) {

        if (!Storage::disk('bpositive')->exists($pathToTgz)) {
            error_log('[scanExperiments]: File does not exist: ' . $pathToTgz);
            throw new \Exception('File does not exist: ' . $pathToTgz);
        }

        $name = str_replace('.tar.gz', '', basename($pathToTgz));
        try {
            $dir = '/tmp/'.uniqid();

            $phar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $pathToTgz);
            $phar->extractTo($dir); // extract all files
            FileUtils::deleteFile($pathToTgz);
        } catch (\Exception $e) {
            error_log('[scanExperiments]: '. $e->getMessage());
            throw new FileException($e->getMessage());
        }

        //Check subdirectories
        $experiments = glob($dir.'/*/*', GLOB_ONLYDIR);
        $experiments = array_map('basename', $experiments);

        if(count($experiments) == 0){
            throw new FileException('The file does not have any experiments: ' . $pathToTgz);
        }

        try {
            foreach ($experiments as $experiment){
                $tar = str_replace('.tar.gz', '-'.$experiment.'.tar', $pathToTgz);

                if(file_exists(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$tar)){
                    FileUtils::deleteFile($tar);
                }
                if(file_exists(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$tar.'.gz')){
                    FileUtils::deleteFile($tar.'.gz');
                }

                $ePhar = new \PharData(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $tar);
                $ePhar->buildFromDirectory($dir, '/'.$name.'\/'.$experiment.'.*/');
                $ePhar->addFile($dir.'/'.$name.'/input.fasta', $name.'/input.fasta');
                $ePhar->addFile($dir.'/'.$name.'/names.txt', $name.'/names.txt');
                $ePhar->addFile($dir.'/'.$name.'/project.conf', $name.'/project.conf');
                $ePhar->compress(\Phar::GZ);
                unset($ePhar);
                FileUtils::deleteFile($tar);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage()." : ".Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $tar);
            FileUtils::deleteFile($tar);
            throw new FileException($e->getMessage()." : ".Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $tar);
        }

        FileUtils::deleteDirectory($dir);

        return $experiments;
    }

    public static function checkFilesFromPath($dir, $mapOfFiles) {

        if (!file_exists($dir)) {
            throw new FileException('Path does not exist: ' . $dir);
        }

        //Check subdirectories
        $dirs = glob($dir.'/*', GLOB_ONLYDIR);
        if(count($dirs)>1){
            throw new FileException('The file has more than one subdirectory: ' . $dir);
        }

        foreach ($mapOfFiles as $name => $path) {
            if(file_exists($dir . '/' . $path) === FALSE){
                throw new FileException('[checkFilesFromPath]: File does not exist: ' . $dir . '/' . $path);
            }
        }

        return TRUE;
    }


    public static function storeAs($file, $path, $update = FALSE) {

        if (!$update && Storage::disk('bpositive')->exists($path.'/'.$file->getClientOriginalName())) {
            throw new FileException('File already exists: '  . $path.'/'.$file->getClientOriginalName());
        }

        try {
            return Storage::disk('bpositive')->putFileAs($path, $file, $file->getClientOriginalName());

        } catch (\Exception $e) {
            error_log('[storeAs]: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    public static function storeBundleAs($file, $path, $update = FALSE) {

        $dir = '/tmp/'.uniqid();

        try {
            $bundlePath = Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().Storage::disk('bpositive')->putFileAs($dir.'/bundle', $file, $file->getClientOriginalName());

            if ($file->getMimeType() == 'application/zip' ) {
                $zip = new \ZipArchive();
                $zip->open($bundlePath);
                $zip->extractTo($dir.'/extracted');
                $zip->close();
                unset($zip);
                //unlink($bundlePath);
            }
            else{
                $phar = new \PharData($bundlePath);
                $phar->extractTo($dir.'/extracted');
                unset($phar);
            }

            Storage::disk('bpositive')->makeDirectory($path.'/');

            $subdirs = glob($dir.'/extracted'.'/*', GLOB_ONLYDIR);
            $names = array();
            foreach ($subdirs as $subdir){
                $info = pathinfo($subdir);
                $tar = Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix(). $path.'/'.$info['filename'].'.tar';

                if($update && file_exists($tar)){
                    FileUtils::deleteDirectory($tar);
                }
                if($update && file_exists($tar.'.gz')){
                    FileUtils::deleteDirectory($tar.'.gz');
                }

                $pharTranscription = new \PharData($tar);
                $pharTranscription->buildFromDirectory($dir.'/extracted', '/'.$info['filename'].'\//');
                $pharTranscription->compress(\Phar::GZ);
                unset($pharTranscription);
                \Phar::unlinkArchive($tar);
                $names[] = $info['filename'];
            }


            FileUtils::deleteDirectory($dir);
            Storage::disk('bpositive')->deleteDirectory($dir);

            return $names;

        } catch (\Exception $e) {
            FileUtils::deleteDirectory($dir);
            Storage::disk('bpositive')->deleteDirectory($dir);
            //Storage::disk('bpositive')->deleteDirectory($path.'/');
            error_log("Exception storing bundle: " . $e->getMessage());
            throw new FileException("Exception storing bundle: " . $e->getMessage());
        }
    }

    public static function scanBundle($file) {
        $dir = '/tmp/'.uniqid();

        try {

            $bundlePath = Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().Storage::disk('bpositive')->putFileAs($dir.'/bundle', $file, $file->getClientOriginalName());

            if ($file->getMimeType() == 'application/zip' ) {
                $zip = new \ZipArchive();
                $zip->open($bundlePath);
                $zip->extractTo($dir.'/extracted');
                $zip->close();
                $name = str_replace('.zip', '', $file->getClientOriginalName());
            }
            else{
                $phar = new \PharData($bundlePath);
                $phar->extractTo($dir.'/extracted');
                $name = str_replace('.tar.gz', '', $file->getClientOriginalName());
            }

            try {
                $files = array();
                $files['input'] = $name.'/input.fasta';
                $files['names'] = $name.'/names.txt';
                $files['project'] = $name.'/project.conf';

                FileUtils::checkFilesFromPath($dir.'/extracted', $files);
                FileUtils::deleteDirectory($dir);
                Storage::disk('bpositive')->deleteDirectory($dir);
                return FALSE;
            }
            catch (FileException $fe){
                $subdirs = glob($dir.'/extracted'.'/*', GLOB_ONLYDIR);
                $names = [];
                foreach ($subdirs as $subdir){
                    $info = pathinfo($subdir);
                    $names[] = $info['filename'];
                }
                FileUtils::deleteDirectory($dir);
                Storage::disk('bpositive')->deleteDirectory($dir);
                return $names;
            }

        } catch (\Exception $e) {
            FileUtils::deleteDirectory($dir);
            throw new FileException($e->getMessage());
        }
    }

    public static function zipToTgz($pathToReadZip) {

        if (!Storage::disk('bpositive')->exists($pathToReadZip)) {
            error_log('[zipToTgz]: File does not exist: ' . $pathToReadZip);
            throw new FileException('File does not exist: '  . $pathToReadZip);
        }


        $tar = str_replace('.zip', '.tar', Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$pathToReadZip);
        $tgz = str_replace('.zip', '.tar.gz', Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$pathToReadZip);

        if(file_exists($tgz)){
            FileUtils::deleteDirectory($tgz);
        }
        try {
            $dir = '/tmp/'.uniqid();
            $zip = new \ZipArchive();
            $zip->open(Storage::disk('bpositive')->getDriver()->getAdapter()->getPathPrefix().$pathToReadZip);
            $zip->extractTo($dir.'/extracted');
            $zip->close();

            $phar = new \PharData($tar);
            $phar->buildFromDirectory($dir.'/extracted');
            $phar->compress(\Phar::GZ);

            Storage::disk('bpositive')->delete($pathToReadZip);
            $metadata = $phar->getMetadata();
            unset($phar);
            \Phar::unlinkArchive($tar);
            FileUtils::deleteDirectory($dir);
            return $metadata;

        } catch (\Exception $e) {
            if(isset($phar)) {
                $p = $phar->getPath();
                unset($phar);
                \Phar::unlinkArchive($p);
                FileUtils::deleteDirectory($dir);
            }
            error_log('[zipToTgz]: '. $e->getMessage());
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
