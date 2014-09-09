<?php

/*
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Common\Traits\Setter;

/**
 * @trait AttributesTrait
 *
 * @package Selene\Module\Common\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait AttributesTrait
{
    use Getter;
    use Setter;

    protected $attributes;

    /**
     * get
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return void
     */
    public function get($key, $default = null)
    {
        return $this->getDefaultUsingKey($this->attributes, $key, $default);
    }

    /**
     * set
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * has
     *
     * @param mixed $key
     *
     * @return void
     */
    public function has($key)
    {
        return $this->hasKey($this->attributes, $key);
    }

    /**
     * remove
     *
     * @param mixed $key
     *
     * @return void
     */
    public function remove($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * initialize
     *
     * @param array $data
     *
     * @return void
     */
    public function initialize(array $data)
    {
        $this->attributes = $data;
    }

    /**
     * keys
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->attributes);
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        return $this->attributes;
    }
}
