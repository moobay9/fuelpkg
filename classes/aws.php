<?php

namespace Funaffect;

class Aws
{
    /**
     * キー（ID）
     */ 
    public static $access_key    = '';

    /**
     * シークレット
     */ 
    public static $access_secret = '';

    // public static $credentials = null;

    /**
     * アクセス権限等の処理
     */ 
    public static function _init()
    {
        $config = \Config::load('aws_credential');

        if ( ! is_null($config['aws_credential']))
        {
            self::$access_key    = $config['aws_credential']['key'];
            self::$access_secret = $config['aws_credential']['secret'];

            putenv('AWS_ACCESS_KEY_ID='.    self::$access_key);
            putenv('AWS_SECRET_ACCESS_KEY='.self::$access_secret);

            // self::$credentials   = new \Aws\Credentials\Credentials(self::$access_key, self::$access_secret);
        }

    }

}