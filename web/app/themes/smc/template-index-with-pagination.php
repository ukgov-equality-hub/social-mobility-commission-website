<?php
/*
* Template Name: Index With Pagination Template
* Template Description: index listing of cpt posts with pagination.
*/


namespace App;

use App\PostTypes\People;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\PostQuery;
use Timber\Timber;

class TemplateIndexWithPaginationController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $page = new Page();

        $context['page'] = $page;
        global $paged;
        if (!isset($paged) || !$paged){
            $paged = 1;
        }
        $context['post']    = $page;
        $context['title']   = $page->title;
        $context['text'] =  $page->intro_text;
        $cpt = get_field('post_type');
        $context['fields'] = get_fields();

        $context['cpt'] = $this->getCPT($paged, $cpt);

        return new TimberResponse( 'templates/listing/index-with-pagination-listing.twig', $context);
    }

    public function getCPT($paged, $cpt)
    {
        $args = [
            'post_type' => $cpt,
            'paged' => $paged,
        ];

        $posts = new PostQuery($args, Post::class);

        return $posts;
    }
}
