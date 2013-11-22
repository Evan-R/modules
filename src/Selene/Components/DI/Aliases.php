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
class Aliases
{
    use Getter;
    /**
     * name
     *
     * @var string
     */
    private $aliases;

    /**
     * __construct
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $aliases = [])
    {
        $this->setAliases($aliases);
    }

    /**
     * add
     *
     * @param mixed $alias
     * @param mixed $service
     *
     * @access public
     * @return void
     */
    public function add($alias, $service)
    {
        $this->aliases[$alias] = $service;
    }

    /**
     * get
     *
     * @param mixed $alias
     * @param mixed $default
     *
     * @access public
     * @return string
     */
    public function get($alias)
    {
        return $this->getDefault($this->aliases, $alias, $alias);
    }

    /**
     * setAliases
     *
     * @param array $aliases
     *
     * @access private
     * @return void
     */
    private function setAliases(array $aliases = [])
    {
        $this->aliases = $aliases;
    }
}
