<?php

namespace App\Menu;

use Rareloop\Lumberjack\Post;
use Timber\Menu as TimberMenu;

class Menu extends TimberMenu
{
    public $MenuItemClass = Item::class;

    public $PostClass     = Post::class;

    // DO NOT DEFINE MENUS or MENU LOCATIONS HERE

    // List your menu locations in config/menus.php

}
