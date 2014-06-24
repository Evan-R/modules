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

use \Selene\Components\DI\Dumper\Stubs\ReturnStatement as Ret;

class ReturnStatementTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldIndentCorrectly()
    {
        $ret = new Ret('foo', 4);

        $this->assertSame('    return foo;', (string)$ret);

        $ret = new Ret('foo', 8);

        $this->assertSame('        return foo;', (string)$ret);
    }
}
