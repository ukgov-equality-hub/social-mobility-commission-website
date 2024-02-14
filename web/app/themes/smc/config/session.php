<?php
use function Env\env;
return [
    /**
     * The driver to use to store the session (defaults to `file`)
     */
    'driver' => env('SESSION_DRIVER') ?: 'file',

    /**
     * The name of the cookie that is set in a visitors browser
     */
    'cookie' => env('SESSION_COOKIE') ?: 'lumberjack_session',

    /**
     * How long the session will persist for
     */
    'lifetime' => env('SESSION_LIFETIME') ?: 120,

    /**
     * The URL path that will be considered valid for the cookie. Normally this is the
     * root of the domain but might need to be changed if serving WordPress from a sub-directory.
     */
    'path' => '/',

    /**
     * The domain that the cookie is valid for
     */
    'domain' => env('SESSION_DOMAIN') ?: null,

    /**
     * If true, the cookie will only be sent if the connection is done over HTTPS
     */
    'secure' => env('SESSION_SECURE_COOKIE') ?: false,

    /**
     * If true, JavaScript will not be able to access the cookie data
     */
    'http_only' => true,

    /**
     * Set a Same Site value
     * NB - this may not be picked up & used until later versions of LumberJack
     */
    'same_site' => getenv('SESSION_COOKIE_SAMESITE') ?: 'strict',


    /**
     * Should the session data be encrypted when stored?
     */
    'encrypt' => false,

    /**
     * When using the `file` driver this is the path to which session data is stored.
     * If none is specified the default PHP location will be used.
     */
    'files' => getenv('SESSION_FILEPATH') ?: false,
];
