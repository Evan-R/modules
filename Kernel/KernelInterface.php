<?php

/**
 * This File is part of the Selene\Module\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel;

use \Selene\Module\Events\SubscriberInterface;
use \Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @class KernelInterface
 * @package Selene\Module\Kernel
 * @version $Id$
 */
interface KernelInterface extends HttpKernelInterface
{
    public function getEvents();

    public function registerKernelSubscriber(SubscriberInterface $subscriber);
}
