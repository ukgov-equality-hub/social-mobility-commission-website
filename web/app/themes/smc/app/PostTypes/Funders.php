<?php
/**
 * Funders
 */

namespace App\PostTypes;

use App\Traits\AdminSearchableByMeta;
use Rareloop\Lumberjack\Post;

class Funders extends Post
{


    // By default the search in admin doesn't search by many fields
    // If you want to be able to search this Custom post Type
    // by one of it's ACF fields, then include this trait.

    // This trait supplies the common method `amendAdminSearchQuery`
    // which is detected & used by CustomPostTypesAdminService
    use AdminSearchableByMeta;

    /**
     * @var int
     */
    public static $perpage = 10;

    /**
     * Return the key used to register the post type with WordPress
     * First parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @return string
     */
    public static function getPostType(): string
    {
        // might be a good idea to "namespace prefix your post type
        // e.g. `acme_products` instead of `products`
        // this can prevent clashes if custom plugins installed at a later date use the same generic post type name
        return 'funders';
    }

    /**
     * Return the config to use to register the post type with WordPress
     * Second parameter of the `register_post_type` function:
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * See: `https://generatewp.com/post-type/` for a code generator
     *
     * @return array|null
     */
    protected static function getPostTypeConfig(): ?array
    {
        return [
            'labels'        => [
                'name'          => __('Funders'),
                'singular_name' => __('Funder'),
                'add_new_item'  => __('Add New Funder'),
            ],
            'public'        => true,
            //'taxonomies' => [],
            'supports'      => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes', 'excerpt'],
            'menu_position' => 6, // below Posts
            'menu_icon'     => 'dashicons-bank',
            'show_ui'       => true,
            'show_in_menu'  => true,
            'show_in_rest'  => true,
//            'has_archive'   => 'reports',
            //            'rewrite' => [
            //                'slug' => 'product',
            //                'with_front' =>  true,
            //                'pages' => true,
            //                'feeds' => true
            //
            //            ]
        ];
    }


    /**
     * OPTIONAL - write methods to add ACF fields as columns
     *            to the WordPress admin list page
     *
     * @param $self
     */
    public static function registerAdminColumns($self): void
    {
        // What columns do we want?
        add_filter('manage_zt_products_posts_columns', [$self, 'register_acf_columns']);

        // What will they show
        add_action('manage_zt_products_posts_custom_column', [$self, 'add_acf_fields_as_admin_columns'], 10, 2);

        // Are they sortable
        add_filter('manage_edit-zt_products_sortable_columns', [$self, 'register_acf_field_as_sortable_admin_column']);
    }


    // CUSTOM ACF FIELD REGISTRATION IN ADMIN

    /**
     * Register custom admin column names
     *
     * @param $columns
     *
     * @return array|string[]|void[]
     */
    public static function register_acf_columns($columns): array
    {
        // First unset date - we always want that last
        $date = $columns['date'];
        unset($columns['date']);

        // Register any other ACF fields we want to display in our Admin
        return array_merge($columns, array(
            // 'acf_fieldname_slug' => __ ( 'ACF Field Name' ),
            //  ... more ACF fields
            'date' => $date,
        ));
    }


    /**
     * Define custom column output
     * For the ACF Fields that have been registered
     * We need to tell WordPress what to show.
     *
     * @param $column
     * @param $post_id
     */
    public static function add_acf_fields_as_admin_columns($column, $post_id): void
    {
        // replace acf_fieldname_slug with the slug of your desired acf field(s)
        // you can have one or more "cases"
        switch ($column) {
            case 'acf_fieldname_slug':
                echo get_post_meta($post_id, '[ACF_FIELDNAME_SLUG]', true);
                break;
        }
    }


    /**
     * Register an existing column as sortable
     * you MUST  register the actual column first though
     *
     * @param $columns
     */
    public static function register_acf_field_as_sortable_admin_column($columns)
    {
        // replace slugs as required
        //$columns['acf_field_slug_for_custom_tax'] = 'zt_MY_CUSTOM_TAXONOMY_SLUG';
        //$columns['external_id'] = 'external_id';

        return $columns;
    }

    /**
     * Take the query and identify any selected Order By fields
     * Then define how that should work for the given field.
     *
     * @param $query
     */
    public static function customOrderby($query): void
    {
        $orderby = $query->get('orderby');

        switch ($orderby) {
            case 'ACF_FIELD_NAME_FOR_A_STORED_STRING_VALUE':
                $query->set('meta_key', 'ACF_FIELD_NAME');
                $query->set('orderby', 'meta_value');
                break;
            case 'ACF_FIELD_NAME_FOR_A_STORED_NUMERIC_VALUE':
                $query->set('meta_key', 'ACF_FIELD_NAME');
                $query->set('orderby', 'meta_value_num');
                break;
        }
    }

}
