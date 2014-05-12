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
 * @interface StackedKernel extends HttpKernelInterface
 * @see HttpKernelInterface
 *
 * @package Selene\Components\Stack
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface StackedKernelInterface extends HttpKernelInterface
{
    /**
     * setKernel
     *
     * @param HttpKernelInterface $kernel
     *
     * @access public
     * @return void
     */
    public function setKernel(HttpKernelInterface $kernel);

    /**
     * getKernel
     *
     *
     * @access public
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface;
     */
    public function getKernel();

    /**
     * get the priority on the kernel stack
     *
     * @access public
     * @return integer
     */
    public function getPriority();
}
