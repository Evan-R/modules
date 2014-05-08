<?php

/**
 * This File is part of the Selene\Components\Kernel\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Traits;

use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @trait StackedKernelTrait
 *
 * @package Selene\Components\Kernel\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait StackedKernelTrait
{
    private $kernel;

    /**
     * priority
     *
     * @var int
     */
    protected $priority;

    /**
     * setKernel
     *
     * @param HttpKernelInterface $kernel
     *
     * @access public
     * @return void
     */
    public function setKernel(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * getKernel
     *
     *
     * @access public
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface;
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * get the priority on the kernel stack
     *
     * @access public
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority ?: 1000;
    }
}
