<?php

declare(strict_types=1);

use Tempest\Router\HttpApplication;

ignore_user_abort(enable: true);

require_once __DIR__ . '/../vendor/autoload.php';

$app = HttpApplication::boot(root: __DIR__ . '/..');

$handler = static function () use ($app): void {
    $app->run();
};

$maxRequests = (int) ($_SERVER['MAX_REQUESTS'] ?? 0);

for ($n = 0; $maxRequests === 0 || $n < $maxRequests; $n++) {
    $keepRunning = frankenphp_handle_request(callback: $handler);

    gc_collect_cycles();

    if (!$keepRunning) {
        break;
    }
}
