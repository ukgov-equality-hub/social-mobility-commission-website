<?php
// page-home.php

/*
 * Template Name: Home Template
 */

namespace App;

use Rareloop\Lumberjack\Page;
use Timber\Timber;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;

class PageHomeController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();
        $context['page'] = $page = new Page();
        $fields = get_fields();
        $context['fields'] = $fields;


        return new TimberResponse('templates/home.twig', $context);
    }

}
