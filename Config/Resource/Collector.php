<?php

/*
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @abstract class AbstractCollector implements CollectorInterface
 * @see CollectorInterface
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Collector implements CollectorInterface
{
    /**
     * resources
     *
     * @var array
     */
    protected $resources;

    /**
     * Constructor.
     *
     * @param array $resources
     */
    public function __construct(array $resources = [])
    {
        $this->setResources($resources);
    }

    /**
     * setResources
     *
     * @param array $resources
     *
     * @return void
     */
    public function setResources(array $resources)
    {
        $this->resources = [];

        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * addResrouce
     *
     * @param ResourceInterface $resource
     *
     * @return void
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * addFileResource
     *
     * @param string $file
     *
     * @return void
     */
    public function addFileResource($file)
    {
        $this->addResource(new FileResource($file));
    }

    /**
     * addObjectResource
     *
     * @param object $object
     *
     * @return void
     */
    public function addObjectResource($object)
    {
        $this->addResource(new ObjectResource($object));
    }

    /**
     * getResources
     *
     * @return array
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * isValidResource
     *
     * @param int $timestamp
     *
     * @return boolean
     */
    public function isValid($timestamp = null)
    {
        $timestamp = $timestamp ?: time();

        foreach ($this->resources as $resource) {
            if (!$resource->isValid($timestamp)) {
                return false;
            }
        }

        return true;
    }
}
