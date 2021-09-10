<?php

namespace App\Tools;

class UrlHelper
{
    public static function encrypt($value)
    {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        return openssl_encrypt($value, $cipher, "cat", $options=OPENSSL_RAW_DATA, $iv);
    }

    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}