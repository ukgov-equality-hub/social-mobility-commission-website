<?php

namespace App\Services;

use function Env\env;

class AcfConfigurationService
{


    public function __construct()
    {
        // Does this Site Use Google Maps?  Ensure it has a Maps API KEY
        add_filter('acf/fields/google_map/api', [$this, 'configureAcfMaps']);
    }

    /**
     * Initialise an Options page with 3 default sub tabs
     */
    public function initOptionsPage(): void
    {
        if (function_exists('acf_add_options_page')) {
            // 4 Custom THeme Area Sections defined -
            // but feel free to move or remove sections as suits your site

            acf_add_options_page(
                [
                    'page_title' => 'Theme General Config',
                    'menu_title' => 'Theme Config',
                    'menu_slug'  => 'theme-general-settings',
                    'capability' => 'edit_posts',
                    'redirect'   => false,
                    'icon'       => 'dashicons-building',
                ]
            );

            acf_add_options_sub_page(
                [
                    'page_title'  => 'Theme Header Settings',
                    'menu_title'  => 'Header',
                    'parent_slug' => 'theme-general-settings',
                ]
            );

            acf_add_options_sub_page(
                [
                    'page_title'  => 'Theme Footer Settings',
                    'menu_title'  => 'Footer',
                    'parent_slug' => 'theme-general-settings',
                ]
            );

            acf_add_options_sub_page(
                [
                    'page_title'  => 'Social Profiles',
                    'menu_title'  => 'Social Profiles',
                    'parent_slug' => 'theme-general-settings',
                ]
            );
        }
    }


    public function configureAcfMaps($args)
    {
        $args['key'] = env('GOOGLE_MAPS_API_KEY') ?: '';

        return $args;
    }

}
