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
    /**
     * @expectedException Selene\Components\Events\Tests\Stubs\CounterOverflowException
     */
    public function testAddObserver()
    {
        $subject = new Stubs\ObservableStub;
        $observer = new Stubs\ObserverStub($this);

        $subject->addObserver($observer);

        $subject->increment();
        $subject->increment();
        $subject->increment();
    }

    /**
     * testAddObserver
     *
     *
     * @access public
     * @return mixed
     */
    public function testRemoveObserver()
    {
        $subject = new Stubs\ObservableStub;
        $observer = new Stubs\ObserverStub($this);

        $subject->addObserver($observer);

        $subject->increment();
        $subject->increment();
        $subject->removeObserver($observer);
        $subject->increment();

        $this->assertTrue(true);
    }
}
