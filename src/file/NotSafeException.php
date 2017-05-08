<?php
/**
 * Contains an exception to throw when a file is not safe.
 *
 * @package Common\File
 * @author  Christian Micklisch <christian.micklisch@successwithsos.com>
 */

namespace Common\File;

/**
 * Class NotSafeException.
 *
 * Exception is thrown when an file is unsafe.
 *
 * @author Christian Micklisch <christian.micklisch@successwithsos.com>
 */
class NotSafeException extends \Exception {
    const FILE_NOT_SAFE = 'The file is not safe. Please give a safe file.';
}