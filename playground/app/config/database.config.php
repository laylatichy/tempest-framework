<?php

declare(strict_types=1);

namespace app\config;

use RuntimeException;
use Tempest\Database\Config\MysqlConfig;

use function Tempest\env;

$host     = env(key: 'DATABASE_HOST');
$port     = env(key: 'DATABASE_PORT');
$username = env(key: 'DATABASE_USER');
$password = env(key: 'DATABASE_PASS');
$database = env(key: 'DATABASE_NAME');

if (!is_string(value: $host) || !is_string(value: $port) || !is_string(value: $username) || !is_string(value: $password) || !is_string(value: $database)) {
    throw new RuntimeException(message: 'DATABASE_* env vars must be strings');
}

return new MysqlConfig(
    host: $host,
    port: $port,
    username: $username,
    password: $password,
    database: $database,
);
