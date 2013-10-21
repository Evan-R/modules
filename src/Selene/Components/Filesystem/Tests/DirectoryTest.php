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

    /**
     * @test
     */
    public function testMkdir()
    {
        $dir = new Directory($this->fs, $this->testDrive);
        $dir->mkdir('test');
        $this->assertIsDir($this->testDrive.DIRECTORY_SEPARATOR.'test');
    }

    /**
     * @test
     */
    public function testRemove()
    {
        mkdir($test = $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir = new Directory($this->fs, $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir->remove();

        $this->assertFalse(file_exists($test));
    }

    /**
     * @test
     */
    public function testCopy()
    {
        mkdir($test = $this->testDrive.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'test', 0777, true);
        $dir = new Directory($this->fs, $this->testDrive.DIRECTORY_SEPARATOR.'test');
        $dir->copy();
        $this->assertFileEquals(dirname($test), $this->testDrive.DIRECTORY_SEPARATOR.'test copy 1');
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function testIgnoreVcs()
    {
        $this->fs->mkdir($this->testDrive.DIRECTORY_SEPARATOR.'.git'.DIRECTORY_SEPARATOR.'0983912380921830809sa89d89a0s8d', 0775, true);

        $dir = $this->fs->directory($this->testDrive);
        $array = $dir->toArray();

        $this->assertTrue(empty($array));

        $this->fs->mkdir($this->testDrive.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'.git'.DIRECTORY_SEPARATOR.'0983912380921830809sa89d89a0s8d', 0775, true);

        $dir = $this->fs->directory($this->testDrive);
        $array = $dir->toArray();

        $this->assertTrue(isset($array['%directories%']['foo']));
        $this->assertFalse(isset($array['%directories%']['foo']['.git']));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

        $this->assertTrue(is_array($f = arrayGet($files, '%files%.fileA')) && $f['name'] === 'fileA');
        $this->assertTrue(is_array($f = arrayGet($files, '%directories%.testB')) && $f['name'] === 'testB');
        $this->assertTrue(is_array($f = arrayGet($files, '%directories%.testB.%files%')) && isset($f['fileD']));
        $this->assertTrue(
            is_array($f = arrayGet($files, '%directories%.testB.%files%.fileD'))&& $f['name'] === 'fileD'
        );
    }

    /**
     * @test
     */
    public function testListDirectoryRestrictDepth()
    {
        foreach (['fileA', 'fileB', 'fileC'] as $file) {
            touch($this->testDrive.DIRECTORY_SEPARATOR.$file);
        }

        mkdir($dir = $this->testDrive.DIRECTORY_SEPARATOR.'testB');
        foreach (['fileD', 'fileE', 'fileF'] as $file) {
            touch($dir.DIRECTORY_SEPARATOR.$file);
        }

        mkdir($dir = $this->testDrive.DIRECTORY_SEPARATOR.'testB'.DIRECTORY_SEPARATOR.'testC');
        foreach (['fileG', 'fileH', 'fileI'] as $file) {
            touch($dir.DIRECTORY_SEPARATOR.$file);
        }

        $dir = $this->fs->directory($this->testDrive);
        $collection = $dir->depth(0)->get();
        $collection->setOutputTree(false);

        $this->assertEquals(4, count($collection->toArray()));

        $collection = $dir->depth(1)->get();
        $collection->setOutputTree(false);

        $this->assertEquals(8, count($collection->toArray()));

        $collection = $dir->depth(2)->get();
        $collection->setOutputTree(false);

        $this->assertEquals(11, count($collection->toArray()));
    }

    /**
     * @test
     */
    public function testListDirectoryOnlyFiles()
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

        $collection = $dir->files()->get();
        $collection->setOutputTree(false);


        $this->assertEquals(6, count($collection->toArray()));
    }

    //public function testListDirectoryStructure()
    //{
    //    //$this->markTestSkipped();
    //    $this->buildTree();
    //    $this->fs->touch($this->testDrive.'/baz.png', time() -  10);
    //    $collection = $this->fs->directory($this->testDrive)
    //        ->filter(['.*\.(jpe?g$|png|gif)$'])
    //        ->files()
    //        ->get();
    //    $collection->setOutputTree(true, false);
    //    var_dump($collection->toArray());
    //}

    /**
     * @test
     */
    public function testGetRealPath()
    {
        $dir = $this->fs->directory($this->testDrive);

        $pathA = $this->testDrive.DIRECTORY_SEPARATOR.'foo';
        $this->assertSame($pathA, $pathA);
        $this->assertSame($pathA, $dir->getRealPath('foo'));
        $this->assertSame($this->testDrive, $dir->getRealPath());
    }

    /**
     * @test
     */
    public function testIgnoreVCSPattern()
    {
        $this->buildTree();
        $dir = $this->fs->directory($this->testDrive.DIRECTORY_SEPARATOR, Directory::IGNORE_VCS);
        $collection = $dir->get();
        $collection->setOutputTree(false);

        $ignored = ['.git', '.svn'];

        foreach($collection->toArray() as $file) {
            if (in_array(basename($file), $ignored)) {
                $this->fail();
            }
        }
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function testNotIgnoreVCSPattern()
    {
        $this->buildTree();
        $dir = $this->fs->directory($this->testDrive.DIRECTORY_SEPARATOR, null);
        $collection = $dir->get();
        $collection->setOutputTree(false);

        $hasVCS = 0;

        $ignored = ['.git', '.svn'];

        foreach($collection->toArray() as $file) {
            if (in_array(basename($file), $ignored)) {
                $hasVCS++;
            }
        }

        $this->assertEquals(2, $hasVCS);
    }

    public function testListDirAndIncludeSelf()
    {
        $this->buildTree();
        $dir = $this->fs->directory($this->testDrive.DIRECTORY_SEPARATOR, Directory::IGNORE_VCS|Directory::IGNORE_DOT);

        $collection = $dir->get();
        $collection->setOutputTree(true, false);

        $files = $collection->toArray();

        $this->assertTrue(count($files) === 1);
        $this->assertTrue(isset($files['%directories%']) && 'source_tree' === $files['%directories%'][0]['name']);

        $collection = $dir->includeSelf()->get();
        $collection->setOutputTree(true, false);

        $files = $collection->toArray();

        $this->assertTrue(isset($files['name']) && basename($this->testDrive) === $files['name']);
    }



    protected function getPathsAsArgument(array $paths)
    {
        $paths = $this->testDrive.DIRECTORY_SEPARATOR.implode(','.$this->testDrive.DIRECTORY_SEPARATOR, $paths);
        return explode(',', $paths);
    }


}
