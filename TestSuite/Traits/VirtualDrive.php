<?php

/**
 * This File is part of the Selene\Module\TestSuite\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\TestSuite\Traits;

use org\bovigo\vfs\vfsStream;

trait VirtualDrive
{
    protected $rootFs;

    /**
     * setupTestDrive
     *
     *
     * @access protected
     * @return mixed
     */
    protected function setupTestDrive()
    {
        $dir = strtolower(strRand(12));
        $this->rootFs = vfsStream::setup($dir);
        return vfsStream::url($dir);
    }

    /**
     * teardownTestDrive
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function teardownTestDrive()
    {
        $this->rootFs = null;
    }
}
