<?php
/**
 * The purpose of this service is to recursively locate parent pages
 * Options
 *   - Find Top Level Ancestor
 *   - Find Fist Ancestor with a specific property that is non-empty
 *
 */

namespace App\Services;

use Timber\Post;

class AncestorService
{

    static $currentPostId;

    /**
     * @var array of Posts not IDs
     */
    static $ancestors = [];

    static $maxDepth  = 3;


    public function fetchTopLevel($postID)
    {
        $currentPost = new Post($postID);
        $topAncestor = $currentPost; // start by assuming NO Ancestors.

        if ($currentPost->parent()) {
            // If this post has a parent - then that parent is the top ancestor until we chain them.
            $topAncestor = $currentPost->parent();
        }

        self::$ancestors = self::getAncestors($postID);
        if (is_array(self::$ancestors) && !empty(self::$ancestors) && count(self::$ancestors) >= 2) {
            // go top?
            $topAncestor = self::$ancestors[count(self::$ancestors) - 1];
        }

        return $topAncestor;
    }


    public function fetchFirstWithProperty($postID, $propertyName)
    {
        $currentPost     = new Post($postID);
        self::$ancestors = self::getAncestors($postID);
        if (!empty(self::$ancestors) && is_iterable(self::$ancestors)) {
            // loop through the array until we find one with the required property
            $ancestors = self::$ancestors;
            foreach ($ancestors as $ancestor) {
                if (property_exists($ancestor, $propertyName) && !empty($ancestor->{$propertyName})) {
                    return $ancestor;
                }
            }
        }

        // didn't find a matching post - return current post instead.
        return $currentPost;
    }

    /**
     * From a given starting post ID fetch all the direct ancestors
     *
     * @param $postID // the current Item
     *
     * @return array   // array of ancestors
     */
    public static function getAncestors($postID): array
    {
        $parent_id = wp_get_post_parent_id($postID);
        $ancestors = [];
        while ($parent_id > 0) {
            $parent      = new Post($parent_id);
            $ancestors[] = $parent;
            $parent_id   = wp_get_post_parent_id($parent->ID);
        }

        return $ancestors;
    }


}
