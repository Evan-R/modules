<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Selene\Module\DI\Parameters;
use \Selene\Module\DI\StaticParameters;

class StaticParametersTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\DI\ParameterInterface', new StaticParameters([]));
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function itShouldThrowExceptionOnSettingParameters()
    {
        $parameters = new StaticParameters([]);
        $parameters->set('foo', 'bar');
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function itShouldThrowExceptionOnResolvingValues()
    {
        $parameters = new StaticParameters(['foo' => 'bar']);
        $parameters->resolveValue('foo');
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function itShouldThrowExceptionOnResolvingParams()
    {
        $parameters = new StaticParameters(['foo' => 'bar']);
        $parameters->resolveParam('foo');
    }

    /** @test */
    public function itShouldGetParametersThough()
    {
        $parameters = new StaticParameters($params = ['foo' => 'bar']);

        $this->assertTrue($parameters->has('foo'));
        $this->assertSame('bar', $parameters['foo']);

        $this->assertSame($params, $parameters->all());
        $this->assertSame($params, $parameters->getRaw());
    }

    /** @test */
    public function itShouldMergeParameters()
    {
        $parametersA = new StaticParameters($params = ['foo' => 'bar']);
        $parametersB = new StaticParameters($params = ['bar' => 'baz']);

        $parametersA->merge($parametersB);

        $this->assertTrue($parametersA->has('foo'));
        $this->assertTrue($parametersA->has('bar'));

    }

    /** @test */
    public function itShouldThrowExceptionWhenMergingWithWrongParamInstance()
    {
        $parametersA = new StaticParameters($params = ['foo' => 'bar']);
        $parametersB = new Parameters;

        try {
            $parametersA->merge($parametersB);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                sprintf('%s can only be merged with as static parameter collection', get_class($parametersA)),
                $e->getMessage()
            );
        } catch (\InvalidArgumentException $e) {
            $this->fail($e->getMessage());
        }
    }
}
