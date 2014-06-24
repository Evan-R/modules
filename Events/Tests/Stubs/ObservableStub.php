<?php

/**
 * This File is part of the Selene\Components\Events\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Tests\Stubs;

use Selene\Components\Events\AbstractObservable;

/**
 * @class ObservableStub
 * @package
 * @version $Id$
 */
class ObservableStub extends AbstractObservable
{
    protected $counter = 0;

    public function increment()
    {
        $this->counter++;
        $this->notify();
    }

    public function getCounter()
    {
        return $this->counter;
    }
}
