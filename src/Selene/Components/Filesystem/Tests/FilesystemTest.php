<?php

/**
 * This File is part of the Selene\Components\Filesystem\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Tests;

use org\bovigo\vfs\vfsStream;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\Filesystem\Filesystem;
use Selene\Components\Filesystem\FileCollection;

/**
 * @class FilesystemTest
 * @package
 * @version $Id$
 */
class FilesystemTest extends FilesystemTestCase
{
    public function testIsDirecctory()
    {
        $this->assertTrue($this->fs->isDir($this->testDrive));
    }

    public function testIsDirecctorySouldReportFalseOnFile()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->assertFalse($this->fs->isDir($target));
    }

    public function testIsFile()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        if (!@touch($target)) {
            $this->fail();
        }
        $this->assertTrue($this->fs->isFile($target));
    }

    public function testShouldCreateDirectory()
    {
        $this->fs->mkdir($target = $this->testDrive.DIRECTORY_SEPARATOR.'create_dir');
        $this->assertIsDir($target);
    }

    public function testFirectoryOrFileExistsSouldReportTrue()
    {
        $this->assertTrue($this->fs->exists($this->testDrive));

        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        if (!@touch($target)) {
            $this->fail();
        }
        $this->assertTrue($this->fs->exists($target));
    }

    public function testFirectoryOrFileExistsSouldReportFalse()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->assertFalse($this->fs->exists($target));
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir';
        $this->assertFalse($this->fs->exists($target));
    }

    /**
     * @expectedException Selene\Components\Filesystem\Exception\IOException
     */
    public function testShouldCreateDirectoryShouldThrowIOException()
    {
        $target = $target = $this->testDrive.DIRECTORY_SEPARATOR.'create_dir';

        if (!@mkdir($target)) {
            $this->fail();
        }
        $this->fs->mkdir($target);
    }

    public function testShouldCreateFile()
    {
        $this->fs->touch($target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file');
        $this->assertIsFile($target);
    }

    public function testShouldCreateFileSouldNotThrowIOExceptionIfFileExists()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->fs->touch($target);
        $this->assertIsFile($target);
    }

    public function testTouchSouldUpdateMTime()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        file_put_contents($target, '');
        touch($target, time() - 1000);

        $this->fs->touch($target);
        $this->assertEquals(time(), filemtime($target));

    }

    public function testTouchFileModificationTime()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        file_put_contents($target, '');

        $time  = time() - 100;
        $atime = time() - 10;

        $this->fs->touch($target, $time, $atime);

        $this->assertEquals($time, filemtime($target));
        $this->assertEquals($atime, fileatime($target));
    }

    public function testRenameFile()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        file_put_contents($source, 'source_content', LOCK_EX);

        $this->fs->rename($source, $target);

        $this->assertFalse(file_exists($source));
        $this->assertIsFile($target);
        $this->assertSame('source_content', file_get_contents($target));
    }

    /**
     * @expectedException Selene\Components\Filesystem\Exception\IOException
     */
    public function testRenameFileShouldThrowIoException()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        file_put_contents($source, 'source_content', LOCK_EX);
        touch($target);

        $this->fs->rename($source, $target, false);
    }

    public function testRenameFileShouldOverwriteExistingFile()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        file_put_contents($source, $content = 'source_content', LOCK_EX);
        touch($target);

        $this->fs->rename($source, $target, true);
        $this->assertEquals($content, file_get_contents($target));
        $this->assertFalse(is_file($source));
    }

    public function testFileSetContents()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->fs->setContents($source, $content = 'source_content');

        $this->assertEquals($content, file_get_contents($source));
    }

    public function testFileGetContents()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->fs->setContents($source, $content = 'source_content');

        $this->assertEquals($content, $this->fs->getContents($source));
    }

    public function testRenameDirectory()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_tree';

        $this->fs->rename($source, $target);

        $this->assertFalse(is_dir($source));
        $this->assertIsDir($target);
        $this->assertIsDir($target.DIRECTORY_SEPARATOR.'nested_node');
    }

    public function testEnsureDirectoryExists()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'nested_dir';
        $this->fs->ensureDirectory($target);
        $this->assertIsDir($target);
    }

    public function testFileCopy()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        file_put_contents($source, 'source_content', LOCK_EX);

        $this->fs->copy($source, $target);
        $this->assertFileEquals($source, $target);
    }

    public function testFileCopySouldEnumerate()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 1';
        file_put_contents($source, 'source_content', LOCK_EX);
        $this->fs->copy($source);
        $this->assertFileEquals($source, $target);
    }

    public function testFileCopySouldEnumerateWithCustomPrefix()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_file kopie 1';
        file_put_contents($source, 'source_content', LOCK_EX);
        $this->fs->setCopyPrefix('kopie');
        $this->fs->copy($source);
        $this->assertFileEquals($source, $target);
    }

    public function testFileCopySouldContiouslyEnumerate()
    {
        $source   = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target   = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 1';
        $expected = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 2';

        file_put_contents($source, 'source_content', LOCK_EX);
        file_put_contents($target, 'source_content', LOCK_EX);

        $this->fs->copy($source);
        $this->assertFileEquals($source, $expected);
    }

    public function testDirectoryCopy()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_tree';

        $this->fs->copy($source, $target);
        $this->assertFileEquals($source, $target);
    }

    public function testDirectoryCopyShouldEnumerate()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree copy 1';

        $this->fs->copy($source);

        $this->assertFileEquals($source, $target);
    }

    public function testFileDirectorySouldContiouslyEnumerate()
    {
        $this->buildTree();
        $source   = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target   = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree copy 1';
        $expected = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree copy 2';

        mkdir($target);

        $this->fs->copy($source);
        $this->assertFileEquals($source, $expected);
    }

    /**
     * @expectedException Selene\Components\Filesystem\Exception\IOException
     */
    public function testCopyNoneExistingFileSouldThrowIOExeption()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        $this->fs->copy($source, $target);
    }

    public function testDeleteDirectory()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->assertIsDir($source);

        $this->fs->rmdir($source);
        $this->assertFalse(is_dir($source));
        $this->assertFalse(file_exists($source));
    }

    public function testChangeDirectoryPermissions()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);

        $this->fs->chmod(dirname($target), 0777, true);

        $this->assertFilePermissions(777, dirname($target));
        $this->assertFilePermissions(777, $target);
    }

    public function testChangeDirectoryGroup()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);

        $this->fs->chgrp(dirname($target), $this->getFileGroup($target), true);
    }

    public function testChangeDirectoryOwner()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);
        $this->fs->chown(dirname($target), $this->getFileOwner($target), true);
        $this->fs->chown(dirname($target), get_current_user(), true);
    }
}
