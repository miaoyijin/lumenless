<?php
/**
 *加解密
 */

namespace  infra\librarys\protocoll;

use infra\librarys\utils\Functions;
use infra\contracts\ProtocolInterface;

/**
 * @author mouyj
 */
class RsaProtocoll implements ProtocolInterface
{
    /**
     * 私钥解密
     * @param  string  $data data
     * @return mixed
     */
    public function convertInput(string $data)
    {
        $not = app('request')->header('not');
        if ($not && Functions::isDev()) {
            $data = json_decode($data, true);
        } else {
            $data = base64_decode($data);
            $dataDecryptStr = implode(
                '',
                array_map(function (string $val) {
                    openssl_private_decrypt($val, $subStr, config('app.ikey'));
                    return $subStr;
                }, str_split($data, 128))
            );
            $data = json_decode($dataDecryptStr, true);
        }
        try {
            $data['uid'] = $data['body']['commonRequest']['user']['uid'] ?? '';
            $data['passid'] = $data['body']['commonRequest']['user']['passid'] ?? '';
            return $data;
        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }

    /**
     * 私钥加密
     * @param  string|array  $data data
     * @return mixed
     */
    public function convertOutput($data)
    {
        $not = app('request')->header('not');
        if ($not && Functions::isDev()) {
            return $data;
        }
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $encryptStr = implode(
            "",
            array_map(function (string $val) {
                openssl_private_encrypt($val, $subStr, config('app.ikey'));
                return $subStr;
            }, str_split($data, 117))
        );
        if ($encryptStr) {
            return base64_encode($encryptStr);
        } else {
            return null;
        }
    }

    /**
     * 公钥解密
     * @param  string  $data data
     * @return mixed
     */
    public function convertInputPKeyTest(string $data)
    {
        $not = app('request')->header('not');
        if ($not && Functions::isDev()) {
            $body = json_decode($data, true);
        } else {
            $data = base64_decode($data);
            $decryptStr = implode(
                '',
                array_map(function (string $val) {
                    openssl_public_decrypt($val, $subStr, config('app.pkey'));
                    return $subStr;
                }, str_split($data, 128))
            );
            $body = json_decode($decryptStr, true);
        }
        return $body;
    }


    /**
     * 私钥解密
     * @param  string  $data data
     * @return mixed
     */
    public function convertInputIKeyTest(string $data)
    {
        $not = app('request')->header('not');
        if ($not && Functions::isDev()) {
            $body = json_decode($data, true);
        } else {
            $data = base64_decode($data);
            $decryptStr = implode(
                '',
                array_map(function (string $val) {
                    openssl_private_decrypt($val, $subStr, config('app.ikey'));
                    return $subStr;
                }, str_split($data, 128))
            );
            $body = json_decode($decryptStr, true);
        }
        if ($body) {
            return $body ;
        }
        return null;
    }

    /**
     * 生成密钥
     * @return array
     */
    public function makeRsaKeyTest()
    {
        $res = openssl_pkey_new(array('private_key_bits' => 1024));
        openssl_pkey_export($res, $privkey);
        $pubkey = openssl_pkey_get_details($res);
        $pubkey = $pubkey["key"];
        var_dump($privkey);
        var_dump($pubkey);
    }
}
