<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Mapper;

use \Mockery as m;
use \Selene\Components\Routing\Mapper\ParameterMapper;
use \Selene\Components\Routing\Tests\Mapper\Stubs\ControllerStub as Controller;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class ParameterMapperTest
 * @package Selene\Components\Routing
 * @version $Id$
 */
class ParameterMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Routing\Mapper\ParameterMapper', new ParameterMapper);
    }

    /** @test */
    public function itShouldMapInputToMethodArguments()
    {
        $input = [
            'baz' => 3,
            'foo' => 1,
            'bar' => 2,
        ];

        $expected = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ];

        $mapper = new ParameterMapper;
        $reflection = new \ReflectionObject(new Controller($this));

        $this->assertSame($expected, $mapper->map($this->getRequest(), $input, $reflection, 'indexAction'));
    }

    /** @test */
    public function itShouldReturnFalseIfArgumentIsMissing()
    {
        $input = [
            'baz' => 3,
            'bar' => 2,
        ];

        $mapper = new ParameterMapper;
        $reflection = new \ReflectionObject(new Controller($this));

        $this->assertFalse($mapper->map($this->getRequest(), $input, $reflection, 'indexAction'));
    }

    /** @test */
    public function itShouldReturnFalseIfMethodDoesNotExist()
    {
        $input = [];
        $mapper = new ParameterMapper;
        $reflection = new \ReflectionObject(new Controller($this));

        $this->assertFalse($mapper->map($this->getRequest(), $input, $reflection, 'barAction'));
    }

    /** @test */
    public function itShouldReturnFalseIfMethodIsNotPublic()
    {
        $input = [];
        $mapper = new ParameterMapper;
        $reflection = new \ReflectionObject(new Controller($this));

        $this->assertFalse($mapper->map($this->getRequest(), $input, $reflection, 'fooAction'));
    }

    /** @test */
    public function itShouldSetRequestRequestObject()
    {
        $input = [];
        $mapper = new ParameterMapper;
        $reflection = new \ReflectionObject(new Controller($this));

        $res = $mapper->map($req = Request::create('/', 'GET'), $input, $reflection, 'requestAction');

        list($arg) = array_values((array)$res);

        $this->assertSame($req, $arg);
    }

    protected function getRequest()
    {
        return m::mock('\Symfony\Component\HttpFoundation\Request');
    }

    protected function tearDown()
    {
        m::close();
    }
}
