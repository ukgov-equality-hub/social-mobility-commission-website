<?php

namespace App\Http;

use Rareloop\Lumberjack\Facades\Config;
use Rareloop\Lumberjack\Http\Lumberjack as LumberjackCore;
use App\Menu\Menu;

class Lumberjack extends LumberjackCore
{
    public function addToContext($context)
    {
        $context['is_home']       = is_home();
        $context['is_front_page'] = is_front_page();
        $context['is_logged_in']  = is_user_logged_in();

        // In Timber, you can use TimberMenu() to make a standard WordPress menu available to the
        // Twig template as an object you can loop through. And once the menu becomes available to
        // the context, you can get items from it in a way that is a little smoother and more
        // versatile than WordPress's wp_nav_menu. (You need never again rely on a
        // crazy "Walker Function!")

        // Do not "define" new menu locations here - do it in config/menus.php

        // registered menu locations
        $registeredMenus = Config::get('menus.menus');
        if (!empty($registeredMenus) && is_iterable($registeredMenus)) {
            foreach ($registeredMenus as $menuSlug => $menuName) {
                $contextSafeSlug           = str_replace('-', '_', $menuSlug);
                $context[$contextSafeSlug] = new Menu($menuSlug);
            }
        }

        return $context;
    }
}
