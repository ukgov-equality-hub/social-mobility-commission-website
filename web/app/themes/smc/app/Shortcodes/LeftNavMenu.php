<?php

namespace App\Shortcodes;

use Timber\Post;
use Timber\Timber;

class LeftNavMenu implements ShortcodeInterface
{

    static $currentPostId;

    static $is_mobile;

    static $parents  = [];

    static $maxDepth = 3;

    static $template = './components/base/left-nav.html.twig';

    static function init(): void
    {
        add_shortcode('leftnav_menu', array(__CLASS__, 'handle_shortcode'));
        add_shortcode('leftnav_menu_mobile', array(__CLASS__, 'handle_shortcode'));
    }


    static function handle_shortcode($atts, $content = null, $tag = ''): void
    {
        // actual shortcode handling here
        self::$is_mobile = $tag === 'leftnav_menu_mobile';

        $current_id  = get_the_ID();
        $currentPost = new Post($current_id);
        $topParent   = $currentPost;


        self::$currentPostId = $current_id;

        // get parent IDs all the way up to the top level (i.e breadcrumbs)
        self::$parents = self::getParents($current_id);
        if ($currentPost->parent()) {
            $topParent = $currentPost->parent();
        }

        if (is_array(self::$parents) && !empty(self::$parents) && count(self::$parents) >= 2) {
            // go top?
            $topParent = self::$parents[count(self::$parents) - 1];
        }

        $menuItems = self::buildSideMenu($topParent);

        // render menu
        self::renderMenu($menuItems, $topParent);
    }


    /**
     * Get a list of parent posts
     * N.B. Top level is last in the list
     *
     * This is essentially a breadcrumb trail
     * Can be used to build a left menu for the site "section" or to decide how many levels bac to view
     *
     * @param $current_id
     *
     * @return array
     */
    static function getParents($current_id): array
    {
        $parent_id = wp_get_post_parent_id($current_id);
        $parents   = [];
        while ($parent_id > 0) {
            $parent    = new Post($parent_id);
            $parents[] = $parent;
            $parent_id = wp_get_post_parent_id($parent->ID);
        }

        return $parents;
    }


    /*
     * Recursively get child post to a max depth
     */
    static function getChildPosts(Post $post, int $depth = 3, int $level = 1): array
    {
        $children = $post->children($post->post_type);
        if ($depth > $level && is_iterable($children) && count($children) >= 1) {
            $level++;
            /** @var Post $child */
            foreach ($children as $child) {
                $child->children = self::getChildPosts($child, $depth, $level);
            }
        }

        return $children ?: [];
    }


    static function buildSideMenu(Post $topLevelPost): array
    {
        return self::getChildPosts($topLevelPost, self::$maxDepth);
    }

    static function renderMenu($items, $topParent): void
    {
        try {
            $menu = Timber::fetch(self::$template, [
                'menuItems'       => $items,
                'is_mobile'       => self::$is_mobile,
                'current_item_id' => self::$currentPostId,
                'topItem'         => $topParent,
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if (self::$is_mobile) {
            // Wrap any special adjustments for mobile menu containers
            $menu = sprintf(
                '<div class="grid-container show-for-small-only"><div class="grid-x grid-padding-x"><div class="small-12 cell">%s</div></div></div>',
                $menu
            );
        }

        echo $menu;
    }


}
