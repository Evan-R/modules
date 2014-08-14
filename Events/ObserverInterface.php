<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events;

use SplSubject;
use SplObserver;

/**
 * @class ObserverInterface
 * @package
 * @version $Id$
 */
interface ObserverInterface extends SplObserver
{
    /**
     * notify
     *
     * @see http://php.net/manual/en/class.splobserver.php
     */
    public function notify(ObserveableInterface $subject);
}
