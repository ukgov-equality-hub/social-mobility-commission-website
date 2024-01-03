<?php
/**
 * @author  Lukas Giegerich <lgiegerich@simplyzesty.com>
 * @version 17/03/2023
 */

/**
 * Class Robots
 *
 */
class Robots
{
    private static string $xRobotsTag     = 'x-robots-tag: noindex, nofollow, nosnippet, noarchive';

    private static array  $noIndexDomains = [
        'zestydev',
        'staging.',
    ];

    public static function allowIndexing(): bool
    {
        if (false === defined('WP_ENV')) {
            return false;
        }

        if (WP_ENV !== 'production') {
            return false;
        }

        if (true === self::isNoIndexDomain()) {
            return false;
        }

        return true;
    }

    public static function getXRotbotsTag(): string
    {
        return self::$xRobotsTag;
    }

    private static function isNoIndexDomain(): bool
    {
        if (true === array_key_exists('HTTP_HOST', $_SERVER)
            && true === self::strContains(
                self::$noIndexDomains,
                $_SERVER['HTTP_HOST'],
            )
        ) {
            return true;
        }

        return false;
    }

    private static function strContains(
        array $needles,
        string $haystack
    ): bool {
        foreach ($needles as $needle) {
            if (true === str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}