<?php

/**
 * This File is part of the Selene\Module\Events\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events\Tests\Stubs;

use Selene\Module\TestSuite\TestCase;
use Selene\Module\Events\ObserverInterface;
use Selene\Module\Events\ObserveableInterface;

/**
 * @class ObserverStub
 * @package
 * @version $Id$
 */
class ObserverStub implements ObserverInterface
{
    public $counter = 0;

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
        $this->counter++;
    }
}
