<?php

/**
 * This File is part of the Stream\Cryptography package
 *
 * (c) Thomas Apple <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cryptography\Tests;

use Selene\Module\TestSuite\TestCase;
use Selene\Module\Cryptography\Crypter;

class CrypterTest extends TestCase
{
    protected $crypter;

    protected function setUp()
    {
        $this->key  = 'foobar';
        $this->salt = 'somesalt';
        $this->crypter = new Crypter($this->key, $this->salt);
    }

    public function testEncyptData()
    {
        $data = 'secret string';

        $secret = $this->crypter->encrypt($data);
        $this->assertEquals('secret string', $this->crypter->decrypt($secret));
    }
}
