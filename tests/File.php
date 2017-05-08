<?php

/**
 * Contains the FileTest class.
 * 
 * @package Common\Test
 * @author  Christian Micklisch <christian.micklisch@successwithsos.com>
 */

namespace Common\Test;

use Common\File;
use Common\File\NotFoundException;
use Common\File\NotSafeException;
use Common\File\NotValidException;
use Common\Reflection;

/**
 * FileTest class. A PHPUnit Test case class.
 *
 * Tests info, usable, valid, and safe methods of the Common\File class.
 * 
 * @author Christian Micklisch <christian.micklisch@successwithsos.com>
 */

class File_Test extends \PHPUnit_Framework_TestCase
{

    const TEST_DIRECTORY = 'Info_Test';

    /**
     * Sets up a basic environment with the testing directory
     */
    public function setUp() {
        try {
            $this->createDirectory(self::TEST_DIRECTORY);
        } catch(\Exception $e) {}
    }

    /**
     * Goes to create the entire dummy directory.
     *
     * @param string $directory The directory to actually create.
     */
    private function createDirectory($directory)
    {
        $dir_array = explode('/', $directory);
        $dir_current_arr = [];

        foreach ($dir_array as $key => $string) {
            $dir_current_arr[] = $string;
            $dir_str = implode('/', $dir_current_arr);
            if (!file_exists($dir_str))
                mkdir($dir_str);
        }
    }


    /**
     * Removes the basic environment for testing the File class
     */
    public static function tearDownAfterClass() {
        try {
            system("rm -rf ".escapeshellarg(current(explode('/', self::TEST_DIRECTORY))));
        } catch(\Exception $e) {}
    }

    /**
     *
     *
     *
     * Input 
     *
     *
     * 
     */

    /**
     * Creates the files to use for the info test case.
     *
     * @return array
     */
    public function input_info() {
        $this->createDirectory(self::TEST_DIRECTORY);

        $default_image_file = self::TEST_DIRECTORY . "/default.png";
        file_put_contents($default_image_file, file_get_contents("http://placehold.it/350x150"));

        return [
            [
                $default_image_file
            ],
            [
                // should be caught by an exception.
                self::TEST_DIRECTORY . "/nonexistant.txt"
            ]
        ];
    }

    /**
     * Creates files with valid types and invalid types.
     * 
     * @return array An array of file paths, valid types, and expected
     *               exception messages.
     */
    public function input_usable() {
        $this->createDirectory(self::TEST_DIRECTORY);

        $default_image_file = self::TEST_DIRECTORY . "/usable.png";
        file_put_contents($default_image_file, file_get_contents("http://placehold.it/350x150"));

        $not_valid_file  = self::TEST_DIRECTORY . "/not_valid.txt";
        file_put_contents($not_valid_file, "This file is simply a text file(filename)");

        return [
            [
                $default_image_file,
                [],
                true,
                "" // I dont expect an exception
            ],
            [
                self::TEST_DIRECTORY,
                [],
                false,
                NotSafeException::FILE_NOT_SAFE
            ],
            [
                self::TEST_DIRECTORY . '/doesnotexist.txt',
                [],
                false,
                NotFoundException::FILE_NOT_FOUND
            ],
            [
                $not_valid_file,
                [],
                false,
                NotValidException::FILE_NOT_VALID
            ],
        ];
    }

    /**
     *
     *
     *
     * Test
     *
     *
     *
     */

    /**
     * Tests that the info function receives all of the necessary
     *
     * @dataProvider input_info
     *
     * @param  string $path The file path.
     */
    public function test_info($path = "") {
        try {
            $result = File::info($path);
        } catch (NotFoundException $e) {
            $this->assertEquals($e->getMessage(), NotFoundException::FILE_NOT_FOUND);
            return;
        }

        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));

        $default_info = Reflection::getProperty(
            '_default_info', 
            'Common\File'
        );

        foreach ($default_info as $default_info_key => $deafult_info_value) {
            $this->assertArrayHasKey($default_info_key, $result);

            switch (gettype($deafult_info_value)) {
                case 'string':
                    $this->assertTrue(is_string($result[$default_info_key]), $default_info_key . " is not a string");
                    $this->assertTrue(strlen($result[$default_info_key]) > 0, $default_info_key . " does not have a string length");
                    break;
                case 'integer':
                    $this->assertTrue(is_int($result[$default_info_key]), $default_info_key . " is not an integer");
                    $this->assertTrue($result[$default_info_key] > 0, $default_info_key . " is not greater than 0");
                    break;
            }
        }
    }

    /**
     * Tests the usable function.
     *
     * @dataProvider input_usable
     *
     * @param  string  $path            The file path.
     * @param  array   $valid_types     The types to validate against.
     * @param  boolean $expected_result The expected result of the usable
     *                                  function.
     * @param  string  $exception_message The expected exception message if an
     *                                    excption is thrown.
     */
    public function test_usable(
        $path = "",
        array $valid_types = [],
        $expected_result = true,
        $exception_message = ""
    ) {
        try {
            $this->assertEquals(File::usable($path, $valid_types), $expected_result);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), $exception_message);
            $this->assertFalse($expected_result);
            return;
        }
    }
}