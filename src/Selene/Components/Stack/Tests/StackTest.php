<?php

/**
 * This File is part of the \Users\malcolm\www\selene_source\src\Selene\Components\Stack\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Stack\Tests;

use \Mockery as m;
use \Selene\Components\Stack\Stack;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @class StackTest
 * @package Selene\Components\Stack\Tests
 * @version $Id$
 */
class StackTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Stack\Stack', new Stack($this->getKernelMock()));
        $this->assertInstanceof('Selene\Components\Stack\Stack', new Stack($this->getStackedKernelMock()));
    }

    /** @test */
    public function itShouldCallHandleOnItsParentKenel()
    {
        $stack = new Stack($kernel = $this->getStackedKernelMock());

        $request = Request::create('/');

        $kernel->shouldReceive('handle')
            ->with($request, HttpKernelInterface::MASTER_REQUEST, true)
            ->andReturn(new Response('success'));

        $response = $stack->handle($request);
        $this->assertSame('success', $response->getContent());
    }

    protected function getKernelMock()
    {
        $kernel = m::mock('Symfony\Component\HttpKernel\HttpKernelInterface');
        return $kernel;
    }

    protected function getStackedKernelMock()
    {
        $kernel = m::mock('Selene\Components\Stack\StackedKernelInterface');
        return $kernel;
    }
}
