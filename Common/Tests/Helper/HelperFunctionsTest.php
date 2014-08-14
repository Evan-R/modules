<?php

/**
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Helper;

use Mockery as m;

/**
 * @class HelperFunctionsTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Module\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class HelperFunctionsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function testArrayColumn()
    {
        $in = [
            [
                'id' => '12',
                'name' => 'rand',
                'handle' => 'xkd23',
            ],
            [
                'id' => '14',
                'name' => 'band',
                'handle' => 'xkd25',
            ],
            [
                'id' => '22',
                'name' => 'land',
                'handle' => 'xkd77',
            ],
        ];

        $this->assertEquals(['12', '14', '22'], array_column($in, 'id'));
        $this->assertEquals(['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'], array_column($in, 'id', 'handle'));
    }

    /** @test */
    public function testClearValue()
    {
        $this->assertNull(clearValue(''));
        $this->assertNull(clearValue(' '));
        $this->assertNull(clearValue(null));
        $this->assertFalse(is_null(clearValue(0)));
        $this->assertFalse(is_null(clearValue(false)));
        $this->assertFalse(is_null(clearValue([])));
    }
}
