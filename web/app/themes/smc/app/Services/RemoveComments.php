<?php
/**
 * @author  Lukas Giegerich <lgiegerich@simplyzesty.com>
 * @version 14/12/2022
 */

namespace App\Services;


/**
 * Class RemoveComments
 *
 * @package App\Services
 *
 * https://wordpress.stackexchange.com/questions/11222/is-there-any-way-to-remove-comments-function-and-section-totally
 */
class RemoveComments
{
    public function remove(): void
    {
        // Removes from admin menu
        add_action('admin_menu', [$this, 'my_remove_admin_menus']);

        // Removes from post and pages
        add_action('init', [$this, 'remove_comment_support'], 100);

        // Removes from admin bar
        add_action('wp_before_admin_bar_render', [$this, 'mytheme_admin_bar_render']);
    }

    public function my_remove_admin_menus(): void
    {
        remove_menu_page('edit-comments.php');
    }

    public function remove_comment_support(): void
    {
        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');
    }

    public function mytheme_admin_bar_render(): void
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }
}
