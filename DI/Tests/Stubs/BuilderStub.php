<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Stubs;

use \Selene\Components\DI\Processor\ProcessorInterface;

/**
 * @class BuilderStub
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
class BuilderStub extends \Selene\Components\DI\Builder
{

    protected $processes;

    public function setBaseProcessorConfig(array $processes)
    {
        $this->processes = $processes;
        foreach ($processes as $process) {
        }
    }

    protected function configureProcessor(ProcessorInterface $processor)
    {
        foreach ($this->processes as $process) {
            $processor->add($process[0], $process[1]);
        }
    }
}
