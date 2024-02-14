<?php
/*
* Template Name: Questionnaire Results Template
* Template Description: Questionnaire Results Page.
*/

namespace App;

use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\Timber;

class TemplateQuestionnaireResultsController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $page = new Page();

        $context['post'] = $page;
        $context['page'] = $page = new Page();
        $fields = get_fields();
        $context['fields'] = $fields;

        return new TimberResponse( 'templates/questionnaire-results.twig', $context);
    }


}
