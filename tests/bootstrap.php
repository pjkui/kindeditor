<?php
/**
 * PHPUnit bootstrap for pjkui/kindeditor.
 *
 * Loads Composer autoload, configures Yii aliases and error handling, and
 * stands up a minimal yii\web\Application so widget/action tests can run.
 */

error_reporting(E_ALL);
ini_set('display_errors', 'stderr');
date_default_timezone_set('UTC');

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    fwrite(STDERR, "Composer dependencies are missing. Run `composer install` first.\n");
    exit(1);
}
require $autoload;

// Yii2 bootstrap.
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@pjkui/kindeditor', dirname(__DIR__));
Yii::setAlias('@tests', __DIR__);

// Create a writable sandbox that simulates @webroot for upload tests.
$runtime = sys_get_temp_dir() . '/kindeditor-tests-' . getmypid();
if (!is_dir($runtime)) {
    mkdir($runtime, 0777, true);
}
Yii::setAlias('@webroot', $runtime);
Yii::setAlias('@web', '/');

new yii\web\Application([
    'id'         => 'kindeditor-tests',
    'basePath'   => dirname(__DIR__),
    'runtimePath' => $runtime,
    'components' => [
        'request' => [
            'cookieValidationKey' => 'kindeditor-tests',
            'scriptFile'  => __FILE__,
            'scriptUrl'   => '/index.php',
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
        ],
    ],
]);
