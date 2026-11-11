<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'gruas_autocarga';
const DB_USER = 'root';
const DB_PASS = '';

/*
 * En Render, las variables de entorno reemplazan los valores locales.
 */
function env_value(string $key, string $default): string {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

define('APP_DB_HOST', env_value('DB_HOST', DB_HOST));
define('APP_DB_PORT', env_value('DB_PORT', DB_PORT));
define('APP_DB_NAME', env_value('DB_NAME', DB_NAME));
define('APP_DB_USER', env_value('DB_USER', DB_USER));
define('APP_DB_PASS', env_value('DB_PASS', DB_PASS));
