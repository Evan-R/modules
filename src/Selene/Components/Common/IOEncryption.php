<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common;

use \Selene\Components\Cryptography\Crypter;

/**
 * De/Encryption of input data.
 *
 * @class IOEncryption implements IOProxyInterface
 * @see IOHandlerInterface
 *
 * @package Selene\Components\Common
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
    public function __construct(Crypter $crypter)
    {
        $this->crypter = $crypter;
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
        return $this->crypter->encrypt($data);
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
        return $this->crypter->decrypt($data);
    }
}
