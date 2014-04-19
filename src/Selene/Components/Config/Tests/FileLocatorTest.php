<?php

/**
 * This File is part of the Selene\Components\Config\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\TestSuite\Traits\TestDrive;
use Selene\Components\Config\Locators\FileLocator;
use Selene\Components\Config\Exception\FileLocatorLocateException;

/**
 * @class FileLocatorTest
 * @package
 * @version $Id$
 */
class FileLocatorTest extends TestCase
{
    use TestDrive;

    protected $testDrive;

    protected function setUp()
    {
        parent::setUp();
        $this->testDrive = $this->setupTestDrive();
    }

    protected function tearDown()
    {
        if (file_exists($this->testDrive)) {
            $this->teardownTestDrive($this->testDrive);
        }
        parent::tearDown();
    }

    public function testLocateFiles()
    {
        mkdir($locationA = $this->testDrive.DIRECTORY_SEPARATOR.'users', 0755 & ~umask(), true);
        mkdir($locationB = $this->testDrive.DIRECTORY_SEPARATOR.'files', 0755 & ~umask(), true);

        touch($f1 = $locationA.DIRECTORY_SEPARATOR.'config.php');
        touch($f2 = $locationB.DIRECTORY_SEPARATOR.'config.php');

        $locator = new FileLocator([$locationA, $locationB]);

        $this->assertEquals([$f1, $f2], $locator->locate('config.php'));

        touch($f3 = $locationB.DIRECTORY_SEPARATOR.'config.php');

        $locator = new FileLocator([$locationA, $locationB]);
        $this->assertEquals([$f1, $f3], $locator->locate('config.php'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLocateFilesSouldThrowException()
    {
        mkdir($locationA = $this->testDrive.DIRECTORY_SEPARATOR.'users', 0755 & ~umask(), true);
        $locationB = $this->testDrive.DIRECTORY_SEPARATOR.'files';

        $locator = new FileLocator([$locationA, $locationB]);
        $locator->locate('config.php');
    }
}
