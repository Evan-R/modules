<?php

/**
 * This File is part of the Stream\Cryptography package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cryptography\Tests;

use Selene\Module\TestSuite\TestCase;

/**
 * Class Hashbcrypttest
 * @author
 */
abstract class HashTestCase extends TestCase
{
    protected $hash;

    public function testHashCreateAndValidate()
    {
        $hash = $this->hash->hash('bragging');
        $this->assertTrue($this->hash->check('bragging', $hash));
        $this->assertFalse($this->hash->check('brAgging', $hash));
    }
}
