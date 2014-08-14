<?php

/**
 * This File is part of the Selene\Module\DI\Dumper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Dumper;

/**
 * @class DefinitionAlias
 * @package Selene\Module\DI\Dumper
 * @version $Id$
 */
class DefinitionAlias
{
    private $alias;

    private $definition;

    /**
     * Constructor.
     *
     * @param DefinitionInterface $definition
     */
    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * getAlias
     *
     *
     * @access public
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias ?: $this->definition->getClass();
    }
}
