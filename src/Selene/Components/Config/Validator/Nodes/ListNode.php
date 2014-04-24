<?php

/**
 * This File is part of the Selene\Components\Config\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator\Nodes;

use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class Collection
 * @package Selene\Components\Config\Validator\Nodes
 * @version $Id$
 */
class ListNode extends ArrayNode implements \IteratorAggregate
{
    protected $validatorValue;

    protected $validatorValues;

    protected $filterError;

    protected $filteredValues;

    /**
     * @param NodeInterface $parent
     *
     * @access public
     * @return mixed
     */
    public function __construct(NodeInterface $parent = null)
    {
        $this->default = [];
        $this->filteredValues = [];
        parent::__construct($parent);
    }

    public function validateType($value)
    {
        return is_array($value) && ctype_digit(implode('', array_keys($value)));
    }

    public function filterError($message)
    {
        $this->filterError = $message;
        return $this;
    }

    /**
     * addChild
     *
     * @param NodeInterface $node
     *
     * @access public
     * @return NodeInterface this instance
     */
    public function addChild(NodeInterface $node)
    {
        $node->setKey(count($this->getChildren()));
        return parent::addChild($node);
    }

    /**
     * item
     *
     * @access public
     * @return mixed
     */
    public function item()
    {
        return $this;
    }

    /**
     * contains
     *
     * @param callable $validate
     *
     * @access public
     * @return NodeInterface
     */
    //public function filterItems(callable $validate)
    //{
        //$this->validatorValue = $validate;
        //return $this;
    //}

    /**
     * filter
     *
     * @param callable $validate
     *
     * @access public
     * @return ListNode
     */
    public function filter(callable $validate)
    {
        $this->validatorValue = $validate;
        return $this;
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
        $valid = parent::validate($value);

        if ($this->hasChildren()) {
            $this->checkExceedingKeys((array)$value);
            $count = count($this->children);

            $value = (array)$value;
            array_splice($value, 0, $count);
        }

        $this->applyFilters((array)$value);

        return $valid;
    }

    /**
     * mergeValue
     *
     * @param mixed $value
     *
     * @access public
     * @return array
     */
    public function mergeValue($value)
    {
        return array_merge((array)$value, $this->filteredValues);
    }

    /**
     * applyFilters
     *
     * @param array $values
     *
     * @access protected
     * @return mixed
     */
    protected function applyFilters(array $values)
    {
        if (!is_callable($this->validatorValue)) {
            return;
        }

        foreach ($values as $key => $val) {
            if (true !== call_user_func_array($this->validatorValue, [$val, $key, $this])) {
                $msg = $this->filterError ?: sprintf('invalid list value for key %s', $this->getKey());
                throw new ValidationException($msg);
            }

            $this->filteredValues[$key] = $val;
        }
    }

    /**
     * checkExceedingKeys
     *
     * @param array $values
     *
     * @access protected
     * @return void
     */
    protected function checkExceedingKeys(array $values)
    {
        if (is_callable($this->validatorValue)) {
            return;
        }

        foreach ($values as $key => $val) {
            if (isset($this->children[$key])) {
                continue;
            }
            throw new ValidationException(
                sprintf('invalid key %s in %s', $key, $this->getKey())
            );
        }
    }

    /**
     * getIterator
     *
     *
     * @access public
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getChildren());
    }
}
