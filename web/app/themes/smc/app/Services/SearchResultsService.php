<?php

namespace App\Services;

/**
 * By default, WordPress simply uses the homepage/website root
 * As its search results page e.g mydomain.com/?s=search+phrase
 * This Serivce allows you to set a different page
 * such as /search
 *
 * Furthermore, it changes from the ?s= GET var to a slugified search phrase
 *
 * To use, simply uncomment the SearchResultsService binding in the
 * `AppServiceProvider:register` method
 * // $this->app->bind('SearchResultsService', 'App\Services\SearchResultsService');
 *
 * AND uncomment the instantiation in the `AppServiceProvider:boot` method
 * // $this->app->get('SearchResultsService'); // simply instantiate
 * You need do nothing else
 */
class SearchResultsService
{

    public function __construct()
    {
        add_action('template_redirect', [$this, 'zsty_change_search_url']);
    }


    /**
     * Change the search results page from WEBROOT/?s= to /search/search-query-terms
     */
    public function zsty_change_search_url(): void
    {
        if (!empty($_GET['s']) && is_search()) {
            // Set a custom URL for search results page here.
            wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
            exit();
        }
    }

}
