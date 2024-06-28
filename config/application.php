<?php

/** @var string Directory containing all of the site's files */
$root_dir = dirname(__DIR__);

/** @var string Document Root */
$webroot_dir = $root_dir . '/web';

/**
 * Expose global env() function from oscarotero/env
 */
use Env\Env; // use Oscatero Env
use function Env\env;
// ENV settings -
//
// Env::USE_ENV_ARRAY To get the values from $_ENV, instead getenv(). < use this one for Dev
// Env::USE_SERVER_ARRAY To get the values from $_SERVER, instead getenv().
// Env::LOCAL_FIRST To get first the values of locally-set environment variables.
Env::$options = Env::LOCAL_FIRST | Env::USE_ENV_ARRAY;



/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = \Dotenv\Dotenv::createUnsafeImmutable($root_dir,'.env');
if (file_exists($root_dir . '/.env')) {
    $dotenv->load();
    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL']);
    }
} else {
  throw new Exception('Missing Environment Details');
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if(false === Robots::allowIndexing()){
    header(Robots::getXRotbotsTag(), false);
}

if (file_exists($env_config)) {
    require_once $env_config;
}


/*
 *  WordPress checks that the full URL matches the protocol/domain that it's expecting
 *  If not, it replies with a 302 Redirect to the canonical URL
 *
 *  We've set the canonical URL to be https://[dev.]socialmobility.independent-commission.uk
 *  CloudFront receives the request first
 *  We ask CloudFront to forward the Host header ([dev.]socialmobility.independent-commission.uk) ✅
 *  But CloudFront forwards the request to Elastic Beanstalk over HTTP, not HTTPS ❌
 *
 *  So, WordPress would usually issue a 302 redirect to https://...
 *  This results in a redirect loop
 *
 *  We use this code (in conjunction with a setting in elastic_beanstalk.tf) to tell WordPress that the user is connecting over HTTPS
 *  So, now it thinks the protocol is https:// ✅
 */
if (env('HTTPS') === "on") {
    $_SERVER["HTTPS"] = "on";
    $_SERVER["SERVER_PORT"] = "443";
}


/**
 * URLs
 */
define('WP_HOME', env('WP_HOME'));
define('WP_SITEURL', env('WP_SITEURL'));

/**
 * Custom Content Directory
 */
define('CONTENT_DIR', '/app');
define('WP_CONTENT_DIR', $webroot_dir . CONTENT_DIR);
define('WP_CONTENT_URL', WP_HOME . CONTENT_DIR);

/**
 * DB settings
 */
define('DB_NAME', env('DB_NAME'));
define('DB_USER', env('DB_USER'));
define('DB_PASSWORD', env('DB_PASSWORD'));
define('DB_HOST', env('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

/**
 * Authentication Unique Keys and Salts
 */
define('AUTH_KEY', env('AUTH_KEY'));
define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
define('NONCE_KEY', env('NONCE_KEY'));
define('AUTH_SALT', env('AUTH_SALT'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
define('NONCE_SALT', env('NONCE_SALT'));


if (!defined('ITSEC_ENCRYPTION_KEY') && !empty(env('ITSEC_ENCRYPTION_KEY'))) {
    define('ITSEC_ENCRYPTION_KEY', env('ITSEC_ENCRYPTION_KEY'));
}

/**
 * Advanced Custom Fields Pro licence key
 */
define('ACF_PRO_LICENSE', env('ACF_PRO_KEY'));


/**
 * SMTP settings - for 'WP Mail SMTP' plugin
 * From: https://wpmailsmtp.com/docs/how-to-secure-smtp-settings-by-using-constants/
 */
define( 'WPMS_ON', true ); // True turns on constants support and usage, false turns it off.
define( 'WPMS_LICENSE_KEY', '' );
define( 'WPMS_MAIL_FROM', env('WPMS_MAIL_FROM'));
define( 'WPMS_MAIL_FROM_FORCE', true ); // If True, the "From Email" setting above will be used for all emails, ignoring values set by other plugins
define( 'WPMS_MAIL_FROM_NAME', 'SMC Website Contact Form' );
define( 'WPMS_MAIL_FROM_NAME_FORCE', true ); // If True, the "From Name" setting above will be used for all emails, ignoring values set by other plugins
define( 'WPMS_SET_RETURN_PATH', true ); // Sets $phpmailer-&gt;Sender if true.

define( 'WPMS_DO_NOT_SEND', false ); // Possible values: true, false.

define( 'WPMS_SMTP_HOST', env('WPMS_SMTP_HOST') ); // The SMTP mail host.
define( 'WPMS_SMTP_PORT', 587 ); // The SMTP server port number.
define( 'WPMS_SSL', '' ); // Possible values '', 'ssl', 'tls' - note TLS is not STARTTLS.
define( 'WPMS_SMTP_AUTH', true ); // True turns it on, false turns it off.
define( 'WPMS_SMTP_USER', env('WPMS_SMTP_USER') ); // SMTP authentication username, only used if WPMS_SMTP_AUTH is true.
define( 'WPMS_SMTP_PASS', env('WPMS_SMTP_PASS') ); // SMTP authentication password, only used if WPMS_SMTP_AUTH is true.
define( 'WPMS_SMTP_AUTOTLS', true ); // True turns it on, false turns it off.
define( 'WPMS_MAILER', 'smtp' );


/**
 * Custom Settings
 */
define('AUTOMATIC_UPDATER_DISABLED', true);
define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);
if(!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}


/**
 * Is this MultiSite?
 * If so uncomment and set appropriate values for the below
 */
//define('WP_ALLOW_MULTISITE', true);
//define('MULTISITE', true);
//define('SUBDOMAIN_INSTALL', false); // obv switch this to true if you're using subdomains
//define('DOMAIN_CURRENT_SITE', env('DOMAIN_CURRENT_SITE') ?: parse_url(WP_HOME,  PHP_URL_HOST));
//define('PATH_CURRENT_SITE', '/');
//define('SITE_ID_CURRENT_SITE', 1);
//define('BLOG_ID_CURRENT_SITE', 1);

// // Prevents mysterious redirects to wp-signup.php?new=maindomain  :: https://gist.github.com/dejanmarkovic/8323792
// // If Domain Current Site is set properly this shouldn't be a problem anyway
//define('NOBLOGREDIRECT', 'https://maindomain');
//
//define('COOKIE_DOMAIN',   parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_HOST) );
//define('COOKIEPATH', '/' );
//define('SITECOOKIEPATH', $_SERVER['HTTP_HOST'] . '/' );
//define('ADMIN_COOKIE_PATH', SITECOOKIEPATH .'wp/wp-admin');


/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}

define('ROOT_DIR', $root_dir);
define('WEBROOT_DIR', $webroot_dir);
define('CACHE_DIR', (ROOT_DIR . '/var/cache') );
define('VENDOR_DIR', (ROOT_DIR.'/vendor'));
define('VENDOR_FORM_DIR', VENDOR_DIR . '/symfony/form');
define('VENDOR_VALIDATOR_DIR', VENDOR_DIR . '/symfony/validator');
define('VENDOR_TWIG_BRIDGE_DIR', VENDOR_DIR . '/symfony/twig-bridge');
define('RESOURCES_DIR', ( __DIR__ . '/../templates') );
define('DEFAULT_FORM_THEME', 'foundation_5_layout.html.twig');

/**
 * Assets Cachebusting
 */
if ( (defined('WP_DEBUG') && WP_DEBUG) || WP_ENV === 'development') {
    define('ASSETS_VERSION', time());
} else {
    // the TIMESTAMP_VERSION placeholder text is automatically amended on deploy
    // see deploy.rb:60  `task :assets_version`
    define('ASSETS_VERSION', 'TIMESTAMP_HERE');
}

// WP-CLI Vulnerability Reports
define( 'VULN_API_TOKEN', env('VULN_API_TOKEN') );

define('MAIL_RETURN_PATH_AND_REPLY_TO', env('MAIL_RETURN_PATH_AND_REPLY_TO'));