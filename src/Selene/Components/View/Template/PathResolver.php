<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

/**
 * @class PathResolver
 * @package Selene\Components\View
 * @version $Id$
 */
class PathResolver implements ResolverInterface, LocatorInterface
{
    /**
     * cache
     *
     * @var array
     */
    private $cache;

    /**
     * paths
     *
     * @var array
     */
    private $paths;

    /**
     * parser
     *
     * @var PathParser
     */
    private $parser;

    /**
     * Create a new PathResolver instance.
     *
     * @param array $paths
     * @param PathParser $parser
     *
     */
    public function __construct(array $paths = [], PathParser $parser = null)
    {
        $this->cache = [];
        $this->parser = $parser ?: new PathParser;

        $this->setPaths($paths);
    }

    /**
     * setPaths
     *
     * @param array $paths
     *
     * @return void
     */
    public function setPaths(array $paths)
    {
        foreach ($paths as $ns => $path) {
            if (is_int($ns)) {
                $ns = $this->parser->getDefaultNamespace();
            }

            $this->addNamespace($ns, $path);
        }
    }

    /**
     * addNamespace
     *
     * @param string $namespaces
     * @param string $path
     *
     * @return void
     */
    public function addNamespace($namespaces, $path)
    {
        $this->paths[$namespaces][] = $path;
    }

    /**
     * locate
     *
     * @param string $template
     *
     * @return string
     */
    public function locate($template)
    {
        return $this->resolve($template);
    }

    /**
     * resolve
     *
     * @param string $template
     *
     * @throws \InvalidArgumentException if template name could not be
     * resolved.
     * @return string
     */
    public function resolve($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        list ($name, $namespace, $template) = $this->parser->parse($name);

        if (!isset($this->paths[$namespace])) {
            throw new \InvalidArgumentException(sprintf('namespace %s does not exist', $namespace));
        }

        $path = null;

        foreach ($this->paths[$namespace] as $filePath) {

            if (is_file($path = rtrim($filePath, '\\\/').'/'.ltrim($template, '\\\/'))) {
                return $this->cache[$name] = $path;
            }
        }

        throw new \InvalidArgumentException(sprintf('no template found for "%s"', $name));
    }
}
