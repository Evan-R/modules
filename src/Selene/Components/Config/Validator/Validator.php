<?php

/**
 * This File is part of the Selene\Components\Config\Validator package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validator;

use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Nodes\ListNode;
use \Selene\Components\Config\Validator\Nodes\ArrayNode;
use \Selene\Components\Config\Validator\Nodes\ScalarNode;
use \Selene\Components\Config\Validator\Nodes\RootNodeInterface;

/**
 * @class Tree
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Validator implements TreeValidatorInterface
{
    use Getter;

    /**
     * root
     *
     * @var array
     */
    private $root;

    /**
     * config
     *
     * @var array
     */
    private $config;

    /**
     * @param DictNode $root
     *
     * @access public
     * @return mixed
     */
    public function __construct(DictNode $root, array $config = [])
    {
        $this->root = $root;
        $this->config = $config;
    }

    /**
     * load
     *
     * @param array $config
     *
     * @access public
     * @return Validator
     */
    public function load(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Get the root node of the tree.
     *
     * @access public
     * @return DictNode
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * validate
     *
     * @access public
     * @return array
     */
    public function validate()
    {
        return $this->doValidate($this->config, $this->root);
    }

    /**
     * validate
     *
     * @param array $values
     * @param ArrayNode $nodes
     * @param mixed $results
     *
     * @access public
     * @return array
     */
    protected function doValidate(array $values, ArrayNode $nodes = null, &$results = [])
    {
        foreach ($nodes as $key => $node) {

            $val = $this->getDefault($values, $key, $node->isOptional() ? $node->getDefault() : null);
            $results[$key] = [];

            $node->validate($val);

            if ($node instanceof ArrayNode) {
                $this->doValidate($val, $node, $results[$key]);

                if ($node instanceof ListNode) {
                    $results[$key] = $node->mergeValue($results[$key]);
                }

                continue;
            }

            $results[$key] = $node->mergeValue($val);
        }

        return $results;
    }
}
