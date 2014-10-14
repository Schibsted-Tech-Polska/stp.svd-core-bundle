<?php

namespace Svd\CoreBundle\Cipher;

/**
 * Cipher
 */
class Mcrypt implements CipherInterface
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $cipher;

    /** @var string */
    protected $mode;

    /** @var integer */
    protected $ivSize;

    /**
     * Construct
     *
     * @param string    $salt      salt
     * @param string    $cipher    cipher
     * @param string    $mode      mode
     */
    public function __construct($salt, $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC)
    {
        $this->key = $this->getKey($salt);
        $this->cipher = $cipher;
        $this->mode = $mode;

        $this->ivSize = mcrypt_get_iv_size($this->cipher, $this->mode);
    }

    /**
     * Encrypt data
     *
     * @param string $data data
     *
     * @return string
     */
    public function encrypt($data)
    {
        $iv = mcrypt_create_iv($this->ivSize, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt($this->cipher, $this->key, $data, $this->mode, $iv);
        $ciphertext = $iv . $ciphertext;

        $res = base64_encode($ciphertext);

        return $res;
    }

    /**
     * Decrypt data
     *
     * @param string $data data
     *
     * @return string
     */
    public function decrypt($data)
    {
        $ciphertext = base64_decode($data);

        $iv = substr($ciphertext, 0, $this->ivSize);
        $ciphertext = substr($ciphertext, $this->ivSize);
        $res = mcrypt_decrypt($this->cipher, $this->key, $ciphertext, $this->mode, $iv);

        return $res;
    }

    /**
     * Get key
     *
     * @param string $salt salt
     *
     * @return string
     */
    protected function getKey($salt)
    {
        $key = md5($salt);

        return $key;
    }
}
