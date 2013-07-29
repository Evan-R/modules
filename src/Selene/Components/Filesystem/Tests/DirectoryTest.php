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

    public function testListDirectoryStructure()
    {
        $this->markTestSkipped();
        $this->buildTree();
        $this->fs->touch($this->testDrive.'/baz.png', time() -  10);
        $collection = $this->fs->directory($this->testDrive)
            //->filter(['.*\.(jpe?g$|png|gif)$'])
            //->filter(['.*\.txt$'])
            //->notIn(['sub_tree', 'source_tree'])
            //->in(['source_tree'])
            ->in(['source_tree/nested_subtree'])
            //->notIn(['source_tree/nested_node'])
            ->get()
            ->toJson();

            //var_dump($collection->getPool());
            //die;
            //->sortByModDate('asc')
            //->sortByExtension('desc');
            //->sortBySize('asc')
            //->setNestedOutput(false)
            //->toJson();
            //->filter('.*\.jpe?g$')
            //->in(['sub_tree'])
            //->get()
            //->getPool();
            //->toJson();

        //var_dump($collection);
        echo($collection);
    }


    protected function getPathsAsArgument(array $paths)
    {
        $paths = $this->testDrive.DIRECTORY_SEPARATOR.implode(','.$this->testDrive.DIRECTORY_SEPARATOR, $paths);
        return explode(',', $paths);
    }
}
