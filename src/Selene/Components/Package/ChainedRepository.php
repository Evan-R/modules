<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

/**
 * @class ChainedRepository
 * @package Selene\Components\Package
 * @version $Id$
 */
class ChainedRepository implements PackageRepositoryInterface
{
    public function __construct(array $repositories)
    {

    }
}
