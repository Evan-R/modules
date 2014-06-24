<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

/**
 * @class Reference
 * @package Selene\Components\DI
 * @version $Id$
 */
class Reference
{
    /**
     * definition
     *
     * @var mixed
     */
    private $id;

    /**
     * @param DefinitionInterface $definition
     *
     * @access public
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * get
     *
     * @access public
     * @return string
     */
    public function get()
    {
        return $this->id;
    }

    /**
     * __toString
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}
