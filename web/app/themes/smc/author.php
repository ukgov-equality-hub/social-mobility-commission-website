<?php
/**
 * The template for displaying Author Archive pages
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\RedirectResponse;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;
use Timber\User as TimberUser;

class AuthorController extends Controller
{
    public function handle()
    {
        // added to prevent account enumeration since most of our sites don't ever show author pages anyway
        return new RedirectResponse('/');

        global $wp_query;

        $data   = Timber::get_context();
        $author = new TimberUser($wp_query->query_vars['author']);

        $data['author'] = $author;
        $data['title']  = 'Author Archives: ' . $author->name();

        $data['posts'] = Post::query(
            [
                'author' => $author->ID,
            ]
        );

        return new TimberResponse('templates/posts.twig', $data);
    }
}
