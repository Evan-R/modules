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

use \Selene\Components\Events\SubscriberInterface;
use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @class KernelInterface
 * @package Selene\Components\Kernel
 * @version $Id$
 */
interface KernelInterface extends HttpKernelInterface
{
    public function getEvents();

    public function registerKernelSubscriber(SubscriberInterface $subscriber);
}
