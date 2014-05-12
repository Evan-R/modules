<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
namespace Selene\Components\Routing\Tests\Controller;

use \Selene\Components\Routing\Controller\ResolverParser;

class ResolverParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $parser = new ResolverParser;
        $this->assertInstanceOf('\Selene\Components\Routing\Controller\ResolverParser', $parser);
    }

    /** @test */
    public function itShouldReturnConcreateControllerClasses()
    {
        $parser = new ResolverParser(['foo' => 'Foo\Bar']);
        $parts = $parser->parse('foo:index:index');

        $this->assertSame(['Foo\Bar\Controller\IndexController', 'indexAction'], $parts);

        $parser = new ResolverParser(['foo' => 'Foo\Bar']);
        $parts = $parser->parse('foo:IndexController:index');

        $this->assertSame(['Foo\Bar\Controller\IndexController', 'indexAction'], $parts);

        $parser = new ResolverParser(['foo' => 'Foo\Bar']);
        $parts = $parser->parse('foo:IndexController:indexAction');

        $this->assertSame(['Foo\Bar\Controller\IndexController', 'indexAction'], $parts);

        $parser = new ResolverParser(['foo' => 'Foo\Bar']);
        $parts = $parser->parse('foo:IndexController:IndexAction');

        $this->assertSame(['Foo\Bar\Controller\IndexController', 'indexAction'], $parts);
    }

    /** @test */
    public function itShouldNotSupportEmptyControllerSpace()
    {
        $parser = new ResolverParser(['foo' => 'Foo\Bar']);

        $this->assertFalse($parser->supports('foo::fam'));

        $this->assertTrue($parser->supports('foo:index:index'));
    }
}
