<?php

/**
 * This File is part of the Selene\Components\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Events\AbstractObservable;
use Selene\Components\Events\ObserverInterface;
use Selene\Components\Events\ObserveableInterface;

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
