# FileClass

A basic file class for PHP. This class is designed to be a Common Class to be utilized as an aid for getting file information, along with confirming that a file is usable (it exists, is safe, and valid).

Its safety is determined by Yousef Ismaeil Cliprz's example found on [php.net](http://php.net/manual/en/function.move-uploaded-file.php#111412). The validity of the file is determined by the file size and content type.

## Usage

To include the FileClass in your composer file add the repo directory into your repositories section in the composer file and add the name of the module into the require section of the composer file. Also include the [ReflectionClass](https://github.com/fufu70/ReflectionClass) for testing purposes.

```
"repositories": {
    ...
    { 
      "type": "vcs", 
      "url": "https://github.com/fufu70/FileClass.git"
    },
    { 
      "type": "vcs",
      "url": "https://github.com/fufu70/ReflectionClass.git"
    },
    ...
}

...

"require": {
    ...
    "fufu70/fileclass": "dev-master",
    "fufu70/reflection-class": "dev-master",
    ...
}
```

To get information from a file simply give the file path to the `File::info` function. A `File\NotFoundException` may be thrown if the file cannot be found.

```php
<?php
use Common\File;

/**
 * @throws NotFoundException
 */
$result = File::info('/path/to/my/file');
```

The result will be structured similar to:

```php
<?php
$result = [
  "original"      => "Info_Test/default.png",
  "realpath"      => "/Users/Fuzzy/milf/FileClass/Info_Test/default.png",
  "dirname"       => "Info_Test",
  "basename"      => "default.png",
  "filename"      => "default",
  "extension"     => "png",
  "mimetype"      => "image/png",
  "size"          => 6486,
  "permissions"   => 33188,
  "time_modified" => 1495979254
];
```

To Test the usability of a file give a file path to the `File::usable` method. The result will be a boolean of usability or a `File\NotFoundException`, `File\NotSafeException`, `File\NotValidException` exception will be thrown to state why a file is not usable.

```php
<?php
use Common\File;

/**
 * @throws NotFoundException
 * @throws NotSafeException
 * @throws NotValidException
 */
$result = File::info('/path/to/my/file');
```
