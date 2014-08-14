<?php

/**
 * This File is part of the Selene\Module\TestSuite\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\TestSuite\Tests;

use Selene\Module\TestSuite\TestCase;

/**
 * @class TestCaseTest
 * @package
 * @version $Id$
 */
class TestCaseTest extends TestCase
{
    private $bar;

    public function testInvokeObjectMethod()
    {
        $this->assertSame('bar', $this->invokeObjectMethod('getFoo', $this));
    }

    public function testGetObjectPropertyValue()
    {
        $object = new \StdClass;
        $object->foo = 'bar';
        $this->assertSame('bar', $this->getObjectPropertyValue('foo', $object));
    }

    public function testSetObjectPropertyValue()
    {
        $this->setObjectPropertyValue('bar', 'foo', $this);
        $this->assertSame('foo', $this->getBar());
    }

    protected function getFoo()
    {
        return 'bar';
    }

    protected function getBar()
    {
        return $this->bar;
    }
}
