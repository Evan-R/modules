<?php

/*
 * This File is part of the Selene\Module\Routing\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests;

use \Mockery as m;
use \Selene\Module\Routing\RouterCache;

/**
 * @class RouterCacheTest
 *
 * @package Selene\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouterCacheTest extends \PHPUnit_Framework_TestCase
{
    protected $store;
    protected $loader;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Module\Routing\RouterCache',
            new RouterCache(
                'source',
                'manifest',
                $this->mockStorage(),
                $this->mockLoader()
            )
        );
    }

    protected function mockStorage()
    {
        return $this->store = m::mock('Selene\Module\Routing\Cache\StorageInterface');
    }

    protected function loaderShouldLoadFile($file)
    {

    }

    /**
     * storeShouldReportFresh
     *
     * @param int $time
     *
     * @return void
     */
    protected function storeShouldReportFresh($time)
    {
        $this->mockStore();
        $this->store->shouldReceive('getLastWriteTime')->andReturn($time - 10);
    }

    /**
     * storeShouldReportDirty
     *
     * @param int $time
     *
     * @return void
     */
    protected function storeShouldReportDirty($time)
    {
        $this->mockStore();
        $this->store->shouldReceive('getLastWriteTime')->andReturn($time);
    }

    /**
     * mockStore
     *
     * @return void
     */
    protected function mockStore()
    {
        $store = m::mock('Selene\Module\Routing\Cache\StoreInterface');

        return $this->store = $store;
    }

    /**
     * mockStore
     *
     * @return void
     */
    protected function mockResources()
    {
        $resources = m::mock('Selene\Module\Config\Resource\CollectorInterface');

        return $this->resources = $resources;
    }

    /**
     * mockLoader
     *
     * @return void
     */
    protected function mockLoader()
    {
        $loader = m::mock('Selene\Module\Config\Loader\LoaderInterface');
        $loader->shouldReceive('addListener')->with(m::any());

        return $this->loader = $loader;
    }
}
