<?php

/*
 * This File is part of the Selene\Module\Routing\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

/**
 * @class ApcSectionCache
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcSectionCache implements SectionCacheInterface
{
    /**
     * Constructor.
     *
     * @param string $prefix
     */
    public function __construct($prefix = 'routing_sections.')
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return apc_exists($this->prefix.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path)
    {
        return apc_fetch($this->prefix.$path);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $names)
    {
        $data = $this->get($path) ?: [];

        apc_store($this->prefix.$path, array_unique(array_merge($data, (array)$names)));
        apc_store($this->prefix.$path.'.lastmod', time());
    }

    public function getModTime($id)
    {
        return apc_fetch($this->prefix.$id.'.lastmod') ?: time();
    }
}
