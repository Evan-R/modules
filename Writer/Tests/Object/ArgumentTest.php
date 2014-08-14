<?php

/**
 * This File is part of the Generator\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\Argument;

/**
 * @class ArgumentTest
 * @package Generator\Object
 * @version $Id$
 */
class ArgumentTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateArgs()
    {
        $arg = new Argument('foo', 'stdClass', 'null');

        $this->assertSame('stdClass $foo = null', $arg->generate());
    }

    /** @test */
    public function argTypesShouldBeSettable()
    {
        $arg = new Argument('foo');

        $arg->setType('stdClass');

        $this->assertSame('stdClass $foo', $arg->generate());
    }

    /** @test */
    public function defaultsShouldBeSettable()
    {
        $arg = new Argument('foo', 'stdClass');

        $arg->setDefault('null');

        $this->assertSame('stdClass $foo = null', $arg->generate());
    }

    /** @test */
    public function itShouldBeStringifiable()
    {
        $arg = new Argument('foo');
        $this->assertSame('$foo', (string)$arg);
    }

    /** @test */
    public function itShouldBeReference()
    {
        $arg = new Argument('foo');
        $arg->isReference(true);
        $this->assertSame('&$foo', (string)$arg);
    }
}
