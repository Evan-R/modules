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
abstract class FilesystemTestCase extends TestCase
{
    /**
     * fs
     *
     * @var Filesystem
     */
    protected $fs;

    protected $testDriver;

    protected function setUp()
    {
        parent::setUp();
        $this->fs = new Filesystem;
        $this->setupTestDrive();
    }

    protected function tearDown()
    {
        if (!is_null($this->testDrive) && is_dir($this->testDrive)) {
            $this->cleanUp($this->testDrive);
        }

        $this->testDrive = null;
    }

    protected function setupTestDrive()
    {
        $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.(time().rand(0, 1000));
        mkdir($dir, 0777, true);

        $this->testDrive = realpath($dir);

    }

    protected function buildTree(\SimpleXMLElement $xml = null)
    {
        if (is_null($xml)) {
            $xml = simplexml_load_file(__DIR__.DIRECTORY_SEPARATOR.'Stubs'.DIRECTORY_SEPARATOR.'tree.xml');
        }

        $this->setupTree($xml, $this->testDrive);
    }

    /**
     * setupTree
     *
     * @param \SimpleXMLElement $xml
     *
     * @access protected
     * @return mixed
     */
    protected function setupTree(\SimpleXMLElement $xml, $baseDir)
    {
        foreach ($xml as $treenode) {
            $dirName = $baseDir . DIRECTORY_SEPARATOR . (string)$treenode->attributes()->name;
            switch($treenode->getName()) {
                case 'directory':
                    $this->createTreeFiles($treenode, $baseDir, true);
                    $this->setupTree($treenode, $dirName);
                    break;
                case 'files':
                    foreach ($treenode as $file) {
                        $this->createTreeFiles($file, $baseDir, false);
                    }
                    break;
                case 'directories':
                        $this->setupTree($treenode, $baseDir);
                    break;
            }
        }
    }

    protected function createTreeFiles(\SimpleXMLElement $file, $dir, $isDir = false)
    {
        $permission = $this->convertPermission((string)$file->attributes()->permission);
        $f = $dir.DIRECTORY_SEPARATOR.$file->attributes()->name;
        if ($isDir) {
            mkdir($f, $permission);
        } else {
            touch($f);
            if ('' !== ($content = (string)$file)) {
                file_put_contents($f, $content);
            }
        }
    }

    protected function convertPermission($perm = null)
    {
        return is_string($perm) ? octdec($perm) : $perm;
    }

    /**
     * cleanUp
     *
     *
     * @access protected
     * @return mixed
     */
    protected function cleanUp($file)
    {
        foreach (new \DirectoryIterator($file) as $f) {
            if ($f->isDot()) {
                continue;
            }
            if ($f->isFile() || $f->isLink()) {
                unlink($f->getRealPath());
            } elseif ($f->isDir()) {
                $this->cleanUp($f->getRealPath());
            }
        }
        rmdir($file);
    }

    public static function assertIsFile($file)
    {
        static::assertThat(is_file($file), static::isTrue());
    }

    public static function assertIsDir($file)
    {
        static::assertThat(is_dir($file), static::isTrue());
    }

    public function assertFilePermissions($expectedPermissions, $file)
    {
        $this->markSkippedIfWindows();
        $this->assertEquals(
            $expectedPermissions,
            (int)substr(sprintf('%o', fileperms($file)), -3)
        );
    }

    public function assertFileOwner($expectedOwner, $file)
    {
        $this->markSkippedIfWindows();
        $this->assertEquals(
            $expectedPermissions,
            (int)substr(sprintf('%o', fileperms($file)), -3)
        );
    }

    protected function getFileGroup($path)
    {
        $stats = stat($path);

        $info = posix_getgrgid($stats['gid']);
        return $info['name'];
    }

    protected function getFileOwner($path)
    {
        $stats = stat($path);
        $info = posix_getpwuid($stats['uid']);
        return $info['name'];
    }
}
