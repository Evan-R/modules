<?php

/**
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Stubs\Traits;

use \Selene\Module\Common\Traits\Getter;

/**
 * @class GetterClass
 *
 * @package Selene\Module\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class GetterClass
{
    use Getter;

    protected $attributes;

    /**
     * @param array $attributes
     *
     * @access public
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getterGetDefault($key, $default = null)
    {
        return $this->getDefault($this->attributes, $key, $default);
    }

    public function getterGetDefaultUsing($key, callable $use)
    {
        return $this->getDefaultUsing($this->attributes, $key, $use);
    }

    public function getterGetDefaultUsingKey($key, $default = null)
    {
        return $this->getDefaultUsingKey($this->attributes, $key, $default);
    }

    public function getterGetDefaultArray($key, $default = null)
    {
        return $this->getDefaultArray($this->attributes, $key, $default);
    }

    public function getterHasKey($key)
    {
        return $this->hasKey($this->attributes, $key);
    }
}
