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

        $paths = [$this->testDrive];
        $this->assertFalse($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));

        $paths = ['source_tree'];
        $this->assertTrue($this->invokeObjectMethod('isIncludedDir', $dir, $this->getPathsAsArgument($paths)));
    }

    public function testListDirectoryStructure()
    {
        $this->buildTree();
        $this->fs->touch($this->testDrive.'/baz.png', time() -  10);
        $collection = $this->fs->directory($this->testDrive)
            ->filter(['.*\.(jpe?g$|png|gif)$'])
            ->notIn(['sub_tree', 'source_tree'])
            ->get()
            //->sortByModDate('asc')
            ->sortByExtension('desc')
            //->sortBySize('asc')
            ->setNestedOutput(false)
            ->toJson();
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
