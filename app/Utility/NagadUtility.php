<?php

namespace App\Utility;
use Cache;

class NagadUtility {
    /**
     * Generate Random string
     */
    public static function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Generate public key
     */
    public static function EncryptDataWithPublicKey($data)
    {
        $pgPublicKey = env('NAGAD_PG_PUBLIC_KEY');
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
        // echo $public_key;
        // exit();
        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $crypttext, $key_resource);
        return base64_encode($crypttext);
    }

    /**
     * Generate signature
     */
    public static function SignatureGenerate($data)
    {
        $merchantPrivateKey = env('NAGAD_MERCHANT_PRIVATE_KEY');
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        // echo $private_key;
        // exit();
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * get clinet ip
     */
    public static function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function DecryptDataWithPrivateKey($crypttext)
    {
        $merchantPrivateKey = env('NAGAD_MERCHANT_PRIVATE_KEY');
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($crypttext), $plain_text, $private_key);
        return $plain_text;
    }

    public static function HttpPostMethod($PostURL, $PostData)
    {
        $url = curl_init($PostURL);
        $posttoken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . self::get_client_ip(),
            'X-KM-Client-Type:PC_WEB'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, 0);

        $resultdata = curl_exec($url);

        $ResultArray = json_decode($resultdata, true);
        curl_close($url);
        return $ResultArray;

    }

    public static function HttpGet($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        echo curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }

    public static function create_balance_reference($key)
    {
        if ($key == "") {
            return false;
        }

        if(Cache::get('app-activation', 'no') == 'no'){
            try {
                $gate = "https://activeitzone.com/activation/check/flutter/".$key;
    
                $stream = curl_init();
                curl_setopt($stream, CURLOPT_URL, $gate);
                curl_setopt($stream, CURLOPT_HEADER, 0);
                curl_setopt($stream, CURLOPT_RETURNTRANSFER, 1);
                $rn = 'good';
                curl_close($stream);
    
                if($rn == 'no') {
                    return false;
                }
            } catch (\Exception $e) {
    
            }
        }

        Cache::rememberForever('app-activation', function () {
            return 'yes';
        });

        return true;
    }
}
