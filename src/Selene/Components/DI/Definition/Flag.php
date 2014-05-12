<?php

/**
 * This File is part of the Selene\Components\DI\Definition package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Definition;

use \Selene\Components\Common\Traits\Getter;

/**
 * @class Flag
 * @package Selene\Components\DI\Definition
 * @version $Id$
 */
class Flag implements FlagInterface
{
    use Getter;

    /**
     * arguments
     *
     * @var array
     */
    protected $arguments;

    /**
     * @param mixed $name
     * @param array $arguments
     *
     * @access public
     */
    public function __construct($name, array $arguments = [])
    {
        if (is_array($name)) {
            $arguments = array_merge($arguments, $name);
        } else {
            $arguments['name'] = $name;
        }

        if (!isset($arguments['name'])) {
            throw new \InvalidArgumentException('no name given');
        }

        $this->arguments = $arguments;
    }

    /**
     * get
     *
     * @param mixed $argument
     * @param mixed $default
     *
     * @access public
     * @return mixed
     */
    public function get($argument, $default = null)
    {
        return $this->getDefault($this->arguments, $argument, $default);
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->getDefault($this->arguments, 'name');
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
