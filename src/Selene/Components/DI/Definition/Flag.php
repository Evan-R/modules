<?php

/**
 * This File is part of the Selene\Components\DI\Definition package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

/**
 * @class Flag
 * @package Selene\Components\DI\Definition
 * @version $Id$
 */
class Flag implements FlagInterface
{
    protected $name;

    protected $definition;

    public function __construct($name, DefinitionInterface $definition)
    {
        $this->name = $name;
        $this->definition = $definition;
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getDefinition
     *
     * @access public
     * @return mixed
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
