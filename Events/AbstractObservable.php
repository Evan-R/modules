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

/**
 * @class AbstractObservable
 * @package
 * @version $Id$
 */
abstract class AbstractObservable implements ObserveableInterface
{
    use Traits\Observable;
}
