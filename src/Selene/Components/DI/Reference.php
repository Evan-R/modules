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
    private $service;

    /**
     * @param DefinitionInterface $definition
     *
     * @access public
     */
    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * get
     *
     * @access public
     * @return string
     */
    public function get()
    {
        return $this->service;
    }

    /**
     * __toString
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return sprintf('$%s', $this->service);
    }
}
