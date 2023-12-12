<?php
/**
 * @author  Lukas Giegerich <lgiegerich@simplyzesty.com>
 * @version 14/12/2022
 */

namespace App\Services;


/**
 * Class RemovePostsSection
 *
 * @package App\Services
 *
 * https://wordpress.stackexchange.com/questions/293148/how-do-i-remove-the-default-post-type-from-the-admin-toolbar
 */
class RemovePostsSection
{
    public function remove(): void
    {
        add_action('admin_menu', [$this, 'remove_default_post_type']);
        add_action('admin_footer', [$this, 'remove_add_new_post_href_in_admin_bar']);
        add_action('init', [$this, 'remove_frontend_post_href']);
        add_action('wp_dashboard_setup', [$this, 'remove_draft_widget'], 999);
    }

    public function remove_default_post_type(): void
    {
        remove_menu_page('edit.php');
    }

    public function remove_add_new_post_href_in_admin_bar(): void
    {
        ?>
        <script type="text/javascript">
            function remove_add_new_post_href_in_admin_bar() {
                var add_new = document.getElementById('wp-admin-bar-new-content');
                if (!add_new) return;
                var add_new_a = add_new.getElementsByTagName('a')[0];
                if (add_new_a) add_new_a.setAttribute('href', '#!');
            }

            remove_add_new_post_href_in_admin_bar();
        </script>
        <?php
    }

    public function remove_frontend_post_href(): void
    {
        if (is_user_logged_in()) {
            add_action('wp_footer', [$this, 'remove_add_new_post_href_in_admin_bar']);
        }
    }

    public function remove_draft_widget(): void
    {
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    }
}
