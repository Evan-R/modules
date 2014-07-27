<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator\Nodes;

use \Selene\Components\Config\Validator\Nodes\Children;
use \Selene\Components\Config\Validator\Nodes\StringNode;

/**
 * @class ChildrenTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class ChildrenTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $cld = new Children;

        $cld->attach($a = new StringNode);
        $cld->attach($b = new StringNode);
        $cld->attach($c = new StringNode);

        $this->assertSame(3, count($cld));

        $this->assertTrue($cld->has($a));
        $this->assertTrue($cld->has($b));
        $this->assertTrue($cld->has($c));

        $this->assertSame($a, $cld->first());
        $this->assertSame($c, $cld->last());

        $cld->detach($a);
        $cld->detach($c);

        $this->assertFalse($cld->has($a));
        $this->assertFalse($cld->has($c));

        $this->assertSame(1, count($cld));
    }

    /** @test */
    public function itShouldBeCountable()
    {
        list ($cld, $a, $b, $c, $d) = $this->prepareChildren();

        $this->assertSame(4, $cld->count());
    }

    /** @test */
    public function itShouldHaveFirstAndLast()
    {
        list ($cld, $a, $b, $c, $d) = $this->prepareChildren();

        $this->assertSame($a, $cld->first());
        $this->assertSame($d, $cld->last());
    }

    /** @test */
    public function itUnsetChildren()
    {
        list ($cld, $a, $b, $c, $d) = $this->prepareChildren();

        $cld->detach($b);
        $this->assertFalse($cld->has($b));
        $this->assertSame(3, $cld->count());
    }

    /** @test */
    public function itShouldBeIteratable()
    {
        list ($cld, $a, $b, $c, $d) = $this->prepareChildren();

        $res = [];

        foreach ($cld as $child) {
            $res[] = $child;
        }

        $this->assertSame($res, [$a, $b, $c, $d]);
    }

    /** @test */
    public function itShouldResetNodes()
    {
        list ($cld, $a, $b, $c, $d) = $this->prepareChildren();

        $cld->detachAll();
        $this->assertSame(0, $cld->count());
    }

    /**
     * prepareChildren
     *
     * @return array
     */
    protected function prepareChildren()
    {
        $cld = new Children;

        $cld->attach($a = new StringNode);
        $cld->attach($b = new StringNode);
        $cld->attach($c = new StringNode);
        $cld->attach($d = new StringNode);

        $a->setKey('a');
        $b->setKey('b');
        $c->setKey('c');
        $d->setKey('d');

        return [$cld, $a, $b, $c, $d];
    }
}
