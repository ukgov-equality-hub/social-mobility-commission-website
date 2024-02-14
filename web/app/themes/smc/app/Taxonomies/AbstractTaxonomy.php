<?php

namespace App\Taxonomies;

use Rareloop\Lumberjack\Exceptions\PostTypeRegistrationException;

abstract class AbstractTaxonomy implements TaxonomyInterface
{

    public static function register(): void
    {
        $termType        = static::getTermType();
        $joinedPostTypes = static::getAssociatedPostTypes();
        $config          = static::getTaxonomyConfig();
        $config          = static::mergeLabels($config);

        if (empty($termType) || $termType === 'category' || $termType === 'tag') {
            throw new PostTypeRegistrationException('Custom Term type not set');
        }

        if (empty($config)) {
            throw new PostTypeRegistrationException('Config not set for custom term type');
        }

        if (empty($joinedPostTypes)) {
            throw new PostTypeRegistrationException('Associated Post Types undefined for Term');
        }

        register_taxonomy($termType, $joinedPostTypes, $config);
    }


    private static function mergeLabels($config): array
    {
        $defaultLabels = [
            'all_items'                  => __('All Items', 'app'),
            'parent_item'                => __('Parent Item', 'app'),
            'parent_item_colon'          => __('Parent Item:', 'app'),
            'new_item_name'              => __('New Item Name', 'app'),
            'add_new_item'               => __('Add New Item', 'app'),
            'edit_item'                  => __('Edit Item', 'app'),
            'update_item'                => __('Update Item', 'app'),
            'view_item'                  => __('View Item', 'app'),
            'separate_items_with_commas' => __('Separate items with commas', 'app'),
            'add_or_remove_items'        => __('Add or remove items', 'app'),
            'choose_from_most_used'      => __('Choose from the most used', 'app'),
            'popular_items'              => __('Popular Items', 'app'),
            'search_items'               => __('Search Items', 'app'),
            'not_found'                  => __('Not Found', 'app'),
            'no_terms'                   => __('No items', 'app'),
            'items_list'                 => __('Items list', 'app'),
            'items_list_navigation'      => __('Items list navigation', 'app'),
        ];

        $config['labels'] = array_merge($config['labels'], $defaultLabels);

        return $config;
    }

}
