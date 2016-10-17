<?php
/**
 * Funaffect
 *
 * Original Class
 *
 * @package    wpconnecter
 * @version    1.8
 * @author     M.Oobayashi - Funaffect
 * @license    MIT License
 * @copyright  Funaffect
 * @link       https://funaffect.jp
 */


\Autoloader::add_core_namespace('Funaffect');

\Autoloader::add_namespace('Aws', APPPATH.'vendor/aws/aws-sdk-php/src/Aws', true);

\Autoloader::add_classes([
    // コアの拡張
    'Funaffect\\Validation' => __DIR__.'/classes/validation.php',
    'Funaffect\\Html'       => __DIR__.'/classes/html.php',
    
    // クラスの追加
    'Funaffect\\Filter'     => __DIR__.'/classes/filter.php',
    'Funaffect\\Aws'        => __DIR__.'/classes/aws.php',
    'Funaffect\\Aws\\S3'    => __DIR__.'/classes/aws/s3.php',

]);