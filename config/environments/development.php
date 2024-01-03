<?php
/** Development */
define('SAVEQUERIES', true);
define('WP_DEBUG',  true);
define('WP_DEBUG_DISPLAY', true);
define('SCRIPT_DEBUG', true);
error_reporting( ~E_DEPRECATED);

// Helps with CLI commands and even composer installs.
if(!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = defined('DOMAIN_CURRENT_SITE') ? DOMAIN_CURRENT_SITE : 'localhost' ;
}
if(!isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = '/';
}
