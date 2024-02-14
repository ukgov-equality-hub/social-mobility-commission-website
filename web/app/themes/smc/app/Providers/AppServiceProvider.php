<?php

namespace App\Providers;

use App\Services\AcfConfigurationService;
use App\Services\AcfCustomBlocksService;
use App\Services\AncestorService;
use App\Services\CustomPostTypesAdminService;
use App\Services\RegisterAssetsService;
use App\Services\RegisterShortcodesService;
use App\Services\TimberPaginationHelperService;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register Custom Services into the DI container
     * https://docs.lumberjack.rareloop.com/container/using-the-container#setting-entries-in-the-container
     */
    public function register(): void
    {
        //        ->bind('ServiceName', 'Fully\Qualified\Class');
        // register our custom ACF Theme Options Page(s)
        $this->app->bind('AcfConfigurationService', AcfConfigurationService::class);

        // Register a class that Enqueues our Stylesheet & JS Files
        $this->app->bind('RegisterAssetsService', RegisterAssetsService::class);

        // Uncomment/comment the next line to activate/deactive the custom search results page slug
        // $this->app->bind('SearchResultsService', 'App\Services\SearchResultsService');

        // Timber Pagination helpers Service to prevent /search/2 throwing a 404
        $this->app->bind('TimberPaginationHelperService', TimberPaginationHelperService::class);

        // CustomPostTypescustom columns in Admin
        $this->app->bind('CustomPostTypesAdminService', CustomPostTypesAdminService::class);

        // ACF Custom Blocks for Gutenberg Editor
        $this->app->bind('AcfCustomBlocksService', AcfCustomBlocksService::class);

        // The Register ShortCode Service registers a LeftNav builder short code for easy LeftNavs based on parental hierarchy
        // If you create your own custom short codes - Register them using this service.
        $this->app->bind('RegisterShortcodesService', RegisterShortcodesService::class);
        // The Ancestor service looks at parent IDs and builds arrays of children/ parents for LeftNav creation
        $this->app->bind('AncestorService', AncestorService::class);
    }

    /**
     * Perform any additional boot required for this application
     */
    public function boot(): void
    {
        // if it is to run in admin *and* front end contexts do it here
        $myAcfBlocks = $this->app->get('AcfCustomBlocksService');
        // ... more

        // Otherwise specifiy services for front/back end accordingly
        if (is_admin()) {
            // Check our CustomPostTypes and add admin columns or update the search query
            $cptAdmin = $this->app->get('CustomPostTypesAdminService');
            $cptAdmin->registerAdminColumns();
            $cptAdmin->amendAdminSearchQuery();
            $cptAdmin->amendOrderBy();
        } else {
            // front end only

            // Register any custom shortcode (including LeftNav builder)
            $this->app->get('RegisterShortcodesService');

            //  Uncomment next line if using custom search results page
            // $this->app->get('SearchResultsService'); // simply instantiate

            // TimberPagination has a habit of breaking on page 2
            // Add TimberPaginationHelpers filters for each archive type
            if ($this->app->has('TimberPaginationHelperService')) {
                $tphs = $this->app->get('TimberPaginationHelperService'); // simply instantiate if you want to use this
            }
        }
    }
}
