<?php

namespace App\Services;

// Add a Use for each CPT that will be paged
// use App\PostTypes\Product;

use App\PostTypes\Product;

class TimberPaginationHelperService
{
    public function __construct()
    {
        // fix 404 on page 2 of timber search results
        add_action('pre_get_posts', [$this, 'timberPaginationQuery']);
    }

    public function timberPaginationQuery($query): void
    {
        // only fire this if it's the Main query - AND it's not an admin page
        if ($query->is_main_query() && !is_admin()) {
            // Add a block for each CPT to be paged
            // ---------------------------------------

            // Product search Page
            if (is_post_type_archive('zt_products')) {
                // If this is not set here the timber page results in a 404 for /page/2 +
                $query->set('posts_per_page', Product::$perpage); //ensure this static var exists!
            }
            // Add more custom post type clauses below here
        }
    }
}
