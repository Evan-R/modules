<?php

/*
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common;

use \Selene\Module\Cryptography\Crypter;

/**
 * De/Encryption of input data.
 *
 * @class IOEncryption implements IOProxyInterface
 * @see IOHandlerInterface
 *
 * @package Selene\Module\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IOEncryption implements IOProxyInterface
{
    /**
     * crypter
     *
     * @var Crypter
     */
    private $crypter;

    /**
     *
     * @param Crypter $crypter
     *
     * @access public
     */
    public function __construct(Crypter $crypter = null)
    {
        $this->crypter = $crypter ?: new Crypter;
    }

    /**
     * in
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function in($data)
    {
        return $this->crypter->encryptEncode($data);
    }

    /**
     * out
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function out($data)
    {
        return $this->crypter->decryptDecode($data);
    }
}
