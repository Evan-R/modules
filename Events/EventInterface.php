<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events;

/**
 * @class EventInterface
 * @package
 * @version $Id$
 */
interface EventInterface
{
    /**
     * stopPropagation
     *
     * @access public
     * @return void
     */
    public function stopPropagation();

    /**
     * isPropagationStopped
     *
     * @access public
     * @return boolean
     */
    public function isPropagationStopped();

    /**
     * getName
     *
     * @param mixed $name
     *
     * @return string
     */
    public function getEventName();

    /**
     * setName
     *
     * @param string $name
     *
     * @return void
     */
    public function setEventName($name);

    /**
     * setDispatcher
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setEventDispatcher(DispatcherInterface $dispatcher);

    /**
     * getDispatcher
     *
     * @return DispatcherInterface
     */
    public function getEventDispatcher();
}
