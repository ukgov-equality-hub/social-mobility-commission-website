<?php
/**
 * [PROJECT_ROOT]/scripts/Bedrock/Postinstall.php
 *
 * This script is triggered by Composer install or composer update
 * It's purpose is to remove any default WordPress themes installed
 * when composer update runs and WordPress gets a version bump.
 *
 * Removing default themes removes WordPress identifiers lowering the target profile
 * It also removes any vulnerabilities that may later be discovered in those themes.
 *
 * Class PostInstall
 *
 * @package Application
 * @require Symfony\Filesystem and Composer\Composer
 */

namespace Bedrock;

use Composer\Script\Event;
use Dotenv\Dotenv;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Postinstall
{
    /**
     * Set .env LOCALLY_ENABLED_PLUGINS to override
     */
    private static array $locallyEnablePlugins = [
        'debug-bar',
        'debug-bar-timber',
        'show-current-template'
    ];

    /**
     * Set .env LOCALLY_DISABLED_PLUGINS to override
     */
    private static array  $locallyDisablePlugins = [
        'better-wp-security',
        'http-security',
        'really-simple-ssl',
        'wordfence',
        'civic-cookie-control-8'
    ];

    private static ?Event $event                 = null;

    private static string $rootPath              = '';

    public static function cleanup(Event $event): void
    {
        self::$event = $event;

        self::$rootPath = realpath(__DIR__ . '/../../');

        self::removeWpContent();
        self::disablePluginsInDev();
    }

    public static function disablePluginsInDev(): void
    {
        $dotenv = Dotenv::createImmutable(self::$rootPath);
        $dotenv->load();

        if (true === self::isNotLocal()) {
            self::echo('Not a local dev site, skipping plugin toggling');

            return;
        }

        self::echo('<info>Enabling plugins for local development</info>');
        self::echo(
            self::enablePlugins()
        );

        self::echo('<info>Disabling plugins for local development</info>');
        self::echo(
            self::disablePlugins()
        );
    }

    public static function removeWpContent(): void
    {
        self::echo('<info>Removing default plugins and themes</info>');

        // Look for any default plugins and themes
        $remove = [
            self::$rootPath . '/web/wp/wp-content/plugins',
            self::$rootPath . '/web/wp/wp-content/themes',
        ];

        $fs = new Filesystem();

        foreach ($remove as $path) {
            if (!$fs->exists($path)) {
                self::echo('path does not exist: ' . $path);
                continue;
            }

            self::echo('Removing ' . $path);
            try {
                $fs->remove($path);
            } catch (IOException $e) {
                self::echo('<error>' . $e->getMessage() . '</error>');
            }
        }

        self::echo('');
    }

    private static function echo(string $string): void
    {
        self::$event
            ->getIO()
            ->write($string)
        ;
    }

    private static function isNotLocal(): bool
    {
        return false === array_key_exists('WP_ENV', $_ENV)
            || false === array_key_exists('WP_HOME', $_ENV)
            || 'development' !== $_ENV['WP_ENV']
            || 1 !== preg_match('#^http://.+\.test:\d{4}$#', $_ENV['WP_HOME']);
    }

    private static function enablePlugins(): string
    {
        return self::togglePlugins(
            self::$locallyEnablePlugins,
            'LOCALLY_ENABLED_PLUGINS',
            'activate'
        );
    }

    private static function disablePlugins(): string
    {
        return self::togglePlugins(
            self::$locallyDisablePlugins,
            'LOCALLY_DISABLED_PLUGINS',
            'deactivate'
        );
    }

    private static function togglePlugins(
        array $pluginsToToggle,
        string $overrideEnvName,
        string $action
    ): string {
        $knownActions = [
            'activate',
            'deactivate',
        ];

        if (false === in_array($action, $knownActions, true)) {
            return '<error>Unknown action!</error>';
        }

        $plugins = $pluginsToToggle;
        if (array_key_exists($overrideEnvName, $_ENV)) {
            $plugins = explode(',', $_ENV[$overrideEnvName]);
        }

        $plugins = implode(' ', $plugins);

        $output = shell_exec('cd ' . self::$rootPath . ' && wp plugin ' . $action . ' ' . $plugins);

        return (string)$output;
    }
}
