<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';
$env = getenv('ENVIRONMENT') ? getenv('ENVIRONMENT') : 'dev';
if ($env !== 'prod') {
    Debug::enable();
} else {

    if (PHP_VERSION_ID < 70000) {
        include_once __DIR__.'/../var/bootstrap.php.cache';
    }
    $env = 'prod';
}

$kernel = new AppKernel($env, false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
