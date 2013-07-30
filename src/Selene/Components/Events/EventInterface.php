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
     * @access public
     * @return string
     */
    public function getName();

    /**
     * setName
     *
     * @param string $name
     *
     * @access public
     * @return void
     */
    public function setName($name);
}
