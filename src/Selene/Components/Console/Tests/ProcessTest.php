<?php

/**
 * This File is part of the Selene\Components\Console package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */
namespace Selene\Components\Console\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Console\Process\Process;

/**
 * @class ProcessTest extends TestCase ProcessTest
 * @see TestCase
 *
 * @package Selene\Components\Console
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ProcessTest extends TestCase
{
    public function testLs()
    {
        $proc = new Process('dostuff');
        $proc->execute(function ($out, $type) use ($proc) {


            if (Process::ERROR === $type) {
                echo 'ERROR:', $out, PHP_EOL;
            } else {
                echo "OK:\n", $out, PHP_EOL;
            }
        });
        var_dump($proc->status());
    }
}
