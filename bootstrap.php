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

// \Autoloader::add_core_namespace('Funaffect');

// \Autoloader::add_namespace('Aws', APPPATH.'vendor/aws/aws-sdk-php/src/Aws', true);

\Autoloader::add_classes([
    // コアの拡張
    'Funaffect\\Controller\\Template'  => __DIR__.'/classes/controller/template.php',
    'Funaffect\\Form'                  => __DIR__.'/classes/form.php',
    'Funaffect\\Validation'            => __DIR__.'/classes/validation.php',
    'Funaffect\\Html'                  => __DIR__.'/classes/html.php',

    // クラスの追加
    'Funaffect\\Observer_Format'      => __DIR__.'/classes/observer/format.php',
    'Funaffect\\Observer_Replacement' => __DIR__.'/classes/observer/replacement.php',

    'Funaffect\\Filter'                => __DIR__.'/classes/filter.php',
    'Funaffect\\Aws'                   => __DIR__.'/classes/aws.php',
    'Funaffect\\Aws\\S3'               => __DIR__.'/classes/aws/s3.php',
]);
