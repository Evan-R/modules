<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Meta;

use \Selene\Components\DI\Meta\Data;

/**
 * @class DataTest
 * @package Selene\Components\DI
 * @version $Id$
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $data = new Data('foo');
        $this->assertInstanceof('Selene\Components\DI\Meta\MetaDataInterface', $data);
    }

    /** @test */
    public function itShouldSetItsName()
    {
        $data = new Data(['name' => 'foo']);

        $this->assertSame('foo', $data->getName());

        $data = new Data('foo');

        $this->assertSame('foo', $data->getName());
    }

    /** @test */
    public function itShouldGetAttributes()
    {
        $data = new Data('foo', ['foo' => 'bar']);

        $this->assertSame('bar', $data->get('foo'));
        $this->assertNull($data->get('bar'));
        $this->assertSame('test', $data->get('bar', 'test'));
    }

    /** @test */
    public function itShouldGetNestedParams()
    {
        $data = new Data('foo', [['baz' => 'baz'], ['foo' => 'baz'], ['foo' => 'bar']]);

        $this->assertSame(['baz', 'bar'], $data->get('foo'));
    }

    /** @test */
    public function itShouldReturnParameters()
    {
        $data = new Data('foo', $params = ['foo' => 'baz']);

        $this->assertSame($params, $data->getParameters());

        $data = new Data(['name' => 'foo', 'foo' => 'baz']);

        $this->assertSame($params, $data->getParameters());
    }

    /** @test */
    public function itShouldBeStringable()
    {
        $data = new Data('foo');
        $this->assertSame('foo', (string)$data);
    }

    /** @test */
    public function isShouldThrowExceptionIfNameIsMissing()
    {
        $dummy = new Data('foo');
        try {
            $data = new Data(['foo']);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(sprintf('%s: No name given', get_class($dummy)), $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }
}
