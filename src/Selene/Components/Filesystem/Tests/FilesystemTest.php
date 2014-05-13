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

use org\bovigo\vfs\vfsStream;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\Filesystem\Filesystem;
use Selene\Components\Filesystem\FileCollection;

/**
 * @class FilesystemTest extends FilesystemTestCase FilesystemTest
 * @see FilesystemTestCase
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FilesystemTest extends FilesystemTestCase
{
    /**
     * @test
     */
    public function testIsDirecctory()
    {
        $this->assertTrue($this->fs->isDir($this->testDrive));
    }

    /** @test */
    public function isDirecctoryShouldReportFalseOnFile()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->assertFalse($this->fs->isDir($target));
    }

    /**
     * @test
     */
    public function testIsFile()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        if (!@touch($target)) {
            $this->fail();
        }
        $this->assertTrue($this->fs->isFile($target));
    }

    /**
     * @test
     */
    public function testShouldCreateDirectory()
    {
        $this->fs->mkdir($target = $this->testDrive.DIRECTORY_SEPARATOR.'create_dir');
        $this->assertIsDir($target);
    }

    /**
     * @test
     */
    public function testFirectoryOrFileExistsShouldReportTrue()
    {
        $this->assertTrue($this->fs->exists($this->testDrive));

        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        if (!@touch($target)) {
            $this->fail();
        }
        $this->assertTrue($this->fs->exists($target));
    }

    /** @test */
    public function testDirectoryOrFileExistsShouldReportFalse()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->assertFalse($this->fs->exists($target));
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir';
        $this->assertFalse($this->fs->exists($target));
    }

    /**
     * @test
     */
    public function testShouldCreateDirectoryShouldThrowIOException()
    {
        $target = $target = $this->testDrive.DIRECTORY_SEPARATOR.'create_dir';

        try {
            mkdir($target);
        } catch (\Exception $e) {
            $this->markTestIncomplete(sprintf('Unable to setup test requirements for %s', __METHOD__));
            return;
        }

        try {
            $this->fs->mkdir($target);
        } catch (\Selene\Components\Filesystem\Exception\IOException $e) {
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('test sould throw \Selene\Components\Filesystem\Exception\IOException');
        }
    }

    /** @test */
    public function itShouldCreateAFile()
    {
        $this->fs->touch($target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file');
        $this->assertIsFile($target);
    }

    /** @test */
    public function itShouldCreateAFileAndShouldNotAnThrowIOExceptionIfFileExists()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        $this->fs->touch($target);
        $this->assertIsFile($target);
    }

    /** @test */
    public function itShouldUpdateMTimeOnAFile()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';
        file_put_contents($target, '');
        touch($target, time() - 1000);

        $this->fs->touch($target);
        $this->assertEquals(time(), filemtime($target));
    }

    /** @test */
    public function testFileMTime()
    {
        touch($file = $this->testDrive.DIRECTORY_SEPARATOR.'file');
        touch($file, $time = time() + 1000);
        $this->assertEquals($time, $this->fs->fileMTime($file));
    }

    /**
     * @test
     */
    public function testFileATime()
    {
        touch($file = $this->testDrive.DIRECTORY_SEPARATOR.'file');
        touch($file, time(), $time = time() + 1000);
        $this->assertEquals($time, $this->fs->fileATime($file));
    }

    /**
     * @test
     */
    public function testFileCTime()
    {
        $time = time();
        touch($file = $this->testDrive.DIRECTORY_SEPARATOR.'file');
        $this->assertEquals($time, $this->fs->fileCTime($file));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testFileSetContents()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->fs->setContents($source, $content = 'source_content');

        $this->assertEquals($content, file_get_contents($source));
    }

    /**
     * @test
     */
    public function testFileGetContents()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->fs->setContents($source, $content = 'source_content');

        $this->assertEquals($content, $this->fs->getContents($source));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testEnsureDirectoryExists()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'nested_dir';
        $this->fs->ensureDirectory($target);
        $this->assertIsDir($target);
    }

    /**
     * @test
     */
    public function testFileCopy()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        file_put_contents($source, 'source_content', LOCK_EX);

        $this->fs->copy($source, $target);
        $this->assertFileEquals($source, $target);
    }

    /**
     * @test
     */
    public function testFileCopyShouldEnumerate()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 1';
        file_put_contents($source, 'source_content', LOCK_EX);
        $this->fs->copy($source);
        $this->assertFileEquals($source, $target);
    }

    /**
     * @test
     */
    public function testFileCopyShouldEnumerateWithCustomPrefix()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_file kopie 1';
        file_put_contents($source, 'source_content', LOCK_EX);
        $this->fs->setCopyPrefix('kopie');
        $this->fs->copy($source);
        $this->assertFileEquals($source, $target);
    }

    /**
     * @test
     */
    public function testFileCopyShouldContiouslyEnumerate()
    {
        $source   = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target   = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 1';
        $expected = $this->testDrive.DIRECTORY_SEPARATOR.'source_file copy 2';

        file_put_contents($source, 'source_content', LOCK_EX);
        file_put_contents($target, 'source_content', LOCK_EX);

        $this->fs->copy($source);
        $this->assertFileEquals($source, $expected);
    }

    /**
     * @test
     */
    public function testDirectoryCopy()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_tree';

        $this->fs->copy($source, $target);
        $this->assertFileEquals($source, $target);
    }

    /**
     * @test
     */
    public function testDirectoryCopyShouldEnumerate()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree copy 1';

        $this->fs->copy($source);

        $this->assertFileEquals($source, $target);
    }

    /**
     * @test
     * @expectedException Selene\Components\Filesystem\Exception\IOException
     */
    public function testFileCopyOnExistingTargetShouldThrowExceptionWithoutReplaceFlag()
    {
        touch($fileA = $this->testDrive.DIRECTORY_SEPARATOR.'testA');
        touch($fileB = $this->testDrive.DIRECTORY_SEPARATOR.'testB');
        $this->fs->copy($fileA, $fileB);
    }

    /**
     * @test
     */
    public function testFileCopyOnExistingTarget()
    {
        touch($fileA = $this->testDrive.DIRECTORY_SEPARATOR.'testA');
        touch($fileB = $this->testDrive.DIRECTORY_SEPARATOR.'testB');

        file_put_contents($fileA, 'some content');
        $this->fs->copy($fileA, $fileB, true);
        $this->assertFileEquals($fileA, $fileB);
    }

    /**
     * @test
     */
    public function testFileDirectoryShouldContiouslyEnumerate()
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
     * @test
     * @expectedException Selene\Components\Filesystem\Exception\IOException
     */
    public function testCopyNoneExistingFileShouldThrowIOExeption()
    {
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_file';
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_file';

        $this->fs->copy($source, $target);
    }

    /**
     * @test
     */
    public function testDeleteDirectory()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->assertIsDir($source);

        $this->fs->rmdir($source);
        $this->assertFalse(is_dir($source));
        $this->assertFalse(file_exists($source));
    }

    /**
     * @test
     */
    public function testFlushDirectory()
    {
        $this->buildTree();
        $source = $this->testDrive.DIRECTORY_SEPARATOR.'source_tree';
        $this->assertIsDir($source);

        $this->fs->flush($source);
        $this->assertIsDir($source);
        $this->assertFalse(file_exists($source.DIRECTORY_SEPARATOR.'nested_subtree'));
    }

    /**
     * @test
     */
    public function testDirectoryIsEmpty()
    {
        $this->assertTrue($this->fs->isEmpty($this->testDrive));
        mkdir($this->testDrive.DIRECTORY_SEPARATOR.'foo');
        $this->assertFalse($this->fs->isEmpty($this->testDrive));
    }

    /**
     * @test
     */
    public function testChangeDirectoryPermissions()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);

        $this->fs->chmod(dirname($target), 0777, true);

        $this->assertFilePermissions(777, dirname($target));
        $this->assertFilePermissions(777, $target);
    }

    /**
     * @test
     */
    public function testChangeDirectoryGroup()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);

        $this->fs->chgrp(dirname($target), $this->getFileGroup($target), true);
    }

    /**
     * @test
     */
    public function testChangeDirectoryOwner()
    {
        $target = $this->testDrive.DIRECTORY_SEPARATOR.'target_dir'.DIRECTORY_SEPARATOR.'sub_dir';
        mkdir($target, 0755, true);
        $this->fs->chown(dirname($target), $this->getFileOwner($target), true);
        $this->fs->chown(dirname($target), get_current_user(), true);
    }

    /**
     * @dataProvider absPathProvider
     */
    public function testIsAbsolutePath($path, $true)
    {
        $this->assertTrue($true === $this->fs->isAbsolutePath($path));
    }

    /**
     * @test
     * @dataProvider absPathProvider
     */
    public function testIsRelativePath($path, $true)
    {
        $this->assertTrue($true !== $this->fs->isRelativePath($path));
    }

    public function absPathProvider()
    {
        return [
            ['/this/path', true],
            ['this/path', false],
            ['../this/path', false],
            ['file:///this/path', true],
            ['C:\\\\windows\\System', true],
            ['C://windows/System', true],
            ['windows\\System', false],
            ['', false],
            [null, false],
        ];
    }
}
