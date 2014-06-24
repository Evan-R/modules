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
    /**
     * removedMetaData
     *
     * @var array
     */
    private $obsoleteMetaData;

    /**
     * @param mixed $parent
     *
     * @access public
     * @return mixed
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->obsoleteMetaData = [];
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

    /**
     * removeMetaData
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function removeMetaData($name)
    {
        $this->obsoleteMetaData[] = $name;

        return parent::removeMetaData($name);
    }

    /**
     * getObsoleteMetaData
     *
     *
     * @access public
     * @return array
     */
    public function getObsoleteMetaData()
    {
        return $this->obsoleteMetaData;
    }
}
