<?php
/**
 * Contains an exception to throw when a file is not found.
 *
 * @package Common\File
 * @author  Christian Micklisch <christian.micklisch@successwithsos.com>
 */

namespace Common\File;

/**
 * Class NotFoundException.
 *
 * Exception is thrown when an file cannot be found.
 *
 * @author Christian Micklisch <christian.micklisch@successwithsos.com>
 */
class NotFoundException extends \Exception {
    const FILE_NOT_FOUND = 'File not found. Give an existing file.';
}