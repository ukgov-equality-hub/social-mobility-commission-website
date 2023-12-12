<?php

namespace App\Traits;

/**
 * This trait allows searching custom post types
 * in the WP Admin area by their ACF fields.
 */
trait AdminSearchableByMeta
{

    // SEARCH Queries

    public static function amendAdminSearchQuery($self): void
    {
        // Join Meta Table(s)
        add_action('posts_join', [$self, 'admin_join']);
        // Amend the Where
        add_action('posts_where', [$self, 'admin_where']);
        // Make result set DISTINCT
        add_action('posts_distinct', [$self, 'admin_distinct']);
    }

    public static function admin_join($join)
    {
        global $pagenow, $wpdb;

        // I want the filter only when performing a search on the edit page of Custom Post Type named "$_GET['post_type']".
        if (true === self::isSearchingCustomPostTypeEditPage($pagenow)) {
            $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }

        return $join;
    }


    public static function admin_where($where)
    {
        global $pagenow, $wpdb;

        // I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
        if (true === self::isSearchingCustomPostTypeEditPage($pagenow)) {
            $where = preg_replace(
                "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*('[^']+')\s*\)/",
                "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)",
                $where
            );
            $where .= " GROUP BY {$wpdb->posts}.id"; // Solves duplicated results
        }

        return $where;
    }

    public static function isSearchingCustomPostTypeEditPage(
        string $pagenow
    ): bool {
        return isset($_GET['post_type'], $_GET['s'])
            && 'edit.php' === $pagenow
            && is_admin()
            && self::getPostType() === $_GET['post_type'];
    }

    public static function admin_distinct($where)
    {
        global $pagenow;

        if (true === self::isSearchingCustomPostTypeEditPage($pagenow)) {
            return "DISTINCT";
        }

        return $where;
    }

}
