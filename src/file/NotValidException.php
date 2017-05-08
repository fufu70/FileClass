<?php
/**
 * Contains an exception to throw when a file is not valid.
 *
 * @package Common\File
 * @author  Christian Micklisch <christian.micklisch@successwithsos.com>
 */

namespace Common\File;

/**
 * Class NotValidException.
 *
 * Exception is thrown when an file is not valid.
 *
 * @author Christian Micklisch <christian.micklisch@successwithsos.com>
 */
class NotValidException extends \Exception {
    const FILE_NOT_VALID = 'File not valid. Give a valid file.';
}