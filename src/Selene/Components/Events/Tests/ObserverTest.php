<?php

/**
 * This File is part of the Selene\Components\Events\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Events\AbstractObservable;
use Selene\Components\Events\ObserverInterface;
use Selene\Components\Events\ObserveableInterface;

class ObserverTest extends TestCase
{
    /**
     * @expectedException CounterOverflowException
     */
    public function testAddObserver()
    {
        $subject = new ObservableStub;
        $observer = new ObserverStub($this);

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
        $subject = new ObservableStub;
        $observer = new ObserverStub($this);

        $subject->addObserver($observer);

        $subject->increment();
        $subject->increment();
        $subject->removeObserver($observer);
        $subject->increment();

        $this->assertTrue(true);
    }

}

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

class ObserverStub implements ObserverInterface
{
    public function __construct(TestCase $test)
    {
        $this->test = $test;;
    }

    public function update(\SplSubject $subject)
    {
        return $this->notify($subject);
    }

    public function notify(ObserveableInterface $subject)
    {
        if ($subject->getCounter() === 3) {
            throw new CounterOverflowException;
        }
    }
}

class CounterOverflowException extends Exception
{}
