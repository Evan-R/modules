<?php

/**
 * This File is part of the Selene\Components\Stack package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Stack;

use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @class StackBuilder
 *
 * @package Selene\Components\Stack
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
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->queue = new \SplPriorityQueue;
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
        $app = $this->kernel;

        while ($this->queue->valid()) {

            $kernel = $this->queue->current();
            $kernel->setKernel($app);
            $this->queue->extract();

            $app = $kernel;
        }

        return new Stack($app);
    }
}
