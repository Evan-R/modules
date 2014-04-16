<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View;

use \SplFileInfo;
use \DirectoryIterator;
use \Selene\Components\Common\Traits\Getter;

/**
 * @class TemplateResolver implements TemplateResolverInterface
 * @see TemplateResolverInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class TemplateResolver implements TemplateResolverInterface
{
    use Getter;

    const PATH_SEPARATOR = '.';

    const PACKAGE_SEPARATOR = ':';

    /**
     * root
     *
     * @var string
     */
    private $root;

    /**
     * packages
     *
     * @var mixed
     */
    private $packages;

    /**
     * @param mixed $root
     * @param array $packages
     *
     * @access public
     */
    public function __construct($root = null, array $packages = [])
    {
        $this->root = $root;
        $this->packages = $packages;
    }

    /**
     * resolve
     *
     * @param mixed $template
     *
     * @access public
     * @return array of strings each representing the full file path.
     */
    public function resolve($template)
    {
        $files = [];
        $path = $this->getTemplatePath($template);

        $name = basename($path);
        $dir  = dirname($path);

        $iter = new DirectoryIterator($dir);

        foreach ($iter as $fileInfo) {
            if ($fileInfo->isFile() && $this->matchesTemplateName($fileInfo, $name)) {
                $files[] = $fileInfo->getFileInfo();
            }
        }

        if (empty($files)) {
            throw new \InvalidArgumentException(sprintf('template %s could not be found', $template));
        }

        return $files;
    }

    /**
     * setPackagePaths
     *
     * @param array $paths associative array.
     *
     * @access public
     * @return void
     */
    public function setPackagePaths(array $paths)
    {
        $this->packages = $paths;
    }

    /**
     * getPackagePaths
     *
     * @access public
     * @return array
     */
    public function getPackagePaths()
    {
        return $this->packages;
    }

    /**
     * getPackagePath
     *
     * @param mixed $package
     *
     * @access public
     * @return string
     */
    public function getPackagePath($package = null)
    {
        if (null === $package) {
            return;
        }

        if ($path = $this->getDefault($this->packages, $package, false)) {
            return $path . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'views';
        }
    }

    /**
     * getTemplatePath
     *
     * @param mixed $template
     *
     * @access protected
     * @return string
     */
    protected function getTemplatePath($template)
    {
        $root = $this->getPackagePath($pn = $this->getPackageName($template)) ?: $this->root;

        $path = $root .
            DIRECTORY_SEPARATOR .
            strtr($this->stripTemplateName($template, $pn), [static::PATH_SEPARATOR => DIRECTORY_SEPARATOR]);

        if (!is_dir($dir = dirname($path))) {
            throw new \InvalidArgumentException(sprintf('template path %s does not exist', $dir));
        }

        return $path;
    }

    /**
     * stripTemplateName
     *
     * @param mixed $template
     * @param mixed $packageName
     *
     * @access protected
     * @return string
     */
    protected function stripTemplateName($template, $packageName = null)
    {
        if (null === $packageName) {
            return $template;
        }

        return substr($template, strlen($packageName) + strlen(static::PACKAGE_SEPARATOR));
    }

    /**
     * matchesTemplateName
     *
     * @param SplFileinfo $fileInfo
     * @param mixed $name
     *
     * @access protected
     * @return boolean
     */
    protected function matchesTemplateName(SplFileInfo $fileInfo, $name)
    {
        $baseName = substr($fileInfo->getFileName(), 0, -1 - strlen($fileInfo->getExtension()));
        return 0 === strcmp($name, $baseName);
    }

    /**
     * getPackageName
     *
     * @param mixed $template
     *
     * @access protected
     * @return string
     */
    protected function getPackageName($template)
    {
        if (false !== ($pos = strpos($template, static::PACKAGE_SEPARATOR))) {
            return substr($template, 0, $pos);
        }
    }

    /**
     * getRootPath
     *
     * @access protected
     * @return string
     */
    protected function getRootPath()
    {
        return $this->root;
    }
}
