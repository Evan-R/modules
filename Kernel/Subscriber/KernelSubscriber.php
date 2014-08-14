<?php

/**
 * This File is part of the Selene\Module\Kernel\Subscriber package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel\Subscriber;

use \Selene\Module\Kernel\KernelInterface;
use \Selene\Module\Events\SubscriberInterface;
use \Selene\Module\Events\DispatcherInterface;

/**
 * @interface KernelSubscriber
 * @package Selene\Module\Kernel\Subscriber
 * @version $Id$
 */
interface KernelSubscriber extends SubscriberInterface
{
    public function subscribeToKernel(KernelInterface $kernel);
}
