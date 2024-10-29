<?php
// page-full.php

/*
 * Template Name: Full Width Template
 */

namespace App;

use Rareloop\Lumberjack\Page;
use Timber\Timber;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;

class PageFullController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $context['page'] = new Page();

        $fields = get_fields();
        $context['text'] = $fields['intro_text'];
        $context['fields'] = $fields;

        if ( post_password_required( $context['page']->ID ) ) {
            Timber::render( 'single-password.twig', $context );
            return new TimberResponse('templates/single-password.twig', $context);
        } else {
            return new TimberResponse('templates/full.twig', $context);
        }
    }
}
