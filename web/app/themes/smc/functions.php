<?php

use App\Http\Lumberjack;
use App\Services\ContextService;
use \App\Library\ThemeFilters;
use App\Services\AcfConfigurationService;
use App\Services\RegisterAssetsService;
use App\Services\TwigService;
use Rareloop\Lumberjack\Facades\Config;

// Create the Application Container
$app = require('bootstrap/app.php');

// Bootstrap Lumberjack from the Container
$lumberjack = $app->make(Lumberjack::class);
$lumberjack->bootstrap();

// AFTER $lumberjack has been created you can get access
// to any of the Configuration values in [themefolder]/config
// e.g. to get the environment from the config/app.php file
$env = Config::get('app.environment');


// enqueue our own Assets
$assets = $app->get(RegisterAssetsService::class);
$assets->handle();

// Import our routes file
require_once('routes.php');

// add to global twig context using ContextService
$app->get(ContextService::class)->load($lumberjack);

// register twig extensions (extra functions and filters)
$app->get(TwigService::class)->load();

// Configure ACF Theme Options and Initialise Maps API Key
$app->get(AcfConfigurationService::class)->initOptionsPage();

// Use an external service for any Hooks/Filters
$themeFilters = new ThemeFilters(); //

// Load translation files from [theme-folder]/languages
load_theme_textdomain( 'app',  get_stylesheet_directory() . '/languages/' );

// remove comments
//$app->get(\App\Services\RemoveComments::class)->remove();

// remove the default posts - handy if you have multiple post types and want to treat them all the same in code
//$app->get(\App\Services\RemovePostsSection::class)->remove();

add_filter( 'wpcf7_autop_or_not', '__return_false' );
//add_filter( 'wpcf7_ajax_loader', '__return_false' );


register_block_pattern_category(
    'SMC',
    array( 'label' => __( 'SMC', 'social-mobility' ) )
);
