<?php

/**
 * Houses the File class.
 *
 * @package Common
 * @author  Christian Micklisch <christian.micklisch@successwithsos.com>
 */

namespace Common;

use Common\File\NotFoundException;
use Common\File\NotSafeException;
use Common\File\NotValidException;

/**
 * File class.
 *
 * Gives General information about the file given and checks that a file is safe.
 *
 * @author Christian Micklisch <christian.micklisch@successwithsos.com>
 */
class File
{

    const ORIGINAL_KEY      = 'original';
    const REALPATH_KEY      = 'realpath';
    const DIRNAME_KEY       = 'dirname';
    const BASENAME_KEY      = 'basename';
    const FILENAME_KEY      = 'filename';
    const EXTENSION_KEY     = 'extension';
    const MIMETYPE_KEY      = 'mimetype';
    const SIZE_KEY          = 'size';
    const PERMISSIONS_KEY   = 'permissions';
    const TIME_MODIFIED_KEY = 'time_modified';

    private static $_default_info = [
        self::ORIGINAL_KEY      => '',
        self::REALPATH_KEY      => '',
        self::DIRNAME_KEY       => '',
        self::BASENAME_KEY      => '',
        self::FILENAME_KEY      => '',
        self::EXTENSION_KEY     => '',
        self::MIMETYPE_KEY      => '',
        self::SIZE_KEY          => 0,
        self::PERMISSIONS_KEY   => 0,
        self::TIME_MODIFIED_KEY => 0,
    ];

    private static $_default_types = [
        // link: http://www.php.net/manual/en/function.exif-imagetype.php
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        "text/plain",
        "text/csv",
        "application/vnd.ms-excel"
    ];

    /**
     * Get general information about a file.
     *
     * Retreives the:
     *     Realpath
     *     Directory
     *     Basename
     *     Filename
     *     Extension
     *     MimeType
     *     Size
     *     Permission
     *     TimeModified
     *
     * @param  string $path The file path.
     * @return array        Contains the list of information.
     * @throws NotFoundException
     */
    public static function info($path = "")
    {
        // throw an error
        if (!file_exists($path)) {
            throw new NotFoundException(NotFoundException::FILE_NOT_FOUND);
        }

        $info = self::$_default_info;

        $path_parts = pathinfo($path);

        $info[self::ORIGINAL_KEY]      = $path;
        $info[self::REALPATH_KEY]      = realpath($path);
        $info[self::DIRNAME_KEY]       = $path_parts['dirname'];
        $info[self::BASENAME_KEY]      = $path_parts['basename'];
        $info[self::FILENAME_KEY]      = $path_parts['filename'];
        $info[self::EXTENSION_KEY]     = $path_parts['extension'];
        $info[self::MIMETYPE_KEY]      = mime_content_type($path);
        $info[self::SIZE_KEY]          = filesize($path);
        $info[self::PERMISSIONS_KEY]   = fileperms($path);
        $info[self::TIME_MODIFIED_KEY] = filemtime($path);

        return $info;
    }

    /**
     * Tests if the file is safe and valid.
     *
     * Checks to see that the given path exists, is valid with the valid_types
     * and is safe to load in.
     *
     * @param  string  $path        The file path.
     * @param  array   $valid_types The types to validate against.
     * @return boolean              If the file is usable.
     * @throws NotFoundException
     * @throws NotSafeException
     * @throws NotValidException
     */
    public static function usable($path = "", array $valid_types = [])
    {

        if (!file_exists($path)) {
            throw new NotFoundException(NotFoundException::FILE_NOT_FOUND);
        }

        if (!self::safe($path)) {
            throw new NotSafeException(NotSafeException::FILE_NOT_SAFE);
        }

        if (!self::valid($path, $valid_types)) {
            throw new NotValidException(NotValidException::FILE_NOT_VALID);
        }

        return true;
    }

    /**
     * Determins whether the given file is a valid type or not.
     *
     * Checks if an image has a valid type, or any other file. If the file is
     * an image it uses exif_imagetype to get the file, otherwise it utilizes
     * the mim
     *
     * @param  string  $path        The file path.
     * @param  array   $valid_types The types to validate against.
     * @return boolean              If the file i valid.
     */
    public static function valid($path = "", array $valid_types = [])
    {

        if (sizeof($valid_types) == 0) {
            $valid_types = self::$_default_types;
        }

        // if the file is an image
        if (is_array(getimagesize($path))) {
            return in_array(exif_imagetype($path), $valid_types);
        } else { // general files
            return in_array(self::info($path)[self::MIMETYPE_KEY], $valid_types);
        }
        
        return in_array(mime_content_type($path), $valid_types);
    }

    /**
     * Checks to see if the file passes safety standards.
     *
     * The file size must exist, have a valid file name, and have a total length.
     *
     * @link   http://php.net/manual/en/function.move-uploaded-file.php#111412
     * @param  string  $path The file path.
     * @return boolean       If the file is safe or not.
     * @author Yousef Ismaeil Cliprz
     */
    public static function safe($path = "")
    {
        return (filesize($path) > 0)
            && !self::checkNameValid($path)
            && !self::checkNameLength($path);
    }

    /**
     * Check $_FILES[][name]
     *
     * @param  string $name Uploaded file name.
     * @return boolean      That the file_upload_name is valid.
     * @author Yousef Ismaeil Cliprz
     */
    private static function checkNameValid($name = "")
    {
        return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i", $name)) ? true : false);
    }

    /**
     * Check $_FILES[][name] length.
     *
     * @param  string $name Uploaded file name.
     * @return boolean      That the file_upload_length is valid.
     * @author Yousef Ismaeil Cliprz
     */
    private static function checkNameLength($name = "")
    {
        return (bool) ((mb_strlen($name, "UTF-8") > 225) ? true : false);
    }
}
