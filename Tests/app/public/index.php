<?php

$loader = require_once __DIR__.'/../autoload.php';
require_once __DIR__.'/../AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use Fp\JsFormValidatorBundle\Tests\AppKernel;

$kernel = new AppKernel('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);