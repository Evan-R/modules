<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

use SplSubject;
use SplObserver;

/**
 * @class ObserveableInterface
 * @package
 * @version $Id$
 */
interface ObserveableInterface extends SplSubject
{
    public function addObserver(ObserverInterface $observer);

    public function removeObserver(ObserverInterface $observer);
}
