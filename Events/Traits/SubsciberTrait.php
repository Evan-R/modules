<?php

/**
 * This File is part of the Selene\Components\Events\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Traits;

trait SubscriberTrait
{
    /**
     * subscriptions
     *
     * @var array
     */
    protected $subscriptions;

    /**
     * getSubscriptions
     *
     *
     * @access public
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->$subscriptions;
    }
}
