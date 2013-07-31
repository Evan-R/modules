<?php

/**
 * This File is part of the Selene\Components\Cryptography package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cryptography;

/**
 * @class Crypter
 *
 * @package Selene\Components\Cryptography
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class Crypter
{
    /**
     * Mcrypt Cypher Constant
     * @var string
     * @access private
     */
    private $cypher = null;

    /**
     * @var resource
     * @access private
     */
    private $cryptModule = null;

    /**
     * cryptModule
     *
     * @var Mixed
     * @access private
     */
    private $key;

    /**
     * _salt
     *
     * @var Mixed
     * @access private
     */
    private $salt;

    /**
     * @param string $key
     * @param string $salt
     * @param string|constant $decm Mcrypt Cypher Constant
     * for setting the encyption algorithm.
     * Defaults to `MCRYPT_BLOWFISH`.
     * See also http://php.net/manual/en/mcrypt.ciphers.php
     *
     * @access public
     * @return void
     */
    public function __construct($key = null, $salt = null, $decm = MCRYPT_BLOWFISH)
    {
        $this->cypher = $decm;
        $this->key    = $key;
        $this->salt   = $salt;
    }

    /**
     * close encryption module when Object gets destroyed;
     *
     * @access public
     * @return void
     */
    public function __destruct()
    {
        $this->closeCryptModule();
        $this->cypher = null;
    }

    /**
     * setKey
     *
     * @param Mixed $key
     * @access public
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * setSalt
     *
     * @param Mixed $salt
     * @access public
     * @return void
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Encrypts given data
     *
     * Key and Salt is not needed for
     * encryprionprocess. The actual Encryption key is generated from
     * a substring of a SHA1 checksum from `$key` and `$salt`.
     *
     * @param  mixed   $data   the data to be encrypted. (string, number or array)
     * @param  mixed   $key    encryption key
     * @param  mixed   $salt   salt added to encryption key
     * @access public
     * @return string encrypted data
     */
    public function encrypt($data)
    {
        $keyVals = $this->getCryptKeyVals();
        $iv = mcrypt_create_iv($keyVals['ivsize'], MCRYPT_DEV_RANDOM);

        mcrypt_generic_init($keyVals['cmodule'], $keyVals['keyfinal'], $iv);

        // be sure to serialize data as it my be an array;
        $enc = mcrypt_generic($keyVals['cmodule'], serialize($data));
        mcrypt_generic_deinit($keyVals['cmodule']);
        return $iv . $enc;
    }

    /**
     * Decrypts given encrypted string
     *
     * @param  string  $data   encrypted data
     * @param  mixed   $key    key used on encryption
     * @param  mixed   $salt   salt used on encryption
     * @access public
     * @return mixed   the encrypted data
     */
    public function decrypt($data)
    {
        $keyVals = $this->getCryptKeyVals();

        $iv = substr($data, 0, $keyVals['ivsize']);

        $data = substr($data, $keyVals['ivsize']);

        mcrypt_generic_init($keyVals['cmodule'], $keyVals['keyfinal'], $iv);
        $dec = mdecrypt_generic($keyVals['cmodule'], $data);

        return unserialize($dec);
    }

    /**
     * encryptEncode
     *
     * @see Lib\Toolkit\Crypter#decrypt()
     * @access public
     * @return string base64 encoded encrypted data
     */
    public function encryptEncode($data)
    {
        return base64_encode($this->encrypt($data));
    }

    /**
     * compressEncryptEncode
     * @see Lib\Toolkit\Crypter#decrypt()
     * @return gzip string compressed base64 encoded encrypted data
     */
    public function compressEncryptEncode($data)
    {
        return base64_encode(gzcompress($this->encrypt($data)));
    }

    /**
     * decryptDecode
     *
     * @param mixed $data
     * @param mixed $key
     * @param mixed $salt
     * @access public
     * @return void
     */
    public function decryptDecode($data)
    {
        return $this->decrypt(base64_decode($data));
    }

    /**
     * decryptDecode
     * @see Lib\Toolkit\Crypter#decrypt()
     */
    public function uncompressDecryptDecode($data)
    {
        return $this->decrypt(gzuncompress(base64_decode($data)));
    }

    /**
     *
     * _getCryptModule
     * @todo: mcrypt_module_open requires stream mode for certain cyphers (e.g.
     * AC4) which results in an IV size of 0.
     *
     * @access private
     * @return void
     */
    private function getCryptModule()
    {
        $throws = false;

        if (!$this->cryptModule) {
            $cm = mcrypt_module_open($this->cypher, null, MCRYPT_MODE_CBC, null);
            //try {
            //    $cm = mcrypt_module_open($this->cypher, null, MCRYPT_MODE_CBC, null);
            //} catch (\Exception $e) {
            //    $throws = true;
            //}
            //if ($throws) {
            //    $cm = mcrypt_module_open($this->cypher, null, MCRYPT_MODE_STREAM, null);
            //}
            $this->cryptModule = $cm;
        }

        return $this->cryptModule;
    }

    /**
     * _closeCryptModule
     *
     * @access private
     * @return boolean
     */
    private function closeCryptModule()
    {
        if ($this->cryptModule) {
            mcrypt_module_close($this->cryptModule);
            return true;
        }
        return false;
    }

    /**
     * _getCryptKeyVals
     *
     * @todo: mcrypt_module_open requires stream mode for certain cyphers (e.g.
     * AC4) which results in an IV size of 0.
     * @param mixed $key
     * @param mixed $salt
     * @access private
     * @return array
     */
    private function getCryptKeyVals()
    {
        $cypher = $this->getCryptModule();
        $ivSize = mcrypt_enc_get_iv_size($cypher);

        return [
            'cmodule'  => $cypher,
            'keyfinal' => substr(hash('sha1', $this->salt . $this->key), 0, mcrypt_enc_get_key_size($cypher)),
            'ivsize'   => $ivSize
        ];
    }
}
