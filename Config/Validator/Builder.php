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

use \Selene\Components\Config\Validator\Nodes\RootNode;
use \Selene\Components\Config\Validator\Nodes\ScalarNode;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Nodes\ListNode;
use \Selene\Components\Config\Validator\Nodes\FloatNode;
use \Selene\Components\Config\Validator\Nodes\IntegerNode;
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
class Builder implements BuilderInterface
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

    protected $macros;

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
        $this->macros = [];
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
     * Set the root name.
     *
     * @param string $name
     *
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
        $node = new IntegerNode;

        return $this->addNode($node, $key);
    }

    /**
     * macro
     *
     * @param mixed $name
     * @param \Closure $macro
     *
     * @return Builder
     */
    public function macro($name, \Closure $macro)
    {
        $builder = new static($name);
        call_user_func($macro, $builder);

        $this->macros[$name] = $builder;

        return $this;
    }

    /**
     * append
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function append(NodeInterface $node)
    {
        if ($node instanceof RootNode) {
            $node = $this->mergeFromRootNode($node);
        }

        return $this->addNode($node, $node->getKey());
    }

    /**
     * mergeFromRootNode
     *
     * @param RootNode $node
     *
     * @return DictNode
     */
    protected function mergeFromRootNode(RootNode $node)
    {
        $newNode = new DictNode;
        $newNode->setKey($node->getKey());

        foreach ($node->getChildren() as $child) {
            $newNode->addChild($child);
        }

        return $newNode;
    }

    /**
     * useMacro
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException if used on a scalar node.
     * @return ArrayNode
     */
    public function useMacro($name)
    {
        if ($this->current instanceof ScalarNode) {
            throw new \InvalidArgumentException('Can’\t use a macro on a scalar node');
        }

        $node = $this->macros[$name]->getRoot();

        foreach ($node->getChildren() as $childNode) {
            $this->current->addChild(clone($childNode));
        }

        return $this->current;
    }

    /**
     * Adds a FloatNode
     *
     * @param mixed $key
     *
     * @return FloatNode
     */
    public function float($key = null)
    {
        return $this->addNode(new FloatNode, $key);
    }

    /**
     * Adds a DictNode
     *
     * @param mixed $key
     *
     * @return DictNode
     */
    public function dict($key = null)
    {
        return $this->addNode(new DictNode, $key);
    }

    /**
     * Adds a ListNode
     *
     * @param mixed $key
     *
     * @return ListNode
     */
    public function values($key = null)
    {
        return $this->addNode(new ListNode, $key);
    }

    /**
     * Adds a StringNode
     *
     * @param mixed $key
     *
     * @return StringNode
     */
    public function string($key = null)
    {
        return $this->addNode(new StringNode, $key);
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
                sprintf('%s::end(): Node %s is already root.', get_class($this), (string)$this->current->getKey())
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
            throw new \InvalidArgumentException('key can\'t be null');
        }

        $key && $node->setKey($key);

        $this->current->addChild($node);
        $this->current = $node;

        return $node;
    }
}
