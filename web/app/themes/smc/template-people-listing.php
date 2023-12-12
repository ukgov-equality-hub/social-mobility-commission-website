<?php
/*
* Template Name: People Listing Template
* Template Description: List of people cpt
*/


namespace App;

use App\PostTypes\People;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\PostQuery;
use Timber\Timber;

class TemplatePeopleListingController
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
        $context['people'] = $this->getPeople($paged);
        $context['fields'] = get_fields();

        return new TimberResponse( 'templates/listing/people-listing.twig', $context);
    }

    public function getPeople($paged)
    {
        $args = [
            'post_type' => 'people',
            'paged' => $paged,
        ];

        $people = new PostQuery($args, People::class);

        return $people;
    }
}
