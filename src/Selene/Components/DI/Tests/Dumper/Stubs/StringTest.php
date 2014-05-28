<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Stubs;

use \Selene\Components\DI\Dumper\Stubs\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShoulWrapAString()
    {
        $str = new String('foo');

        $this->assertSame('foo', $str->dump());
        $this->assertSame('foo', (string)$str);
    }
}
