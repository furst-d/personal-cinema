<?php declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;

require_once dirname(__DIR__).'/vendor/autoload.php';

ErrorHandler::register(null, false);

$_ENV['APP_ENV'] = 'test';
$_ENV['APP_DEBUG'] = '1';

if (method_exists(Dotenv::class, 'bootEnv')) {
    $dotenv = new Dotenv();
    // Explicitně načítání .env.test
    $dotenv->load(dirname(__DIR__).'/.env.test');
}
