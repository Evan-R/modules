<?php

/**
 * This File is part of the Selene\Adapters\Twig\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Adapters\Twig\Loaders;

use \Twig_LoaderInterface as LoaderInterface;
use \Selene\Components\View\Template\ResolverInterface;
use \Selene\Components\View\Template\LoaderInterface as TemplateLoaderInterface;

/**
 * @class FileLoader
 * @package Selene\Adapters\Twig\Loaders
 * @version $Id$
 */
class FileLoader implements LoaderInterface, TemplateLoaderInterface
{
    /**
     * paths
     *
     * @var array
     */
    protected $paths;

    /**
     * cache
     *
     * @var array
     */
    protected $cache;

    /**
     * parser
     *
     * @var PathParser
     */
    protected $parser;

    public function __construct(ResolverInterface $resolver)
    {
        $this->cache = [];
        $this->resolver = $resolver;
    }

    /**
     * getSource
     *
     * @param mixed $source
     *
     * @return string
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    public function load($name)
    {
        return $this->findTempalte($name);
    }

    /**
     * findTemplate
     *
     * @param mixed $name
     *
     * @return string
     */
    protected function findTemplate($name)
    {
        return $this->resolver->resolve($name);
    }

    /**
     * getCacheKey
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function getCacheKey($name)
    {
        $key = hash('sha256', $name);

        return $key;
    }

    /**
     * isFresh
     *
     * @param string $name
     * @param int $time
     *
     * @return boolean
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) < $time;
    }

    /**
     * isValid
     *
     * @param string $name
     * @param int $time
     *
     * @return boolean
     */
    public function isValid($name, $time)
    {
        return $this->isFresh($name, $time);
    }
}
