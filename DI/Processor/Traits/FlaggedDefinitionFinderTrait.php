<?php

/**
 * This File is part of the Selene\Module\DI\Processor\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Processor\Traits;

/**
 * @class FlaggedDefinitionFinderTrait
 * @package Selene\Module\DI\Processor\Traits
 * @version $Id$
 */
trait DefinitionFinderTrait
{
    /**
     * findFlaggedDefinitions
     *
     *
     * @access protected
     * @return array
     */
    protected function findFlaggedDefinitions()
    {
        return array_filter($this->container->getDefinitions(), function ($definitions) {
            return $definition->hasFlags();
        });
    }

    /**
     * findDefinitionsWithFlag
     *
     * @param mixed $flag
     *
     * @access protected
     * @return array
     */
    protected function findDefinitionsWithFlag($flag)
    {
        return array_filter($this->container->getDefinitions(), function ($definitions) use ($flag) {
            return $definition->hasFlag($flag);
        });
    }
}
