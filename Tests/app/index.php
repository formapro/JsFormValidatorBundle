<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_error_handler(
    function ($code, $message, $file, $line) {
        throw new \ErrorException($message . ' in ' . $file . ' line ' . $line, $code);
    }
);
$env = 'dev';
if (!empty($_SERVER['REQUEST_URI'])) {
    // This trick uses to test different translation domains
    preg_match('/javascript_unit_test\/translations\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $requestParts);
    if ($requestParts && 'test' == $requestParts[1]) {
        $env = 'trans';
    }
    // This trick uses to test disabled validation
    preg_match('/javascript_unit_test\/disable\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $requestParts);
    if ($requestParts && 'global' == $requestParts[1]) {
        $env = 'disable';
    }
}

$loader = require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

/** @noinspection PhpUndefinedClassInspection */
$kernel   = new AppKernel($env, true);
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);