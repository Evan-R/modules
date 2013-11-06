[![Build Status](https://api.travis-ci.org/seleneapp/filesystem.png?branch=development)](https://travis-ci.org/seleneapp/filesystem)

## Setup

```php
<?php

use Selene\Components\Filesystem\Filesystem;

$filesystem = new Filesystem();
```

## Usage

### Deleting a file
`unlink()` deletes a file.

```php
<?php

$filesystem->unlink('/path/source_file.jpg');
// or
$filesystem->remove('/path/source_file.jpg');
```
### Touching a files modification and access time

```php
<?php

// Sets the current time for `filemtime` and `fileatime`.
// If the file doesn't exists, it will be created.
$filesystem->touch('/path/target_file.jpg');

// Set modification and accesstime explicitly.
$filesystem->touch('/path/target_file.jpg', $time, $atime);
```

### Ensure a file exists
`ensureFile()` is basically the same as `touch` except it won't touch the file unless it
doesn't exist yet.

```php
<?php

$filesystem->ensureFile('/path/target_file.txt');
```

### Dump content into a file 
`setContents()` dumps content into a file. `setContents()` takes a second argument
`$writeFlags`, see [file_put_contents *flags*](http://www.php.net/manual/en/function.file-put-contents.php).  
If the file doesn't exists, it will be created.

```php
<?php

$filesystem->setContents('/path/target_file.txt', 'some content');
```
### Get a files content

`getContents()` dumps the files content. It takes a second argument
`$readFlags`, see [file_get_contents *flags*](http://www.php.net/manual/en/function.file-get-contents.php).

```php
<?php

$filesystem->getContents('/path/target_file.txt'); // returns 'some content'
```

### Creating directories

`mkdir()` tries to create a directory. The method
takes a second, boolean argument weather to recursivly create directories or
not (default is `true`).

```php
<?php

$filesystem->mkdir('/path/target_dir');
```

This however will throw an `IOExecption` if the target already exists. If you
want to be sure a directory is only created if it doesn't exists yet, you can
use `Filesystem::ensureDirectory`

```php
<?php

$filesystem->ensureDirectory('/path/target_dir');
```

### Removing directories

```php
<?php

$filesystem->rmdir('/path/source_dir');
// or
$filesystem->remove('/path/source_dir');
```


### Listing directories

```php
<?php

$filesystem
	->directory('/path/dir') // returns an instance of Selene\Components\Filesystem\Directory
	->get();  // returns an instance of Selene\Components\FileCollection
```

### Copy files or directories
`copy()` creates a mirror of a file or a directory and all its contents. The
second `$target` argument is optional. If omitted, `copy()` creates a new file
name that is unique in the context of the current directory.

```php
<?php

$filesystem->copy('/target', '/source');
$filesystem->copy('/target'); // creates '/target copy 1'
$filesystem->copy('/target'); // creates '/target copy 2'

// define the copy prefix
$filesystem->setCopyPrefix('Kopie');
$filesystem->copy('/target'); // creates '/target Kopie 1'
```

## Working with file permissions


All posix methods `chmod()`, `chwon()`, `chgrp()`, take a third boolean argument
`$recursive` weather to change permission settings on all subdirectories or or
not.


### Change read/write permission on a file or directory
```php
<?php

// for files:
$filesystem->chmod('target_file', 0644);

// for directories:
$filesystem->chmod('target_dir', 0777, true);
```
### Change ownership on a file or directory
```php
<?php

// for files:
$filesystem->chown('target_file', 'new_owner');
// or
$filesystem->chown('target_file', 500 /*the uid*/);

// for directories:
$filesystem->chown('target_dir', 'new_owner', true);
```

### Change the filegroup on a file or directory
```php
<?php

// for files:
$filesystem->chgrp('target_file', 'new_group');
// or
$filesystem->chgrp('target_file', 20 /*the gid*/);

// for directories:
$filesystem->chgrp('target_dir', 'new_group', true);
// or
$filesystem->chgrp('target_dir', 20 /*the gid*/, true);
```

## TODO: Working with files and directories

### The directory object `Directory`

The directory object provides some convenient, chainable methods for
interacting with the filesystem. 
It also allows to list an array representation of its root path. Both, the
firectory- and the fileobject implement a arrayable and jsonabled interface,
providing a `toArray()` and `toJson()` method. 

Chainable methods are: `remove()`, `mkdir()`, `rmdir()`, `chmod()`,
`chown()`, `chgrp()`, `in()`, `notIn()`, `filter()`.

### The file object `File`

These methods are chainable: `remove()`, `mkdir()`, `rmdir()`, `chmod()`,
`chown()`, `chgrp()`.

### Creating a file- or directory object

```php
<?php

use Selene\Components\Filesystem\File;
use Selene\Components\Filesystem\Directory;
use Selene\Components\Filesystem\Filesystem;


// get an instance of `Selene\Components\Filesystem\Directory`
$dir = $filesystem->directory('/path/target_dir'); 
// or
$dir = new Directory(new Filesystem, '/path/target_dir');

// get an instance of `Selene\Components\Filesystem\File`
$file = $filesystem->file('/path/target_file'); 
// or
$file = new File(new Filesystem, '/path/target_file');
```

### Creating a file collection
```php
<?php

// Get the directory contents as `FileCollection` instance:
$collection = $filesystem->directory('/path/target_dir')
	->get(); 

// Get contents from a certain subdirectory or subdirectories:
$collection = $filesystem->directory('/path/target_dir')
	->in(['sub_a', 'sub_b'])
	->get(); 

// Exclude subdirectories:
$collection = $filesystem->directory('/path/target_dir')
	->notIn(['sub_c', 'sub_d'])
	->get(); 

// Filter for files:
// By now, `filter takes an arbitrary regeular expression. Glob matching is
// planed for the future. `
$collection = $filesystem->directory('/path/target_dir')
	->in(['images'])
	->filter('.*\.(jpe?g|png|gif)$')
	->get(); 
```

```php
<?php

foreach ($collection as $fileName => $fileInfo) {
	if ($fileInfo->isDir()) {
		// ...		
	}	
	if ($fileInfo->isFile()) {
		// ...		
	}	
}

```

```php
<?php
// export collection to an array:
$collection->toArray();
// export collection to json
$collection->toJson();
```

```json
 {
    ".": {
        "%directories%": {
            "source_tree": {
                "name": "source_tree",
                "path": "\/files\/source_tree",
                "lastmod": 1375118383,
                "type": "dir",
                "owner": 501,
                "group": 20,
                "%directories%": {
                    "nested_subtree": {
                        "name": "nested_subtree",
                        "path": "\/files\/source_tree\/nested_subtree",
                        "lastmod": 1375118383,
                        "type": "dir",
                        "owner": 501,
                        "group": 20
                    }
                },
                "%files%": {
                    "bar.txt": {
                        "name": "bar.txt",
                        "path": "\/files\/source_tree\/bar.txt",
                        "lastmod": 1375118383,
                        "type": "file",
                        "owner": 501,
                        "group": 20,
                        "extension": "txt",
                        "mimetype": "text\/plain"
                    }
                }
            }
        }
    }
}
```
