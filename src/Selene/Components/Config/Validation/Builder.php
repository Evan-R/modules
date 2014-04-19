<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation;

use \Selene\Components\Common\Traits\Setter;
use \Selene\Components\Config\Validation\Nodes\Node;
use \Selene\Components\Config\Validation\Nodes\RootNode;
use \Selene\Components\Config\Validation\Nodes\ArrayNode;
use \Selene\Components\Config\Validation\Nodes\ScalarNode;

/**
 * @class Builder
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Builder
{
    use Setter;

    const NODE_TYPE_ROOT = 100;

    const NODE_TYPE_SCALAR = 101;

    const NODE_TYPE_ARRAY = 102;

    /**
     * separator
     *
     * @var string
     */
    protected static $separator = '=>';

    /**
     * nodes
     *
     * @var array
     */
    protected $nodes;

    /**
     * root
     *
     * @var \Selene\Components\Config\Validation\Nodes\RootNode
     */
    protected $root;

    /**
     * parentNodes
     *
     * @var \SplStack
     */
    protected $parentNodes;

    /**
     * current
     *
     * @var \Selene\Components\Config\Validation\Nodes\ScalarNode
     */
    protected $current;

    /**
     * currentKey
     *
     * @var string
     */
    protected $currentKey;

    /**
     * Creates a new Builder object.
     *
     * @access public
     */
    public function __construct()
    {
        $this->nodes = [];
        $this->parentNodes = new \SplStack;
    }

    /**
     * Get all node.
     *
     * @access public
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Creates a rootnode.
     *
     * @param string $name
     *
     * @access public
     * @return \Selene\Components\Config\Validation\Nodes\RootNode
     */
    public function root($name = 'root')
    {
        $this->nodes = [];
        $root = $this->setNode(static::NODE_TYPE_ROOT, $name);
        $this->root = $root;
        return $root;
    }

    /**
     * Get the rootnode.
     *
     * If the rootnode does not exist, a rootnode will be created with the name
     * of 'root'.
     *
     * @access public
     * @return \Selene\Components\Config\Validation\Nodes\RootNode
     */
    public function getRoot()
    {
        if (null === $this->root) {
            $this->root('root');
        }

        return $this->root;
    }

    /**
     * addNode
     *
     * @param mixed $name
     * @param mixed $type
     *
     * @access public
     * @return NodeInterface
     */
    public function addNode($name, $type)
    {
        return $this->setNode($type, $name);
    }

    /**
     * Add a ScalarNode to the current rootnode.
     *
     * @param string $name
     *
     * @access public
     * @return \Selene\Components\Config\Validation\Nodes\Node
     */
    public function scalarNode($name)
    {
        return $this->setNode(static::NODE_TYPE_SCALAR, $name);
    }

    /**
     * Add an ArrayNode to the current rootnode.
     *
     * @param string $name
     *
     * @access public
     * @return \Selene\Components\Config\Validation\Nodes\Node
     */
    public function arrayNode($name)
    {
        return $this->setNode(static::NODE_TYPE_ARRAY, $name);
    }

    /**
     * Close the current node.
     *
     * @access public
     * @return mixed returns the current rootnode or the builder.
     */
    public function end()
    {
        if ($this->hasKey()) {
            if (false !== ($pos = strrpos($this->currentKey, static::$separator))) {
                $this->currentKey = substr($this->currentKey, 0, $pos);
            } else {
                $this->currentKey = null;
            }
        }

        $current = $this->current;
        $this->current = null;

        if (0 < ($count = $this->parentNodes->count())) {

            if (!$current) {
                $this->parentNodes->pop();
            }

            if ($count > 1) {
                return $this->parentNodes->top();
            }
        }

        return $this;
    }

    /**
     * hasKey
     *
     * @access protected
     * @return boolean
     */
    protected function hasKey()
    {
        return null !== $this->currentKey && 0 < strlen($this->currentKey);
    }

    /**
     * getKeyName
     *
     * @param mixed $name
     *
     * @access protected
     * @return string
     */
    protected function getKeyName($name)
    {
        if (null !== $this->currentKey && strlen($this->currentKey) > 0) {
            return $this->currentKey . static::$separator . $name;
        }

        return $name;
    }

    /**
     * setNode
     *
     * @param int $type
     * @param string $name
     *
     * @access protected
     * @return \Selene\Components\Config\Validation\Nodes\Node
     */
    protected function setNode($type, $name)
    {
        $key = $this->getKeyName($name);

        $this->currentKey = $key;

        $node = $this->createNode($type, $key);

        if ($node instanceof ArrayNode) {
            $this->current = null;
            $this->parentNodes->push($node);
        } else {
            $this->current = $node;
        }

        return $node;
    }

    /**
     * createNode
     *
     * @param mixed $type
     * @param mixed $key
     *
     * @access protected
     * @return \Selene\Components\Config\Validation\Nodes\Node
     */
    protected function createNode($type, $key)
    {
        switch ($type) {
            case static::NODE_TYPE_ROOT:
                return new RootNode($this, $key);
            case static::NODE_TYPE_SCALAR:
                return new ScalarNode($this, $key);
            case static::NODE_TYPE_ARRAY:
                return new ArrayNode($this, $key);
            default:
                throw new \Exception('invalid node');

        }
    }

    /**
     * getSeparator
     *
     * @access public
     * @return string
     */
    public static function getSeparator()
    {
        return static::$separator;
    }

    /**
     * setSeparator
     *
     * @param mixed $separator
     *
     * @access public
     * @return void
     */
    public static function setSeparator($separator)
    {
        static::$separator = $separator;
    }
}
