<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$loader = require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

/** @noinspection PhpUndefinedClassInspection */
$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);