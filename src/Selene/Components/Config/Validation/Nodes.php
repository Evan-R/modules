<?php

/**
 * This File is part of the Selene\Components\Config\Validation package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation;

/**
 * @class Nodes
 * @package Selene\Components\Config\Validation
 * @version $Id$
 */
class Nodes
{
    const TYPE_LIST = 'list';
    const TYPE_BOOL = 'boolean';
    const TYPE_ARRAY = 'array';
    const TYPE_SCALAR = 'scalar';
    const TYPE_STRING = 'string';

    protected static $typeMap;

    protected static $separator = '=>';

    protected $parent;

    public function __construct($parent = null)
    {
        $this->parent = $parent;
        static::setTypeMap();
    }

    /**
     * setParent
     *
     * @param mixed $parent
     *
     * @access public
     * @return mixed
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function end()
    {
        return $this->parent;
    }

    public function booleanNode($name)
    {
        return $this;
    }

    public function listNode($name)
    {
        return $this;
    }

    public function arrayNode($name)
    {
        return $this;
    }

    public function scalarNode($name)
    {
        return $this;
    }

    /**
     * addNode
     *
     * @param mixed $name
     * @param mixed $type
     *
     * @access public
     * @return mixed
     */
    public function addNode($name, $type)
    {
        if (!array_key_exists($type, static::$typeMap)) {
            throw new \InvalidArgumentException(sprintf('unknown type "%s"', $type));
        }

        $class = static::$typeMap[$type];
        $this->nodes[$path = $this->getPath($name)] = new static::$typeMap[$type]($name, $path);
    }

    /**
     * getPath
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function getPath($name)
    {
        return $this->parent->getPath() . static::$separator . $name;
    }

    public function getFoo(callable $foo)
    {
        return $foo();
    }

    /**
     * setTypeMap
     *
     * @access protected
     * @return mixed
     */
    protected static function setTypeMap()
    {
        if (null === static::$typeMap) {
            static::$typeMap = [
                self::TYPE_BOOL    => __NAMESPACE__ . '\\BooleanBuilder',
                self::TYPE_ARRAY   => __NAMESPACE__ . '\\ArrayBuilder',
                self::TYPE_SCALAR  => __NAMESPACE__ . '\\BooleanBuilder',
                self::TYPE_STRING  => __NAMESPACE__ . '\\ScalarBuilder',
            ];
        }
    }
}
