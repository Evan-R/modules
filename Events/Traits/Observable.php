<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Traits;

use SplObserver;
use Selene\Module\Events\ObserverInterface;

/**
 * @trait AbstractObservable
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
trait Observable
{
    /**
     * observers
     *
     * @var array
     */
    protected $observers = [];

    /**
     * notify
     *
     * @param mixed $param
     *
     * @return void
     */
    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->notify($this);
        }
    }

    /**
     * addObserver
     *
     * @param ObserverInterface $observer
     *
     * @return void
     */
    public function addObserver(ObserverInterface $observer)
    {
        if (!in_array($observer, $this->observers)) {
            $this->observers[] = $observer;
        }
    }

    /**
     * addObserver
     *
     * @param ObserverInterface $observer
     *
     * @return void
     */
    public function removeObserver(ObserverInterface $observer)
    {
        if (0 >= ($index = array_search($observer, $this->observers))) {
            unset($this->observers[$index]);
        }
    }

    /**
     * attach
     *
     * @param ObserverInterface $observer
     *
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        return $this->addObserver($observer);
    }

    /**
     * dettach
     *
     * @param ObserverInterface $observer
     *
     * @return void
     */
    public function detach(SplObserver $observer)
    {
        $this->removeObserver($observer);
    }

}
