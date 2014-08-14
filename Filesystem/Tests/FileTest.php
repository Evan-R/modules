<?php

/**
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Tests;

use Selene\Module\Filesystem\File;
use Selene\Module\Filesystem\Filesystem;
use Selene\Module\Filesystem\Exception\IOException;

/**
 * @class FileTest
 * @package
 * @version $Id$
 */
class FileTest extends FilesystemTestCase
{
    /**
     * @expectedException Selene\Module\Filesystem\Exception\IOException
     */
    public function testSetInvalidPathShouldThrowAnException()
    {
        $file = new File(new Filesystem, '/idonotexists');
    }

    /**
     * @test
     */
    public function testWrapperMethods()
    {
        $mockedFs = $this->getMock('Selene\Module\Filesystem\Filesystem', ['chmod', 'chown', 'chgrp', 'touch']);
        $mockedFs->expects($this->once())->method('chmod');
        $mockedFs->expects($this->once())->method('chown');
        $mockedFs->expects($this->once())->method('chgrp');
        $mockedFs->expects($this->once())->method('touch');

        touch($path = $this->testDrive.'/testfile');

        $file = new File($mockedFs, $path);

        $file->touch(time() - 100);
        $file->chmod(0777);
        $file->chown('thomas');
        $file->chgrp('thomas');


    }

    /**
     * @test
     */
    public function testFileToArray()
    {
        touch($path = $this->testDrive.'/testfile');
        $file = new File(new Filesystem, $path);

        $array = $file->toArray();

        $this->assertSame($path, $array['path']);
        $this->assertSame(basename($path), $array['name']);
    }
}
