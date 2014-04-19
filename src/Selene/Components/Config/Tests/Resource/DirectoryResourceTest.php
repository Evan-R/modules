<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Resource;

use \Mockery as m;
use \Selene\Components\Config\Resource\DirectoryResource;

class DirectoryResourceTest extends FileResourceTest
{

    protected static $fsCompare = 'isDir';

    protected function getResource($file, $fs = null)
    {
        return new DirectoryResource($file, $fs);
    }

    public function validationProvider()
    {
        return [
            ['/some/dir', false, false],
            ['/some/dir', true, false, 1, 0],
            ['/some/dir', true, true, 0, 1]
        ];
    }
}
