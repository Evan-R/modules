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

    public function validationProvider()
    {
        $time = time();
        return [
            ['dirA', false, false],
            ['dirB', true, false, $time, $time - 10],
            ['dirB', true, true, $time - 10, $time]
        ];
    }

    protected function createResourceFile($file, $timestamp, $isFile = true)
    {
        $file = $this->path.DIRECTORY_SEPARATOR.$file;

        if (!$isFile) {
            return $file;
        }

        mkdir($file, 0775, true);

        $child = $this->root->getChild('resources')->getChild(basename($file));
        $child->lastModified($timestamp);

        return $file;
    }

    protected function getResource($file)
    {
        return new DirectoryResource($file);
    }
}
