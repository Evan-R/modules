<?php

/**
 * This File is part of the Selene\Module\Writer\Tests\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\Constant;

/**
 * @class ConstantTest
 * @package Selene\Module\Writer\Tests\Object
 * @version $Id$
 */
class ConstantTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCompileToConstnatString()
    {
        $this->assertSame('    const FOO = 12;', (new Constant('foo', '12'))->generate());
    }
}
