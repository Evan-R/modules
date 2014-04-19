<?php

/**
 * This File is part of the Selene\Components\Config\Validation package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation;

use \Selene\Components\Config\Validation\Nodes\Node;
use \Selene\Components\Config\Validation\Nodes\ArrayNode;
use \Selene\Components\Config\Validation\Nodes\ScalarNode;

/**
 * @class Tree
 * @package Selene\Components\Config\Validation
 * @version $Id$
 */
class Tree implements \IteratorAggregate
{
    protected $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->builder->getNode());
    }

    public function validate(array $config)
    {
        $this->validateNodes($this->builder->getRoot(), $config);
    }

    protected function validateNodes(ArrayNode $nodes, array $config = [])
    {
        foreach ($nodes as $key => $node) {

            if (!isset($config[$key])) {

                if ($node->isRequired()) {
                    throw new \Exception(sprintf('node %s is required', $node));
                }
            }

            if ($node instanceof ArrayNode) {
                if (!is_array($config[$key])) {
                    throw new \Exception(sprintf('node %s must be array', $node));
                }
                $this->validateNodes($node, $config[$key]);
                continue;
            }

            if ($node instanceof ScalarNode) {

                if (ScalarNode::TYPE_STRING === $node->getType() && !is_string($config[$key])) {
                    throw new \Exception(sprintf('node %s needs to be type of string', $node));
                }

                if (ScalarNode::TYPE_BOOLEAN === $node->getType() && !is_bool($config[$key])) {
                    throw new \Exception(sprintf('node %s needs to be type of boolean', $node));
                }

                if (ScalarNode::TYPE_INTEGER === $node->getType() && !is_int($config[$key])) {
                    throw new \Exception(sprintf('node %s needs to be type of integer', $node));
                }

                if (ScalarNode::TYPE_FLOAT === $node->getType() && !is_float($config[$key])) {
                    throw new \Exception(sprintf('node %s needs to be type of float', $node));
                }
            }
        }
    }
}
