<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$loader = require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/AppKernel.php';

set_error_handler(
    function ($code, $message, $file, $line) {
        throw new \ErrorException($message . ' in ' . $file . ' line ' . $line, $code);
    }
);
$env = 'test';
// This trick uses to test different translation domains
preg_match('/javascript_unit_test\/translations\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $testTranslationParameters);
if ($testTranslationParameters && 'default' != $testTranslationParameters[1]) {
    $env = 'trans';
}

use Symfony\Component\HttpFoundation\Request;

/** @noinspection PhpUndefinedClassInspection */
$kernel   = new AppKernel($env, true);
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);