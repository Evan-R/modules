<?php

/**
 * This File is part of the Selene\Components\DI\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

/**
 * Class ParentDefinition
 * @package Selene\Components\DI
 */
class ParentDefinition extends AbstractDefinition
{
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
     * replaceArgument
     *
     * @param mixed $argument
     * @param int $index
     *
     * @access public
     * @return mixed
     */
    public function replaceArgument($argument, $index = 0)
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException();
        }
        $this->arguments['index_'  .(string)(int)$index] = $argument;
        //var_dump($this->arguments);
    }
}
