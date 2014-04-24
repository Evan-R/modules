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

use \Selene\Components\Config\Validator\Nodes\EnumNode;
use \Selene\Components\Config\Validator\Nodes\RootNode;
use \Selene\Components\Config\Validator\Nodes\ScalarNode;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Nodes\ListNode;
use \Selene\Components\Config\Validator\Nodes\FloatNode;
use \Selene\Components\Config\Validator\Nodes\IntergerNode;
use \Selene\Components\Config\Validator\Nodes\StringNode;
use \Selene\Components\Config\Validator\Nodes\BooleanNode;
use \Selene\Components\Config\Validator\Nodes\NodeInterface;
use \Selene\Components\Config\Validator\Nodes\ParentableInterface;

/**
 * @class Builder Builder
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Builder
{
    /**
     * root
     *
     * @var \Selene\Components\Config\Validator\Nodes\NodeInterface
     */
    protected $root;

    /**
     * current
     *
     * @var \Selene\Components\Config\Validator\Nodes\NodeInterface
     */
    protected $current;

    /**
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function __construct($name = null)
    {
        $this->setRoot($name);
    }

    /**
     * getValidator
     *
     * @access public
     * @return \Selene\Components\Config\Validator\TreeValidatorInterface
     */
    public function getValidator()
    {
        return new Validator($this->root);
    }

    /**
     * setRoot
     *
     * @param mixed $name
     *
     * @access public
     * @return void
     */
    public function setRoot($name = null)
    {
        $this->root = $this->current = new RootNode;
        $this->root->setKey($name ?: 'root');
        $this->root->setBuilder($this);

        return $this;
    }

    /**
     * getRoot
     *
     * @access public
     * @return NodeInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Initializes the root node.
     *
     * @access public
     * @return \Selene\Components\Config\Validator\Builder
     */
    public function root()
    {
        $this->current = $this->root;
        return $this;
    }

    /**
     * Adds a BooleanNode
     *
     * @param mixed $key
     *
     * @access public
     * @return BooleanNode
     */
    public function boolean($key = null)
    {
        $node = new BooleanNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds a IntegerNode
     *
     * @param mixed $key
     *
     * @access public
     * @return IntegerNode
     */
    public function integer($key = null)
    {
        $node = new IntergerNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds a FloatNode
     *
     * @param mixed $key
     *
     * @access public
     * @return FloatNode
     */
    public function float($key = null)
    {
        $node = new FloatNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds a DictNode
     *
     * @param mixed $key
     *
     * @access public
     * @return DictNode
     */
    public function dict($key = null)
    {
        $node = new DictNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds a ListNode
     *
     * @param mixed $key
     *
     * @access public
     * @return ListNode
     */
    public function values($key = null)
    {
        $node = new ListNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds a StringNode
     *
     * @param mixed $key
     *
     * @access public
     * @return StringNode
     */
    public function string($key = null)
    {
        $node = new StringNode;
        return $this->addNode($node, $key);
    }

    /**
     * Adds an enum node
     *
     * @param mixed $key
     *
     * @access public
     * @return EnumNode
     */
    public function enum($key = null)
    {
        $node = new EnumNode;
        return $this->addNode($node, $key);
    }

    /**
     * Close the current node.
     *
     * @access public
     * @throws \BadMethodCallException if current node is already root
     * @return \Selene\Components\Config\Validator\Builder
     */
    public function end()
    {
        if (!$this->current->hasParent()) {
            throw new \BadMethodCallException(
                sprintf('node %s is already root', (string)$this->current->getKey())
            );
        }

        $this->current = $this->current->getParent();
        return $this;
    }

    /**
     * addNode
     *
     * @param NodeInterface $node
     * @param mixed $key
     *
     * @access protected
     * @return NodeInterface
     */
    protected function addNode(NodeInterface $node, $key = null)
    {
        if (null == $key && !($this->current instanceof ListNode)) {
            throw \InvalidArgumentException('key can\'t be null');
        }

        $key && $node->setKey($key);
        $this->current->addChild($node);
        $this->current = $node;

        return $node;
    }
}
