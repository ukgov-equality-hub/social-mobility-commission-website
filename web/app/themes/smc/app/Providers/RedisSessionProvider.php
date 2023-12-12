<?php
/**
 * N.B. This requires the CREDIS library
 * ` composer require colinmollenhour/credis ^1.12 `
 */

namespace App\Providers;

use App\Session\Drivers\RedisSessionDriver;
use Credis_Client;
use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Contracts\Encrypter as EncrypterContract;
use Rareloop\Lumberjack\Facades\Session;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use Rareloop\Lumberjack\Session\EncryptedStore;
use Rareloop\Lumberjack\Session\Store;


class RedisSessionProvider extends ServiceProvider
{
    /**
     * Register any app specific items into the container
     */
    public function register(): void
    {
        $redisHost       = getenv('REDIS_HOST') ?: 'localhost';
        $redisPort       = getenv('REDIS_PORT') ?: 6379;
        $timeout         = null;
        $persistent      = '';
        $db              = 0;
        $password        = getenv('REDIS_PASSWORD') ?: null;
        $redisConnection = new Credis_Client($redisHost, $redisPort, $timeout, $persistent, $db, $password);
        $this->app->bind('redis_connection', $redisConnection);
    }

    /**
     * Perform any additional boot required for this application
     */
    public function boot(): void
    {
        $redisConnection = $this->app->get('redis_connection');
        $config          = $this->app->get(Config::class);
        Session::extend('redis', static function ($app) use ($redisConnection, $config) {
            $sessionLifetime = $config->get('session.lifetime', 120);
            $handler         = new RedisSessionDriver($redisConnection, $sessionLifetime);

            $sessionName = $config->get('session.cookie', 'lumberjack');

            $sessionId = ($_COOKIE[$sessionName] ?? null);

            if ($config->get('session.encrypt')) {
                $encrypter = $app->get(EncrypterContract::class);

                return new EncryptedStore($sessionName, $handler, $encrypter, $sessionId);
            }

            return new Store($sessionName, $handler, $sessionId);
        });
    }
}
