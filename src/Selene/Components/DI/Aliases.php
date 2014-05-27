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

use \Selene\Components\Common\Traits\Getter;

/**
 * @class Alias
 * @package Selene\Components\DI
 * @version $Id$
 */
class Aliases implements \ArrayAccess
{
    use Getter;

    /**
     * aliases
     *
     * @var array
     */
    private $aliases;

    /**
     * @param array $aliases
     *
     * @access public
     */
    public function __construct(array $aliases = [])
    {
        $this->aliases = [];

        foreach ($aliases as $id => $alias) {
            $this->set($id, $alias);
        }
    }

    /**
     * set
     *
     * @param string $alias
     * @param string $service
     *
     * @api
     * @access public
     * @return void
     */
    public function set($alias, $id)
    {
        $this->aliases[strtolower($alias)] = $id instanceof Alias ? $id : new Alias(strtolower($id));
    }

    /**
     * Get an alias set on this collection.
     * If the alias is not set, the inpput string will be returened instead.
     *
     * If you need to explicitly check if an alias exists, `use Aliases::has()` or
     * `isset($aliases[$alias])` respectively.
     *
     * @param string $alias
     *
     * @api
     * @access public
     * @return string|Alias if the alias exists, an alias object will be
     * returned, otherwise the input string gets passed through.
     */
    public function get($alias)
    {
        $alias = strtolower($alias);
        return $this->getDefault($this->aliases, $alias, $alias);
    }

    /**
     * Check it an Alias exists.
     *
     * @param string $alias
     *
     * @api
     * @access public
     * @return bool
     */
    public function has($alias)
    {
        return isset($this->aliases[strtolower($alias)]);
    }

    /**
     * Get all aliases.
     *
     * @api
     * @access public
     * @return array
     */
    public function all()
    {
        return $this->aliases;
    }

    /**
     * offsetSet
     *
     * @param string $alias
     * @param string $id
     *
     * @access public
     * @return void
     */
    public function offsetSet($alias, $id)
    {
        return $this->set($alias, $id);
    }

    /**
     * offsetGet
     *
     * @param string $alias
     *
     * @access public
     * @return Alias
     */
    public function offsetGet($alias)
    {
        return $this->get($alias);
    }

    /**
     * offsetExists
     *
     * @param mixed $alias
     *
     * @access public
     * @return bool
     */
    public function offsetExists($alias)
    {
        return $this->has($alias);
    }

    /**
     * offsetUnset
     *
     * @param mixed $alias
     *
     * @access public
     * @return mixed
     */
    public function offsetUnset($alias)
    {
        unset($this->aliases[$alias]);
    }
}
