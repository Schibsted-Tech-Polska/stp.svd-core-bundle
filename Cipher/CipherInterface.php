<?php

namespace Svd\CoreBundle\Cipher;

/**
 * Cipher
 */
interface CipherInterface
{
    /**
     * Encrypt data
     *
     * @param string $data data
     *
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypt data
     *
     * @param string $data data
     *
     * @return string
     */
    public function decrypt($data);
}
