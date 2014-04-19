<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

use Selene\Components\Foundation\BundleInterface;
use Selene\Components\Common\Traits\SegmentParser;

/**
 * @class Configuration
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class Configuration extends SequenceParser implements ConfigurationInterface
{
    /**
     * loader
     *
     * @var array
     */
    private $loaders;

    /**
     * bunles
     *
     * @var array
     */
    private $bundles = [];

    /**
     * configuration
     *
     * @var array
     */
    private $configuration = [];

    /**
     * environment
     *
     * @var string
     */
    protected $environment;

    /**
     * __construct
     *
     * @param FileLoaderInterface $loader
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $loaders = null, $env = 'production')
    {
        $this->loaders     = $loaders;
        $this->environment = $env;
    }

    /**
     * get
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $default
     *
     * @access public
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $items) = $this->parseSequence($key);
        $this->load($namespace, $group, $prefix = $this->getGroupPrefix($group, $namespace));
        return $this->returnDefault(arrayGet($this->configuration[$prefix], $items), $default);
    }

    /**
     * set
     *
     * @param mixed $attribute
     * @param mixed $value
     * @param mixed $default
     *
     * @access public
     * @return mixed
     */
    public function set($attribute, $value)
    {
        list($namespace, $group, $segments) = $this->parseSegment($attribute);

        $prefix = $this->getGroupPrefix($namespace, $group);

        if (!isset($this->configuration[$prefix])) {
            $this->configuration[$prefix] = [];
        }
        return arraySet($segments, $value, $this->configuration[$prefix]);
    }

    /**
     * configureBundlePath
     *
     * @access public
     * @return mixed
     */
    public function configureBundlePath()
    {

    }

    /**
     * parseNamespace
     *
     * @param mixed $descriptor
     *
     * @access protected
     * @return mixed
     */
    protected function parseNamespace($descriptor)
    {
        list($namespace, $sequence) = $this->splitNamespace($descriptor);

        if (in_array($namespace, $this->bundles)) {
            return $this->parseBundleSegment($namespace, $sequence);
        }

        return $this->parseNamespaceParts($namespace, $sequence);
    }

    /**
     * parseBundleSegment
     *
     * @param mixed $namespace
     * @param mixed $sequence
     *
     * @access protected
     * @return mixed
     */
    protected function parseBundleSegment($namespace, $sequence)
    {

    }

    /**
     * addBundle
     *
     * @access public
     * @return mixed
     */
    public function addBundle(BundleInterface $bundle)
    {
        $this->getBundleConfig($bundleNamespace);
    }

    /**
     * getBundlePath
     *
     * @access protected
     * @return mixed
     */
    protected function getBundlePath()
    {

    }

    /**
     * getBundleConfig
     *
     * @param mixed $namespace
     *
     * @access protected
     * @return mixed
     */
    protected function getBundleConfig($namespace)
    {

    }

    /**
     * getGroupPrefix
     *
     * @param mixed $namspace
     * @param mixed $group
     *
     * @access protected
     * @return string
     */
    protected function getGroupPrefix($group, $namespace = null)
    {
        return sprintf('%s%s%s', $namespace ?: '*', $this->getNsSeparator(), $group);
    }

    /**
     * load
     *
     * @param mixed $namespace
     * @param mixed $group
     * @param mixed $prefix
     *
     * @access protected
     * @return mixed
     */
    protected function load($namespace, $group, $prefix)
    {
        if (isset($this->configuration[$prefix])) {
            return $this->configuration[$prefix];
        }
    }

    /**
     * getLoader
     *
     * @access protected
     * @return FileLoaderInterface
     */
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * returnDefault
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @access protected
     * @return mixed
     */
    protected function returnDefault($value = null, $default = null)
    {
        return is_null($value) ? $default : $value;
    }
}
