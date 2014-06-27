<?php

/**
 * This File is part of the Selene\Components\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests;

use \Selene\Components\Common\IOEncryption;
use \Selene\Components\Cryptography\Crypter;

/**
 * @class IOEncryptionTest
 * @package Selene\Components\Common\Tests
 * @version $Id$
 */
class IOEncryptionTest extends IOProxyTest
{

    protected $crypter;

    protected function getIO()
    {
        $this->crypter = new Crypter;
        return new IOEncryption($this->crypter);
    }

    /**
     * @dataProvider inDataProvider
     */
    public function testIn($is, $expected)
    {
        $out = $this->io->in($is);
        $this->assertSame($expected, $this->crypter->decrypt($out));
    }


    /**
     * @dataProvider outDataProvider
     */
    public function testOut($is, $expected)
    {
        parent::testOut($this->crypter->encrypt($is), $expected);
        //var_dump($this->io->out($is));
        //var_dump($this->crypter->encrypt($is));
    }
}