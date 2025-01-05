<?php

namespace App\Services;

use Exception;

class AESDecryptor
{
    private $key;

    public function __construct()
    {
        // Load key from .env
        $this->key = hex2bin(env('AES_KEY'));
        if ($this->key === false) {
            throw new Exception("Invalid AES key.");
        }
    }

    /**
     * Decrypt AES-128-ECB encrypted data.
     *
     * @param string $encryptedData
     * @return array|string
     * @throws Exception
     */
    public function decrypt(string $encryptedData)
    {
        // Decode Base64
        $decodedData = base64_decode($encryptedData);
        if ($decodedData === false) {
            throw new Exception("Invalid base64 data.");
        }

        // Decrypt AES
        $decrypted = openssl_decrypt(
            $decodedData,
            'AES-128-ECB',
            $this->key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
        );

        if ($decrypted === false) {
            throw new Exception("Decryption failed.");
        }

        // Remove padding (\0 or PKCS7 if needed)
        $decrypted = rtrim($decrypted, "\0");

        // Decode JSON if possible
        $jsonData = json_decode($decrypted, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $jsonData; // Return as array if JSON
        }

        return $decrypted; // Return raw string if not JSON
    }
}
