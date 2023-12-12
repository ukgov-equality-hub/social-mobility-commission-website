<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\PostQuery;
use Timber\Timber;

class IndexController extends Controller
{
    public function handle()
    {
        $context          = Timber::get_context();

        // do search and outout results
        global $paged;
        if (!isset($paged) || !$paged){
            $paged = 1;
        }

        $args = [
            'post_type' => 'post',
            'posts_per_page' => 6,
            'order' => 'DESC',
            // 'orderby' => 'post_date_gmt', // optiona l choose an Order by field
            // 'ignore_custom_sort' => true, // if using IntuitiveSortOrder plugin and you still want order by date, unocmment this
            'paged' => $paged,
        ];

        $context['posts'] = new PostQuery($args, \Timber\Post::class); // use this line if you want pagination
        //$context['posts'] = Post::all(6,'menu_order','DESC'); // if you just want e.g. 6 latest post - use this line

        return new TimberResponse('templates/posts.twig', $context);
    }
}
