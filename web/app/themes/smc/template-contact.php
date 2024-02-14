<?php
/*
* Template Name: Contact Us Template
* Template Description: contact us page.
*/

namespace App;

use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\Timber;

class TemplateContactController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $page = new Page();

        $context['post'] = $page;
        $context['page'] = $page = new Page();
        $fields = get_fields();
        $context['text'] = $fields['intro_text'];
        $context['fields'] = $fields;

        return new TimberResponse( 'templates/contact-us.twig', $context);
    }


}
