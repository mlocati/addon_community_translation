<?php

const C5_ENVIRONMENT_ONLY = true;
const APP_UPDATED_PASSTHRU = true;

define('DIR_APPLICATION', __DIR__);
const DIR_BASE = __DIR__;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$path = dirname(__DIR__) . '/vendor/concrete5/core';

// Load in the required constants
require_once $path . '/bootstrap/configure.php';

// Load in concrete5's autoloader
require_once $path . '/bootstrap/autoload.php';

// Get the concrete5 application
$cms = require_once $path . '/bootstrap/start.php';

// Boot the runtime
$runtime = $cms->getRuntime();
$runtime->boot();

require_once __DIR__.'/../controller.php';

$package = $cms->make(Concrete\Package\CommunityTranslation\Controller::class);
/* @var Concrete\Package\CommunityTranslation\Controller $package */

$app->singleton('community_translation/config', function() use ($package) {
    return $package->getFileConfig();
});

$package->on_tests_start();

if (php_sapi_name() === 'cli-server') {
    $request = Request::getInstance();
    $server = $app->make(Concrete\Core\Http\DefaultServer::class);
    /* @var Concrete\Core\Http\DefaultServer $server */
    $response = $server->handleRequest($request);
    $response->prepare($request)->send();
    exit(0);
}

$process = new Symfony\Component\Process\Process(basename(PHP_BINARY) . ' -n -S localhost:49150 ' . escapeshellarg(__FILE__));
$process->setWorkingDirectory(dirname(PHP_BINARY));
$process->setEnhanceWindowsCompatibility(false);
$process->setTimeout(0);
$process->start();

register_shutdown_function(function() use ($process) {
    $process->stop(0);
});

return $cms;
