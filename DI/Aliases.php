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
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Aliases implements \ArrayAccess, \IteratorAggregate
{
    use Getter;

    /**
     * aliases
     *
     * @var array
     */
    private $aliases;

    /**
     * Constructor.
     *
     * @param array $aliases
     */
    public function __construct(array $aliases = [])
    {
        $this->aliases = [];

        foreach ($aliases as $alias => $id) {
            $this->set($alias, $id);
        }
    }

    /**
     * set
     *
     * @param string $alias
     * @param string $service
     *
     * @api
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
     * @return array
     */
    public function all()
    {
        return $this->aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($alias, $id)
    {
        return $this->set($alias, $id);
    }

    /**
     * {@inheritdoc}
     *
     * @return Alias
     */
    public function offsetGet($alias)
    {
        return $this->get($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($alias)
    {
        return $this->has($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($alias)
    {
        unset($this->aliases[$alias]);
    }

    /**
     * getIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
