<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Events;

use \Selene\Components\Events\Event;
use \Selene\Components\Package\PackageInterface;

/**
 * @class PackagePublishEvent
 * @package Selene\Components\Package
 * @version $Id$
 */
class PackageEvent extends Event
{
    private $package;

    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }

    public function getPackage()
    {
        return $this->package;
    }
}
