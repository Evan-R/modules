<?php

/**
 * This File is part of the Selene\Module\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests;

use \Selene\Module\Common\IOEncryption;
use \Selene\Module\Cryptography\Crypter;

/**
 * @class IOEncryptionTest
 * @package Selene\Module\Common\Tests
 * @version $Id$
 */
class IOEncryptionTest extends IOProxyTest
{

    protected $crypter;

    protected function setUp()
    {
        if (!class_exists('Selene\Module\Cryptography\Crypter')) {
            $this->markTestIncomplete();
        }

        return parent::setUp();
    }

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
