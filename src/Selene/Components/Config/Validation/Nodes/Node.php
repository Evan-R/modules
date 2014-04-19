<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

use \Selene\Components\Config\Validation\Builder;

/**
 * @abstract class Node implements NodeInterface
 * @see NodeInterface
 * @abstract
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Node implements NodeInterface
{
    /**
     * \Selene\Components\Config\Validation\Builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * required
     *
     * @var boolean
     */
    protected $required;

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * end
     *
     * @var boolean
     */
    protected $end;

    /**
     * @param Builder $builder
     *
     * @access public
     */
    public function __construct(Builder $builder, $name)
    {
        $this->end = false;
        $this->name = $name;
        $this->builder = $builder;
    }

    /**
     * __toString
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        $keys = explode(Builder::getSeparator(), $this->name);
        $name = strWrapStr(implode('][', $keys), '[', ']');
        return $name;
    }

    /**
     * required
     *
     * @access public
     * @return Node
     */
    public function required()
    {
        $this->required = true;
        return $this;
    }

    /**
     * optional
     *
     * @access public
     * @return Node
     */
    public function optional()
    {
        $this->required = false;
        return $this;
    }

    /**
     * isOptional
     *
     * @access public
     * @return mixed
     */
    public function isOptional()
    {
        return !$this->required;
    }

    /**
     * isRequired
     *
     * @access public
     * @return mixed
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * end
     *
     * @access public
     * @return mixed
     */
    public function end()
    {
        if ($this->end) {
            throw new \Exception(
                sprintf('node %s already closed', (string)$this)
            );
            return $this;
        }

        $this->end = true;
        return $this->builder->end();
    }

    /**
     * validate
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function validate($value = null)
    {
        if (null === $value && $this->required) {
            throw new \Exception();
        }
    }

    /**
     * isClosed
     *
     * @access protected
     * @return boolean
     */
    protected function isClosed()
    {
        return $this->end;
    }
}
