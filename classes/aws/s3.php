<?php
/**
 * Aws_S3
 *
 * AWS SDK を利用する
 *
 */
namespace Funaffect\Aws;

class S3 extends \Funaffect\Aws
{

    public static $s3_client = null;

    public static function _init()
    {
        parent::_init();

        self::$s3_client = new \Aws\S3\S3Client([
            'version'     => 'latest',
            'region'      => 'ap-northeast-1',
        ]);
    }

    public static function lists()
    {
        try
        {
            var_dump(self::$s3_client->listBuckets());
        }
        catch (\Aws\S3\Exception\S3Exception $e)
        {
            print_r($e->getMessage());
        }
    }

}