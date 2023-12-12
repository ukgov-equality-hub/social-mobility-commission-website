<?php
/*
* Template Name: Index Toggle Template
* Template Description: Listing of cpt posts with toggles.
*/


namespace App;

use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\PostQuery;
use Timber\Timber;

class TemplateToggleController
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
        $context['fields'] = get_fields();
        $cpts= get_field('post_types');
        $context['cpts'] = $cpts;
        if(isset($_GET['selected'])) {
            $selected = $_GET['selected'];
            $context['selected'] = $selected;
            $context['posts'] = $this->getCPTSelected($paged, $cpts, $selected);
        }else{
            $context['posts'] = $this->getCPTs($paged, $cpts);
        }
        $context['links'] = get_field('links');

        return new TimberResponse( 'templates/listing/index-toggle.twig', $context);
    }

    public function getCPTSelected($paged, $cpts, $selected)
    {
        $all = [];
        foreach ($cpts as $cpt) {
            $all[] = $cpt['value'];
        }
        if($selected == "all"){
            $args['post_type'] = $all;
        }else{
            $args['post_type'] = $selected;
        }
        $args['paged'] = $paged;

        $posts = new PostQuery($args, Post::class);

        return $posts;
    }
    public function getCPTs($paged, $cpts)
    {
        $all = [];
        foreach ($cpts as $cpt) {
            $all[] = $cpt['value'];
        }
        $args['post_type'] = $all;
        $args['paged'] = $paged;

        $posts = new PostQuery($args, Post::class);

        return $posts;
    }
}
