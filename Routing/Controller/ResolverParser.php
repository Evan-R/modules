<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\Common\SeparatorParser;
use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\Common\Helper\StringHelper;

/**
 * @class ResolverParser extends SeparatorParser
 * @see SeparatorParser
 *
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ResolverParser extends SeparatorParser
{
    use Getter;

    protected $aliases;

    /**
     * @param array $aliases
     *
     * @access public
     */
    public function __construct(array $aliases = [])
    {
        $this->aliases = $aliases;
    }

    /**
     * addNamespaceAlies
     *
     * @param mixed $alias
     * @param mixed $namespace
     *
     * @access public
     * @return mixed
     */
    public function setNamespaceAlias($alias, $namespace)
    {
        $this->aliases[$alias] = $namespace;
    }

    /**
     * setNamespaceAliases
     *
     * @param array $aliass
     *
     * @access public
     * @return void
     */
    public function setNamespaceAliases(array $aliases)
    {
        foreach ($aliases as $key => $namespace) {
            $this->setNamespaceAlias($key, $namespace);
        }
    }

    /**
     * parse
     *
     * @param mixed $string
     *
     * @access public
     * @return array
     */
    public function parse($string)
    {
        list($first, $mid, $last) = explode(static::$separator, $string);

        return [
            $this->getControllerClass($first, $mid),
            $this->getControllerAction($last)
        ];
    }

    /**
     * supports
     *
     * @param mixed $string
     *
     * @access public
     * @return boolean
     */
    public function supports($string)
    {
        if ($supports = parent::supports($string)) {
            return static::$separator !== $string[1 + strpos($string, static::$separator)];
        }

        return $supports;
    }

    /**
     * getNamespaceFromAlias
     *
     * @param mixed $alias
     *
     * @access protected
     * @return string
     */
    protected function getNamespaceFromAlias($alias)
    {
        return $this->getDefault($this->aliases, $alias, $alias);
    }

    /**
     * getControllerName
     *
     * @param mixed $controller
     *
     * @access protected
     * @return string
     */
    protected function getControllerName($controller)
    {
        $ctrl = 'Controller' . '\\' .$this->getCtrlAndLocalNamespace(ucfirst($controller));
        return StringHelper::striEndsWith($ctrl, 'Controller') ? $ctrl : $ctrl.'Controller';
    }

    /**
     * getControllerAction
     *
     * @param mixed $action
     *
     * @access protected
     * @return string
     */
    protected function getControllerAction($action)
    {
        return lcfirst(StringHelper::striEndsWith($action, 'action') ? $action : $action.'Action');
    }

    /**
     * getCtrlAndLocalNamespace
     *
     * @param mixed $controller
     *
     * @access protected
     * @return string
     */
    protected function getCtrlAndLocalNamespace($controller)
    {
        return StringHelper::strCamelCaseAll($controller, ['.' => '\ ']);
    }

    /**
     * getControllerClass
     *
     * @param mixed $first
     * @param mixed $mid
     *
     * @access protected
     * @return string
     */
    protected function getControllerClass($first, $mid)
    {
        return $this->getNamespaceFromAlias($first) .'\\'. $this->getControllerName($mid);
    }
}
