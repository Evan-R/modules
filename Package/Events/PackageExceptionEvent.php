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
class PackageExceptionEvent extends PackageEvent
{
    private $exception;

    public function __construct(PackageInterface $package, \Exception $e)
    {
        $this->exception = $e;
        parent::__construct($package);
    }

    /**
     * getException
     *
     *
     * @access public
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
