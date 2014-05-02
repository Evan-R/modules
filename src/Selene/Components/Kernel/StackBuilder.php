<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @class StackBuilder
 *
 * @package Selene\Components\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class StackBuilder
{
    /**
     * app
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $app;

    /**
     * stack
     *
     * @var \SplStack
     */
    private $queue;

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     *
     * @access public
     */
    public function __construct()
    {
        $this->queue = new \SplPriorityQueue;
    }

    /**
     * setKernel
     *
     * @param HttpKernelInterface $app
     *
     * @access public
     * @return mixed
     */
    public function setKernel(HttpKernelInterface $app)
    {
        $this->app  = $app;
    }


    /**
     * add
     *
     * @param \Selene\Components\Kernel\StackedKernelInterface $kernel
     *
     * @access public
     * @return void
     */
    public function add(StackedKernelInterface $kernel)
    {
        $this->queue->insert($kernel, $kernel->getPriority());
    }

    /**
     * build
     *
     * @param AppCoreInterface $app
     *
     * @access public
     * @return \Selene\Components\Kernel\Stack
     */
    public function make()
    {
        $app = $this->app;

        while ($this->queue->valid()) {

            $kernel = $this->queue->current();
            $kernel->setKernel($app);
            $this->queue->extract();

            $app = $kernel;
        }

        return new Stack($app);
    }
}
