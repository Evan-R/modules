<?php

/**
 * This File is part of the Selene\Components\Kernel\Subscriber package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Subscriber;

use \Selene\Components\Kernel\KernelInterface;
use \Selene\Components\Events\SubscriberInterface;
use \Selene\Components\Events\DispatcherInterface;

/**
 * @interface KernelSubscriber
 * @package Selene\Components\Kernel\Subscriber
 * @version $Id$
 */
interface KernelSubscriber extends SubscriberInterface
{
    public function subscribeToKernel(KernelInterface $kernel);
}
