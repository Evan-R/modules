<?php

/**
 * This File is part of the Selene\Components\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

use \Selene\Components\Common\Helper\StringHelper;
use \Selene\Components\Filesystem\Traits\PathHelperTrait;
use \Selene\Components\Common\SeparatorParserInterface;

/**
 * @class Locator Locator
 *
 * @package Selene\Components\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Locator implements LocatorInterface
{
    use PathHelperTrait;

    private $locations;

    private $pathAliases;

    /**
     * aliasCache
     *
     * @var mixed
     */
    private $aliasCache;

    /**
     * parser
     *
     * @var PathParser
     */
    private $parser;

    /**
     * @param mixed $root
     * @param mixed $locations
     * @param SeparatorParserInterface $parser
     *
     * @access public
     */
    public function __construct($root = null, $locations = [], SeparatorParserInterface $parser = null)
    {
        $this->locations = $locations;
        $this->parser = $parser ?: new PathParser;

        $this->pathAliases = [];
        $this->aliasCache = [];

        $this->setRoot($root);
    }

    /**
     * setRoot
     *
     * @param mixed $root
     *
     * @access public
     * @return void
     */
    public function setRoot($root = null)
    {
        $this->root = rtrim($root, '\\/') ?: getcwd();
    }

    /**
     * locate
     *
     * @param mixed $template
     *
     * @access public
     * @return string
     */
    public function locate($template, $optimistic = false)
    {
        foreach ($this->locations as $location) {

            $file = $this->getLocationPath($this->getPathFromAlias($location, $template));

            if (is_file($file)) {
                return $file;
            }
        }

        return $optimistic ? $tempalte : false;
    }

    /**
     * addPathAlias
     *
     * @param mixed $aliase
     * @param mixed $path
     *
     * @access public
     * @return void
     */
    public function addPathAlias($alias, $path)
    {
        $this->pathAliases[$alias] = $path;
    }

    /**
     * addPathAliases
     *
     * @param array $aliases
     *
     * @access public
     * @return void
     */
    public function addPathAliases(array $aliases)
    {
        foreach ($aliases as $alias => $path) {
            $this->addPathAlias($alias, $path);
        }
    }

    /**
     * addLocation
     *
     * @param mixed $path
     *
     * @access public
     * @return void
     */
    public function addLocation($path)
    {
        if (in_array($path, $this->locations)) {
            return;
        }
        $this->locations[] = $path;
    }

    /**
     * getLocationPath
     *
     * @param mixed $path
     *
     * @access protected
     * @return string
     */
    protected function getLocationPath($path)
    {
        return $this->isRelativePath($path) ? $this->root . DIRECTORY_SEPARATOR . $path : $path;
    }

    /**
     * getPathFromAlias
     *
     * @param mixed $path
     *
     * @access protected
     * @return string
     */
    protected function getPathFromAlias($path, $file)
    {
        if (isset($this->aliasCache[$file])) {
            return $this->aliasCache[$file];
        }

        if ($this->parser->supports($file)) {

            list ($package, $template) = $this->parser->parse($file);

            if (isset($this->pathAliases[$package])) {
                return $this->aliasCache[$file] = $this->getPathAlias($package, $template);
            }
        }

        //return $this->aliasCache[$path] = $file;
        return $this->aliasCache[$path] = $path . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * getPathAlias
     *
     * @param mixed $package
     * @param mixed $subpath
     * @param mixed $template
     *
     * @access protected
     * @return string
     */
    protected function getPathAlias($package, $template)
    {
        //$subpath = 0 < strlen($subpath) ? DIRECTORY_SEPARATOR . $subpath : $subpath;

        //return $this->pathAliases[$package] . DIRECTORY_SEPARATOR . 'Resources' . $subpath . DIRECTORY_SEPARATOR .
            //'view' . DIRECTORY_SEPARATOR . $template;
    }
}
