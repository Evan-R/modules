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

use Selene\Components\Events\EventInterface;

/**
 * @class EventStub
 * @see EventInterface
 *
 * @package Selene\Components\Events\Tests\Stubs
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class EventStub implements EventInterface
{
    protected $stopped = false;

    protected $name = false;

    public function stopPropagation()
    {
        $this->stopped = true;
    }

    public function isPropagationStopped()
    {
        return $this->stopped;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
