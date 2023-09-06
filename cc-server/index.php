<?php

declare(strict_types=1);

$error_context = (object)[
  'node' => '',
  'path' => '',
  'method' => '',
  'user' => '- anon -'
];

/**
 * Reference implementation of a credit commons node
 */
require_once './vendor/autoload.php';
ini_set('html_errors', '0');
$temp_config = parse_ini_file('./node.ini');
if (\CCNode\Db::connect($temp_config['db']['name'], $temp_config['db']['user'], $temp_config['db']['pass'], $temp_config['db']['server'])->connect_error) {
    header('Location: config/index.php');
}

// Treat all warnings as errors
error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
});

//  Simpletest needs to be able to call $app->run() itself.
require './slimapp.php';
$app->run();
