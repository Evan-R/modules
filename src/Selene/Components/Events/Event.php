<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

/**
 * @class Event
 * @package Selene\Components\Events
 * @version $Id$
 */
class Event implements EventInterface
{
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * isStopped
     *
     * @var mixed
     */
    protected $isStopped = false;

    /**
     * setName
     *
     * @param mixed $name
     *
     * @access public
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * stopPropagation
     *
     *
     * @access public
     * @return mixed
     */
    public function stopPropagation()
    {
        $this->isStopped = true;
    }

    /**
     * isPropagationStopped
     *
     * @access public
     * @return mixed
     */
    public function isPropagationStopped()
    {
        return $this->isStopped;
    }
}
