<?php
/**
 * Author:RJH
 * 适用php7.1及以上
 * 对称加密，解密封装类
 */

namespace common;


class OpensslClass
{
    private static $cipher = 'AES-128-CBC';
    private static $iv = "";
    private static $key = "";
    public function __construct($key = '', $iv = '') {
        if (!empty($key)) self::$key = base64_decode($key);
        if (!empty($iv)) self::$iv = base64_decode($iv);
    }

    /**
     * php7.1 使用openssl_encrypt()加密，加密方式：AES-128-CBC
     * @param $info
     * @return string
     */
    public function signQuestion($info) {
        $key = signKey();
        $plaintext = json_encode($info);
        $cipher = self::$cipher;
        $iv_size = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($iv_size);
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=1, $iv);
        # 对密文进行 base64 编码
        $output_data['ciphertext'] = base64_encode($ciphertext);
        $output_data['key'] = base64_encode($key);
        $output_data['iv'] = base64_encode($iv);

        return implode(',',$output_data);
    }


    /**
     * php7.1 使用openssl_decrypt()解密，解密方式：AES-128-CBC，与加密相同
     * @param $ciphertext
     * @return string
     */
    public function unsignQuestion($ciphertext) {
        # === 警告 ===
        # 密文并未进行完整性和可信度保护，
        # 所以可能遭受 Padding Oracle 攻击。
        # --- 解密 ---
        $ciphertext = base64_decode($ciphertext);
        $plaintext_dec = openssl_decrypt($ciphertext, self::$cipher, self::$key, 1, self::$iv);
        return $plaintext_dec;
    }

}