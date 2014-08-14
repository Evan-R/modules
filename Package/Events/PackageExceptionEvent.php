<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Events;

use \Selene\Module\Events\Event;
use \Selene\Module\Package\PackageInterface;

/**
 * @class PackagePublishEvent
 * @package Selene\Module\Package
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
