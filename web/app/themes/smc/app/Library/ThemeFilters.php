<?php
/**
 * A centralised place to list all of
 * your custom filters and actions
 */

namespace App\Library;


class ThemeFilters
{

    /**
     * List out any  `add_filter()` here instead of in functions.php
     */
    public function __construct()
    {
        // SECURITY :: prevent user enumeration
        // we don't want ANY author sitemaps so return an empty array for this.
        add_filter('wpseo_sitemap_exclude_author', static function () {
            return [];
        });

        // force the yoast SEO boxes in the CMS to sit at the bottom of the page (after any custom ACF stuff)
        add_filter('wpseo_metabox_prio', static function () {
            return 'low';
        });

        //  prevent sitemaps for certain taxonomies
        add_filter('wpseo_sitemap_exclude_taxonomy', [$this, 'sitemap_exclude_taxonomy'], 10, 2);

        // prevent sitemaps for certain post types
        add_filter('wpseo_sitemap_exclude_post_type', [$this, 'sitemap_exclude_post_type'], 10, 2);

        // If creating custom gutenberg blocks,
        // need some custom CSS loaded for our custom Gutenberg blocks in Admin side
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(
                'gutenbergadminstyles',
                get_stylesheet_directory_uri() . '/assets/main/css/gutenberg-admin.css',
                '',
                ASSETS_VERSION,
                'screen'
            );
        });

        // Set ReplyTo and ReturnPaths for PHPMailer.
        // This overrides anything set in CMS to prevent mails form the server being marked as spam!
        // WPMailReturnPath is in this Library - ie same namespace.
        add_action('phpmailer_init', [WpMailReturnPath::class, 'wpMailReturnPathPhpMailerInit']);


        // Are you using a CDN for whole site caching? e.g. Varnish, Cloudfront etc
        // If so and you see you site gets stuck in infinite 302 redirect loops
        // uncomment below to disable WP's Canonical URL Redirect feature
        // remove_filter('template_redirect','redirect_canonical');
    }


    public function sitemap_exclude_taxonomy($excluded, $taxonomy): bool
    {
        $myExcluded = [
            // 'zty_custom_taxonomy_slug', // list the slugs of the taxonomies you don't want to have Sitemaps
        ];

        return !$excluded && in_array($taxonomy, $myExcluded, true);
    }


    public function sitemap_exclude_post_type($excluded, $post_type): bool
    {
        $myExcluded = [
            // 'zty_custom_taxonomy_slug', // list the slugs of the custom post types you don't want to have Sitemaps
        ];

        return !$excluded && in_array($post_type, $myExcluded, true);
    }

}
