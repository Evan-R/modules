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

use Selene\Components\Filesystem\Directory;

/**
 * @class DirectoryTest
 * @package
 * @version $Id$
 */
class DirectoryTest extends FilesystemTestCase
{

    public function testMkdir()
    {
        $dir = new Directory($this->fs, $this->testDrive);
        $dir->mkdir('test');
        $this->assertIsDir($this->testDrive.DIRECTORY_SEPARATOR.'test');
    }

    public function testRemove()
    {
        mkdir($test = $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir = new Directory($this->fs, $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir->remove();

        $this->assertFalse(file_exists($test));
    }

    public function testCopy()
    {
        mkdir($test = $this->testDrive.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'test', 0777, true);
        $dir = new Directory($this->fs, $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir->copy();
        $this->assertFileEquals(dirname($test), $this->testDrive.DIRECTORY_SEPARATOR.'test copy 1');
    }

    public function testIsIncludedDir()
    {
        $this->buildTree();
        $collection = $this->fs->directory($this->testDrive);

        $this->invokeObjectMethod('setVcsFilter', $collection);

        $paths = ['.git'];
        $this->assertFalse($this->invokeObjectMethod('isIncludedFile', $collection, $this->getPathsAsArgument($paths)));
        $paths = ['source_tree'];
        $this->assertTrue($this->invokeObjectMethod('isIncludedDir', $collection, $this->getPathsAsArgument($paths)));
    }

    public function testIsIncludedFile()
    {
        $this->buildTree();
        $collection = $this->fs->directory($this->testDrive);

        $this->invokeObjectMethod('setVcsFilter', $collection);

        $paths = ['.gitignore'];
        $this->assertFalse($this->invokeObjectMethod('isIncludedFile', $collection, $this->getPathsAsArgument($paths)));
        $paths = ['target.txt'];
        $this->assertTrue($this->invokeObjectMethod('isIncludedFile', $collection, $this->getPathsAsArgument($paths)));
    }

    public function testOnlyInFilter()
    {
        $this->buildTree();
        $dir = $this->fs->directory($this->testDrive);
        $dir->in(['source_tree']);
        $this->invokeObjectMethod('setVcsFilter', $dir);

        $paths = [null];
        $this->assertFalse($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));

        $paths = ['source_tree'];
        $this->assertTrue($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));

        $this->invokeObjectMethod('clearFilter', $dir);
        $this->invokeObjectMethod('setVcsFilter', $dir);

        $dir->in(['source_tree/nested_subtree']);
        $paths = ['source_tree/nested_subtree'];
        $this->assertTrue($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));

        $paths = str_replace('/', '\\', $this->getPathsAsArgument($paths));
        $this->assertTrue($this->invokeObjectMethod('isIncludedDir', $dir, $paths));

        $paths = ['source_tree/nested_node'];
        $this->assertFalse($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));

    }

    public function testListDirectoryStructureSouldIncludeFiles()
    {
        foreach (['fileA', 'fileB', 'fileC'] as $file) {
            touch($this->testDrive.DIRECTORY_SEPARATOR.$file);
        }
        $dir = $this->fs->directory($this->testDrive);
        $collection = $dir->get();
        $collection->setOutputTree(false);
        $this->assertTrue(3 === count($collection->toArray()));
    }

    public function testListDirectoryStructureSouldIncludeFilesAndDirectories()
    {
        foreach (['fileA', 'fileB', 'fileC'] as $file) {
            touch($this->testDrive.DIRECTORY_SEPARATOR.$file);
        }

        mkdir($dir = $this->testDrive.DIRECTORY_SEPARATOR.'testB');
        foreach (['fileD', 'fileE', 'fileF'] as $file) {
            touch($dir.DIRECTORY_SEPARATOR.$file);
        }

        $dir = $this->fs->directory($this->testDrive);
        $collection = $dir->get();
        $collection->setOutputTree(false);
        $this->assertEquals(7, count($collection->toArray()));

        $collection->setOutputTree(true);

        $files = $collection->toArray();

        $this->assertTrue(is_array($f = arrayGet('%files%.fileA', $files)) && $f['name'] === 'fileA');
        $this->assertTrue(is_array($f = arrayGet('%directories%.testB', $files)) && $f['name'] === 'testB');
        $this->assertTrue(is_array($f = arrayGet('%directories%.testB.%files%', $files)) && isset($f['fileD']));
        $this->assertTrue(
            is_array($f = arrayGet('%directories%.testB.%files%.fileD', $files))&& $f['name'] === 'fileD'
        );
    }

    /*
    public function testListDirectoryStructure()
    {
        //$this->markTestSkipped();
        $this->buildTree();
        $this->fs->touch($this->testDrive.'/baz.png', time() -  10);
        $collection = $this->fs->directory($this->testDrive)
            ->filter(['.*\.(jpe?g$|png|gif)$'])
            ->get()
            ->toJson();
    }
    */

    protected function getPathsAsArgument(array $paths)
    {
        $paths = $this->testDrive.DIRECTORY_SEPARATOR.implode(','.$this->testDrive.DIRECTORY_SEPARATOR, $paths);
        return explode(',', $paths);
    }
}
