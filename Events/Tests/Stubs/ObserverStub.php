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

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Events\ObserverInterface;
use Selene\Components\Events\ObserveableInterface;

/**
 * @class ObserverStub
 * @package
 * @version $Id$
 */
class ObserverStub implements ObserverInterface
{
    public function __construct(TestCase $test)
    {
        $this->test = $test;
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
