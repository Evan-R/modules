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
 * @interface CollectorInterface
 * @package Selene\Module\Config\Resource
 * @version $Id$
 */
interface CollectorInterface
{
    public function addFileResource($file);

    public function addObjectResource($object);

    public function getResources();

    public function isValid($timestamp = null);
}
