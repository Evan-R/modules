<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @class ObjectResource extends AbstractResource
 * @see AbstractResource
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ObjectResource extends FileResource implements ObjectResourceInterface
{
    protected $reflection;

    public function __construct($resource)
    {
        if (!is_object($resource) || $resource instanceof \Closure) {
            throw \InvalidArgumentException('resource must be an object');
        }

        parent::__construct($resource);
    }

    /**
     * getPath
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        return $this->getObjectReflection()->getFileName();
    }

    /**
     * getObjectReflection
     *
     * @access protected
     * @return \ReflectionObject
     */
    public function getObjectReflection()
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionObject($this->resource);
        }

        return $this->reflection;
    }
}
