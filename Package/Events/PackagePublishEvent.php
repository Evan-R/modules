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

use \Selene\Components\Package\PackageInterface;

/**
 * @class PackagePublishEvent
 * @package Selene\Components\Package
 * @version $Id$
 */
class PackagePublishEvent extends PackageEvent
{
    private $file;

    public function __construct(PackageInterface $package, $file)
    {
        $this->file = $file;
        parent::__construct($package);
    }

    public function getFile()
    {
        return $this->file;
    }
}
