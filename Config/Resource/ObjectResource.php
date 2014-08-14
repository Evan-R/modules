<?php

/**
 * This File is part of the Selene\Module\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @class ObjectResource extends AbstractResource
 * @see AbstractResource
 *
 * @package Selene\Module\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ObjectResource extends FileResource implements ObjectResourceInterface
{
    /**
     * reflection
     *
     * @var \ReflectionObject|null
     */
    protected $reflection;

    /**
     * Constructor.
     *
     * @param object $resource
     */
    public function __construct($resource)
    {
        if (!is_object($resource) || $resource instanceof \Closure) {
            throw \InvalidArgumentException('resource must be an object');
        }

        parent::__construct($resource);
    }

    /**
     * Get the file path of the object.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getObjectReflection()->getFileName();
    }

    /**
     * Get the reflection of the object.
     *
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
