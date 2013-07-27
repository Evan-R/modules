<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Tests;

/**
 * @class DirectoryTest
 * @package
 * @version $Id$
 */
class DirectoryTest extends FilesystemTestCase
{
    public function testListDirectoryStructure()
    {
        $this->buildTree();
        $collection = $this->fs->directory($this->testDrive)->notIn(['foo', 'bar'])->get()->toJson();

        //echo($collection);
    }
}
