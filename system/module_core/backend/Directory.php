<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use UnexpectedValueException;

abstract class Directory {

    const SCAN_MODE_DEFAULT      = FilesystemIterator::UNIX_PATHS|FilesystemIterator::SKIP_DOTS;
    const SCAN_WITH_DIR_AT_FIRST = RecursiveIteratorIterator::SELF_FIRST;
    const SCAN_WITH_DIR_AT_LAST  = RecursiveIteratorIterator::CHILD_FIRST;

    const ERR_MESSAGE_UNKNOWN             = 'Unknown error!';
    const ERR_MESSAGE_IS_NOT_EXISTS       = 'Directory is not exists!';
    const ERR_MESSAGE_IS_NOT_READABLE     = 'Directory is not readable!';
    const ERR_MESSAGE_IS_NOT_WRITABLE     = 'Directory is not writable!';
    const ERR_MESSAGE_PERM_ARE_TOO_STRICT = 'Directory permissions are too strict!';

    const ERR_CODE_UNKNOWN             = 0;
    const ERR_CODE_IS_NOT_EXISTS       = 20;
    const ERR_CODE_IS_NOT_READABLE     = 21;
    const ERR_CODE_IS_NOT_WRITABLE     = 22;
    const ERR_CODE_PERM_ARE_TOO_STRICT = 23;

    static function create($path, $mode = 0777) {
        try { return file_exists($path) || @mkdir($path, $mode, true); } catch (Exception $e) {}
    }

    static function delete($path) {
        try { return @rmdir($path); } catch (Exception $e) {}
    }

    static function is_writable($path) {
        if (Core::is_Win())
             return is_writable($path);
        else return is_writable($path) && is_executable($path);
    }

    static function items_select($path, $filter = '', $with_dirs = false) {
        try {
            $result = [];
            if ($with_dirs === true) $scan = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, static::SCAN_MODE_DEFAULT), static::SCAN_WITH_DIR_AT_FIRST);
            if ($with_dirs !== true) $scan = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, static::SCAN_MODE_DEFAULT));
            foreach ($scan as $c_path => $c_spl_info) {
                if (!$filter || ($filter && preg_match($filter, $c_path))) {
                    if     ($c_spl_info->isFile()) $result[$c_path] = new File($c_path);
                    elseif ($c_spl_info->isDir ()) $result[$c_path] =          $c_path;
                }
            }
            krsort($result);
            return $result;
        } catch (UnexpectedValueException $e) {
            return [];
        }
    }

    static function items_delete_by_date($path, $files_limit = 5000, $date = '2020-01-01') {
        if (file_exists($path)) {
            try {
                $counter = 0;
                $scan = new RecursiveDirectoryIterator($path, static::SCAN_MODE_DEFAULT);
                # 2010-01-01 ← the entire directory with contents will be deleted
                # 2020-01-01 ← the entire directory with contents will be deleted
                # 2030-01-01 ← date above current - no deletion
                # 2040-01-01 ← date above current - no deletion
                foreach ($scan as $c_top_path => $c_top_spl_info) {
                    if ($c_top_spl_info->isDir()) {
                        $c_top_name = $c_top_spl_info->getFilename();
                        if (Security::validate_date($c_top_name) &&
                                                    $c_top_name < $date) {
                            # try to recursively delete all files and directories in current "YYYY-MM-DD" directory
                            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($c_top_path, static::SCAN_MODE_DEFAULT), static::SCAN_WITH_DIR_AT_LAST) as $c_path => $c_spl_info) {
                                if     ($counter >= $files_limit) return;
                                if     ($c_spl_info->isFile()) {@unlink($c_path); $counter++;}
                                elseif ($c_spl_info->isDir ()) {@rmdir ($c_path);}
                            }
                            # try to delete current empty "YYYY-MM-DD" directory
                            @rmdir($c_top_path);
                        }
                    }
                }
            } catch (UnexpectedValueException $e) {
                return;
            }
        }
    }

}
