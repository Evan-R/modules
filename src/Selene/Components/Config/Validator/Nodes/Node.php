<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\MissingValueException;

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
     * key
     *
     * @var string
     */
    protected $key;

    /**
     * default
     *
     * @var mixed
     */
    protected $default;

    /**
     * required
     *
     * @var boolean
     */
    protected $required;

    /**
     * allowEmpty
     *
     * @var mixed
     */
    protected $allowEmpty;

    /**
     * builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * parent
     *
     * @var NodeInterface
     */
    protected $parent;

    /**
     * @param NodeInterface $parent
     *
     * @access public
     */
    public function __construct(NodeInterface $parent = null)
    {
        $this->children = [];
        $this->required = true;
        $this->allowEmpty = true;

        if (null !== $parent) {
            $this->setParent($parent);
        }
    }

    /**
     * Mark this node as optional.
     *
     * @access public
     * @return mixed
     */
    public function optional()
    {
        $this->required = false;
        return $this;
    }

    /**
     * Check if this node is optional.
     *
     *
     * @access public
     * @return boolean
     */
    public function isOptional()
    {
        return false === $this->required;
    }

    /**
     * Get the default value if any.
     *
     * @access public
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Sets the default value of this node.
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface this instance
     */
    public function defaultValue($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * setBuilder
     *
     * @param mixed $builder
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface this instance
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * Get the builder instance.
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Builder
     */
    public function getBuilder()
    {
        if ($this->hasParent()) {
            return $this->getParent()->getBuilder();
        }

        return $this->builder;
    }

    /**
     * Set the key of the node.
     *
     * @param string|int $name
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface this instance
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get the key of the node.
     *
     * @access public
     * @return string|int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the parent node.
     *
     * @param NodeInterface $node
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface this instance
     */
    public function setParent(NodeInterface $node)
    {
        $this->parent =& $node;
        $node->addChild($this);

        return $this;
    }

    /**
     * Get the parent node.
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface the parent node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Check if this node has a parent node.
     *
     * @access public
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }

    /**
     * Do not allow empty values.
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Nodes\NodeInterface the parent node
     */
    public function notEmpty()
    {
        $this->allowEmpty = false;
        return $this;
    }

    /**
     * Validates the type of the node.
     *
     * @param mixed $value
     *
     * @access public
     * @abstract
     * @return boolean
     */
    abstract public function validateType($value);

    /**
     * Validates a value against the nodes definition.
     *
     * @param mixed $value
     *
     * @throws MissingValueException
     * @throws InvalidTypeException
     * @access public
     * @return boolean
     */
    public function validate($value = null)
    {
        $empty = $this->isEmptyValue($value);

        if ($this->isOptional()) {
            if ($empty && null === $this->getDefault()) {
                throw new MissingValueException(
                    sprintf('optional key %s with empty value must have a default value', $this->getKey())
                );
            }

        } else {

            if (null === $value) {
                throw new MissingValueException(
                    sprintf('%s is required but missing', $this->getKey())
                );
            }

            if ($empty) {
                throw new MissingValueException(
                    sprintf('%s may not be empty', $this->getKey())
                );
            }
        }

        if (!$this->validateType($value)) {
            throw new InvalidTypeException($this->getInvalidTypeMessage($value));
        }

        return true;
    }

    /**
     * mergeValue
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public function mergeValue($value)
    {
        return $value;
    }

    /**
     * isEmptyValue
     *
     * @param mixed $value
     *
     * @access protected
     * @return boolean
     */
    protected function isEmptyValue($value = null)
    {
        return is_string($value) ? (!strlen(trim($value)) > 0) : empty($value);
    }

    /**
     * __call
     *
     * @param mixed $method
     * @param mixed $arguments
     *
     * @access public
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (($builder = $this->getBuilder()) && method_exists($builder, $method)) {
            return call_user_func_array([$builder, $method], $arguments);
        }

        throw new \BadMethodCallException(
            sprintf('call to undefined method %s::%s', get_class($this), $method)
        );
    }

    /**
     * getInvalidTypeMessage
     *
     * @param mixed $value
     *
     * @access protected
     * @return string
     */
    protected function getInvalidTypeMessage($value = null)
    {
        return sprintf('invalid value for %s', $this->getKey());
    }

    /**
     * callOnBuilder
     *
     * @param mixed $builder
     * @param mixed $method
     * @param mixed $arguments
     *
     * @access private
     * @return mixed
     */
    private function callOnBuilder($builder, $method, $arguments)
    {
        return call_user_func_array([$builder, $method], $arguments);
    }
}
