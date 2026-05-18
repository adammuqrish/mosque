<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$__storage = __DIR__.'/../storage';
$__cache   = __DIR__.'/../bootstrap/cache';
if (!is_writable($__storage) || !is_writable($__cache)) {
    @chmod($__storage, 0777);
    @chmod($__cache, 0777);
    if (is_dir($__storage.'/logs')) { @chmod($__storage.'/logs', 0777); }
    if (is_dir($__storage.'/framework')) { @chmod($__storage.'/framework', 0777); }
    if (is_dir($__storage.'/framework/views')) { @chmod($__storage.'/framework/views', 0777); }
    if (is_dir($__storage.'/framework/cache')) { @chmod($__storage.'/framework/cache', 0777); }
    if (is_dir($__storage.'/framework/sessions')) { @chmod($__storage.'/framework/sessions', 0777); }
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
