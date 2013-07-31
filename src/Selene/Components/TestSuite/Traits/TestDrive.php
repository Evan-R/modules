<?php

/**
 * This File is part of the Selene\Components\TestSuite\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\TestSuite\Traits;

/**
 * @class TestDrive
 * @package
 * @version $Id$
 */
trait TestDrive
{
    /**
     * setupTestDrive
     *
     *
     * @access protected
     * @return mixed
     */
    protected function setupTestDrive()
    {
        $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.(time().rand(0, 1000));
        mkdir($dir, 0777, true);
        return realpath($dir);
    }

    /**
     * teardownTestDrive
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function teardownTestDrive($file)
    {
        foreach (new \DirectoryIterator($file) as $f) {
            if ($f->isDot()) {
                continue;
            }
            if ($f->isFile() || $f->isLink()) {
                unlink($f->getRealPath());
            } elseif ($f->isDir()) {
                $this->teardownTestDrive($f->getRealPath());
            }
        }
        rmdir($file);
    }

}
