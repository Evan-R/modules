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

use Selene\Components\TestSuite\TestCase;
use Selene\Components\DependencyInjection\Container;

/**
 * @class ContainerTest extends TestCase ContainerTest
 * @see TestCase
 *
 * @package Selene\Components\DependencyInjection\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ContainerTest extends TestCase
{
    /**
     * @var ClassName
     */
    protected $object;

    protected function setUp()
    {
        $this->container = new Container;
    }

    /**
     * @test
     */
    public function testSetUpContainer()
    {
        $this->container->setParam('foo.options', ['opt1', 'opt2']);

        $this->container->setParam('foo.service.class', 'Acme\FooService');
        $this->container->setParam('bar.service.class', 'Acme\BarService');
        $this->container->setParam('baz.service.class', 'Acme\BazService');
        $this->container->setParam('bla.service.class', 'Acme\BlaService');
        $this->container->setParam('Acme\BarService', ['$foo']);

        $this->container->setService('foo', '@foo.service.class', ['@foo.options']);

        $this->container->setService('bar', '@bar.service.class', ['$foo']);

        $this->container->setService('baz', '@baz.service.class');
        $this->container->setService('bla', '@bla.service.class')
            ->addArgument('@foo.options');

        $service = $this->container->getService('bar');
        var_dump($service);

        $service = $this->container->getService('baz');
        var_dump($service);

        $service = $this->container->getService('bla');
        var_dump($service);

        //var_dump($service);

    }

    protected function tearDown()
    {

    }
}

namespace Acme;

class FooService
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }
}

class BarService
{
    private $foo;

    public function __construct(FooService $foo)
    {
        $this->foo = $foo;
    }
}

class BazService extends BarService
{

}

class BlaService extends BarService
{
    public function __construct(FooService $foo, $bam = null)
    {
        parent::__construct($foo);
        $this->bam = $bam;
    }
}
