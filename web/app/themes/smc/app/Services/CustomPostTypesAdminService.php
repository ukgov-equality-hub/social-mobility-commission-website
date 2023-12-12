<?php
/**
 * This service looks at your REGISTERED custom post types
 * It then checks to see if they have the methods
 * registerAdminColumns and/or amendAdminSearchQuery
 *
 * If so it will fire of those methods.
 *
 * This allows you to define your Custom post type in a PostType calls
 * and have that class decide how the WordPress Admin list page should look.
 *
 */

namespace App\Services;

use Rareloop\Lumberjack\Facades\Config;

class CustomPostTypesAdminService
{

    private $postTypes  = [];

    private $taxonomies = [];

    public function __construct(Config $config)
    {
        $all = $config::get('posttypes.register');
        $this->splitAllToPostandTax($all);
        //dump([$this->postTypes, $this->taxonomies]);
    }


    public function registerAdminColumns(): void
    {
        foreach ($this->postTypes as $postType) {
            //$postTypeClass = get_class($postType);
            if (method_exists($postType, 'registerAdminColumns')) {
                $postType::registerAdminColumns($postType);
            }
        }
    }


    // Admin End
    public function amendAdminSearchQuery(): void
    {
        foreach ($this->postTypes as $postType) {
            //$postTypeClass = get_class($postType);
            if (method_exists($postType, 'amendAdminSearchQuery')) {
                $postType::amendAdminSearchQuery($postType);
            }
        }
    }

    // Admin Order By ACf Fields
    public function amendOrderBy(): void
    {
        if (!is_admin()) {
            return;
        }

        foreach ($this->postTypes as $postType) {
            //$postTypeClass = get_class($postType);
            if (method_exists($postType, 'customOrderby')) {
                // get search query
                add_action('pre_get_posts', [$postType, 'customOrderby'], 99);
            }
        }
    }

    private function splitAllToPostandTax(array $allTypes = []): void
    {
        foreach ($allTypes as $type) {
            if (strpos($type, '\\PostTypes\\')) {
                $this->postTypes[] = $type;
            } elseif (strpos($type, '\\Taxonomies\\')) {
                $this->taxonomies[] = $type;
            }
        }
    }


}
