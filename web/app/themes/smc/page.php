<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\Timber;
use function Env\env;

class PageController extends Controller
{
    public function handle()
    {
        $context = Timber::get_context();
        $page    = new Page();

        $context['post']    = $page;
        $context['page']    = $page;
        $context['title']   = $page->title;
        $context['content'] = $page->content;
        $fields = get_fields();
        $context['text'] = $fields['intro_text'];
        $context['fields'] = $fields;

        return new TimberResponse(env('DEFAULT_TEMPLATE'), $context);
    }
}
