<?php

/**
 * This File is part of the Selene\Module\Config\Validator package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator;

use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Config\Validator\Nodes\DictNode;
use \Selene\Module\Config\Validator\Nodes\ListNode;
use \Selene\Module\Config\Validator\Nodes\ArrayNode;
use \Selene\Module\Config\Validator\Nodes\ScalarNode;
use \Selene\Module\Config\Validator\Nodes\BooleanNode;
use \Selene\Module\Config\Validator\Nodes\RootNodeInterface;

/**
 * @class Tree
 *
 * @package Selene\Module\Config
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
        $this->root->finalize($this->config);
        $this->root->validate();

        return $this->root->getValue();
    }
}
