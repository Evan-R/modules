<?php

/**
 * This File is part of the Selene\Module\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests;

use Selene\Module\TestSuite\TestCase;
use Selene\Module\Events\AbstractObservable;
use Selene\Module\Events\ObserverInterface;
use Selene\Module\Events\ObserveableInterface;

class ObserverTest extends TestCase
{
    /** @test */
    public function isShouldIncrementNotofications()
    {
        $subject = new Stubs\ObservableStub;
        $observer = new Stubs\ObserverStub($this);

        $subject->addObserver($observer);

        $subject->increment();
        $subject->increment();
        $subject->increment();

        $this->assertSame(3, $observer->counter);
    }

    /** @test */
    public function isShouldRemoveObservers()
    {
        $subject = new Stubs\ObservableStub;
        $observer = new Stubs\ObserverStub($this);

        $subject->attach($observer);

        $subject->increment();
        $subject->increment();
        $subject->detach($observer);
        $subject->increment();

        $this->assertSame(2, $observer->counter);
    }
}
