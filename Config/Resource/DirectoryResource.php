<?php

/*
 * This File is part of the Selene\Module\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @class DirectoryResource extends Resource
 * @see Resource
 *
 * @package Selene\Module\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class DirectoryResource extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return is_dir($this->resource);
    }
}
