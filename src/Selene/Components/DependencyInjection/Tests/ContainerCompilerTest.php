<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
namespace Selene\Components\DependencyInjection\Tests;

use Mockery as m;
use Selene\Components\TestSuite\TestCase;
use Selene\Components\DependencyInjection\Compiler;
use Selene\Components\DependencyInjection\Container;

class ContainerCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * compiler
     *
     * @var Compiler
     */
    protected $compiler;

    /**
     * container
     *
     * @var Container
     */
    protected $container;

    /**
     * setUp
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        $this->compiler = new Compiler;
        $this->container = new Container;

        $this->compiler->setContainer($this->container);
    }

    public function testCompileContainer()
    {
        $fstub = __NAMESPACE__.'\\CompilerTest\\FooBarClass';

        $this->container->bind('foo', function () {
            return 'bar';
        });

        $this->container
            ->bind('fclass', $fstub)
            ->call('setThis', 'that')
            ->call('setThat', 'thatThat');

        $this->compiler->compile();
    }
}

namespace Selene\Components\DependencyInjection\Tests\CompilerTest;

class FooBarClass
{
    public function setThis($that)
    {
        $this->that = $that;
    }
    public function setThat($thatThat)
    {
        $this->setThat = $thatThat;
    }
}
