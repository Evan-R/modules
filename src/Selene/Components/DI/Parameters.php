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
 * @class Parameters implements ParameterInterface Parameters
 * @see ParameterInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Parameters implements ParameterInterface
{
    private $parameters;

    private $parsedKeys;

    /**
     * __construct
     *
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        $this->parameters = [];
        $this->parsedKeys = [];
    }

    /**
     * add
     *
     * @param mixed $param
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function set($param, $value)
    {
        $this->parameters[$param] = $value;
    }

    /**
     * get
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function get($param, $default = null)
    {
        return $this->has($param) ?
            $this->parameters[$param] : (null !== $default ? $default : $param);
    }

    /**
     * merge
     *
     * @param ParameterInterface $parameters
     *
     * @access public
     * @return mixed
     */
    public function merge(ParameterInterface $parameters)
    {
        if ($this === $parameters) {
            throw new \InvalidArgumentException('%s: cannot merge same instance', get_class($this));
        }

        $this->parameters = array_merge($this->parameters, $parameters->getParameters());
        $this->parsedKeys = array_merge($this->parsedKeys, $parameters->getKeys());
    }

    /**
     * getParameters
     *
     * @access public
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    public function getKeys()
    {
        return $this->parsedKeys;
    }

    /**
     * has
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function has($param)
    {
        return array_key_exists($param, $this->parameters);
    }

    /**
     * replaceString
     *
     * @param mixed $string
     *
     * @access public
     * @return mixed
     */
    public function replaceString($string)
    {
        return str_replace(array_keys($this->parameters), $this->parameters, $string);
    }

    private function escapeKey($param)
    {
        return str_replace('\\', '\\\\', $param);
    }
}
