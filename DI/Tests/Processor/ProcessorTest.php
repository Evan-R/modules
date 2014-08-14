<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Processor;

use \Mockery as m;
use \Selene\Module\DI\Processor\Processor;
use \Selene\Module\DI\Processor\ProcessInterface;
use \Selene\Module\DI\Processor\ProcessorInterface;
use \Selene\Module\DI\ContainerInterface;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
     /** @test */
    public function itShouldBeInstantiable()
    {
        $conf = $this->mockConfig();
        $conf->shouldReceive('configure');
        $this->assertInstanceof('Selene\Module\DI\Processor\ProcessorInterface', new Processor($conf));
    }

    /** @test */
    public function itShouldBeConfiguredAtConstruction()
    {

        $proc = null;

        $conf = $this->mockConfig();

        $conf->shouldReceive('configure')->andReturnUsing(function ($p) use (&$proc) {
            $proc = $p;
        });

        $processor = new Processor($conf);

        $this->assertSame($proc, $processor);
    }

    /** @test */
    public function processesShouldBeExecutedInOrder()
    {
        $orders = [];

        $conf = $this->mockConfig();
        $conf->shouldReceive('configure');

        $processor = new Processor($conf);
        $container = $this->getContainerMock();

        foreach ([
            [$p2 = $this->getProcessMock($container, $orders, 2), ProcessorInterface::OPTIMIZE],
            [$p4 = $this->getProcessMock($container, $orders, 4), ProcessorInterface::REMOVE],
            [$p5 = $this->getProcessMock($container, $orders, 5), ProcessorInterface::AFTER_REMOVE],
            [$p3 = $this->getProcessMock($container, $orders, 3), ProcessorInterface::BEFORE_REMOVE],
            [$p1 = $this->getProcessMock($container, $orders, 1), ProcessorInterface::BEFORE_OPTIMIZE],
        ] as $p) {
            $processor->add($p[0], $p[1]);
        }

        $processor->process($container);

        $this->assertSame($orders[0], 1);
        $this->assertSame($orders[1], 2);
        $this->assertSame($orders[2], 3);
        $this->assertSame($orders[3], 4);
        $this->assertSame($orders[4], 5);
    }

    /** @test */
    public function itShouldReportFalseWhenAlreadyProcessed()
    {
        $conf = $this->mockConfig();
        $conf->shouldReceive('configure');

        $processor = new Processor($conf);
        $container = $this->getContainerMock();

        $this->assertTrue($processor->process($container));
        $this->assertFalse($processor->process($container));

        //new container:
        $this->assertTrue($processor->process($this->getContainerMock()));
    }

    /**
     * getContainerMock
     *
     * @access protected
     * @return ContainerInterface
     */
    protected function getContainerMock()
    {
        return m::mock('\Selene\Module\DI\ContainerInterface');
    }

    /**
     * getProcessMock
     *
     * @param ContainerInterface $container
     * @param mixed $orders
     *
     * @access protected
     * @return ProcessInterface
     */
    protected function getProcessMock(ContainerInterface $container, &$orders, $order)
    {
        $process = m::mock('\Selene\Module\DI\Processor\ProcessInterface');

        $process->shouldReceive('process')
            ->with($container)
            ->andReturnUsing(function ($container) use (&$process, &$orders, $order) {
                $orders[] = $order;
            });

        return $process;
    }

    protected function mockConfig()
    {
        return m::mock('\Selene\Module\DI\Processor\ConfigInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
