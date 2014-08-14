<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @class AbstractResource implements ResourceInterface
 * @see ResourceInterface
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * resource
     *
     * @var string
     */
    protected $resource;

    /**
     * Constructor.
     *
     * @param string $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($timestamp)
    {
        return $this->exists() && filemtime($this->getPath()) < $timestamp;
    }

    /**
     * Get the resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get the resource path.
     *
     * @return sring
     */
    public function getPath()
    {
        return $this->getResource();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getPath();
    }
}
