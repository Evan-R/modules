<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Tests;

use Selene\Components\Cache\Storage;
use Selene\Components\Filesystem\Filesystem;
use Selene\Components\Cache\Driver\FilesystemDriver;
use Selene\Components\TestSuite\Traits\TestDrive;

/**
 * @class StorageFilesystemTest
 * @see StorageTestCase
 *
 * @package Selene\Components\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class StorageFilesystemTest extends StorageTestCase
{
    use TestDrive;

    protected $testDrive;

    protected function setUp()
    {
        parent::setUp();
        $this->testDrive = $this->setupTestDrive();
        $this->cache = new Storage(new FilesystemDriver(new Filesystem, $this->testDrive), 'mycache');
    }

    /**
     * tearDown
     *
     *
     * @access protected
     * @return mixed
     */
    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->testDrive)) {
            $this->teardownTestDrive($this->testDrive);
        }
        $this->testDrive = null;
    }
}
