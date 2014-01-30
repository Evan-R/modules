<?php

/**
 * This File is part of the Selene\Components\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests;

use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Common\Tests\Stubs\Serializable\JsonSerializableStub as TestStub;

class SerializeableJsonTest extends TestCase
{
    /**
     * @test
     */
    public function testTestCase()
    {
        $stub = new TestStub;
    }
}
