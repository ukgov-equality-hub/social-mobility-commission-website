<?php
/*
* Template Name: 20-80 Page
* Template Description: 20% Left Nav and Wide Main area
*/


namespace App;

use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\Timber;

class Template2080Controller
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $page = new Page();

        $context['page'] = $page;
        $context['post']    = $page;
        $context['title']   = $page->title;
        $context['content'] = $page->content;
        $fields = get_fields();
        $context['text'] = $fields['intro_text'];
        $context['fields'] = $fields;


        return new TimberResponse( ['templates/20-80.twig',getenv('DEFAULT_TEMPLATE')], $context);
    }
}
