<?php

/**
 * This File is part of the Selene\Components\Common\Tests\Helper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests\Helper;

/**
 * @class RandTest
 * @package Selene\Components\Common\Tests\Helper
 * @version $Id$
 */
class RandTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shotRand()
    {
        $store = [];

        foreach (range(0, 999) as $index) {
            $r = rand();

            if (in_array($r, $store)) {
                echo 'match';
            }
            $store[] = $r;
        }
    }
}
