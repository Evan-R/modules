<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection;

/**
 * @class Parameters
 * @package Selene\Components\DependencyInjection
 * @version $Id$
 */
class Parameters
{
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
        $this->parameters[$this->getKey($param)] = $value;
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
        return $this->has($param = $this->escapeKey($param)) ?
            $this->parameters[$param] : (null !== $default ? $default : $param);
    }


    private function getKey($param)
    {
        return '@'.$this->escapeKey($param);
    }

    private function has($param)
    {
        return array_key_exists($param, $this->parameters);
    }

    private function escapeKey($param)
    {
        return str_replace('\\', '\\\\', $param);
    }
}
