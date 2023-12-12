<?php

/**
 * The Template for displaying all single posts
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;
use function Env\env;

class SingleController extends Controller
{
    /**
     * the handle() method is called by default by LumberJack's WordPressControllersServiceProvider
     * @return TimberResponse
     */
    public function handle()
    {
        $context = Timber::get_context();
        $post    = new Post();

        $context['post']    = $post;
        $context['page']    = $post;
        $context['title']   = $post->title;
        $context['content'] = $post->content;
        $fields = get_fields();
        $context['text'] = $fields['intro_text'];
        $context['fields'] = $fields;


        return new TimberResponse(env('DEFAULT_TEMPLATE'), $context);
    }
}
