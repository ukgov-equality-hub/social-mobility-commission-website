<?php
/**
 * Search results page
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\PostQuery;
use Timber\Timber;

class SearchController extends Controller
{
    public function handle()
    {
        $context     = Timber::get_context();
        $searchQuery = get_search_query();

        // do search and outout results
        global $paged;
        if (!isset($paged) || !$paged){
            $paged = 1;
        }

        $context['title'] = 'Search results for \'' . htmlspecialchars($searchQuery) . '\'';
            $args = [
                's' => $searchQuery,
                'paged' => $paged,
            ];

        $context['posts'] = new PostQuery($args, \Timber\Post::class);

        return new TimberResponse('templates/posts.twig', $context);
    }
}
