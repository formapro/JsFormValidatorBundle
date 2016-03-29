<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

set_error_handler(
    function ($code, $message, $file, $line) {
        throw new \ErrorException($message . ' in ' . $file . ' line ' . $line, $code);
    },
    E_ALL ^ E_USER_DEPRECATED
);
$env = 'dev';
if (!empty($_SERVER['REQUEST_URI'])) {
    // This trick uses to test different translation domains
    preg_match('/test\/translations\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $requestParts);
    if ($requestParts && 'test' == $requestParts[1]) {
        $env = 'trans';
    }
    // This trick uses to test disabled validation
    preg_match('/test\/disable\/(\w+)\/\d/', $_SERVER['REQUEST_URI'], $requestParts);
    if ($requestParts && 'global' == $requestParts[1]) {
        $env = 'disable';
    }
    // This trick uses to test custom unique entity controller
    preg_match('/test\/customUniqueEntityController\/-\/-/', $_SERVER['REQUEST_URI'], $requestParts);
    if ($requestParts) {
        $env = 'unique';
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