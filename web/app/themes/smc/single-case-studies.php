<?php

/**
 * The Template for displaying all single Case Studies
 */

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\PostQuery;
use Timber\Timber;
use function Env\env;

class SingleCaseStudiesController extends Controller
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
        $context['badges'] = $this->getBadges($fields);

        return new TimberResponse('templates/single/case-study.twig', $context);
    }
    public function getBadges($fields)
    {
        $badges = [];
        $taxs = [];
        if(is_iterable($fields['case_study_type'])){
            $taxs[] = ['id'=>$fields['case_study_type'][0],
                'tax'=>'case-study-type'];
        }
        if(is_iterable($fields['industry_area'])){
            $taxs[] = ['id'=>$fields['industry_area'][0],
                'tax'=>'industry-area'];
        }
        if(!empty($fields['location'])){
            $taxs[] = ['id'=>$fields['location'][0],
                'tax'=>'location'];
        }
        foreach($taxs as $tax) {
            if (!empty($tax)) {
                $badges[] = $this->loopField($tax);
            }
        }

        return $this->simplifyBadges($badges);

    }
    public function loopField($field)
    {
        $catcher = [];
        if(is_iterable($field)){
                $catcher[] = get_term($field['id'])->name;
            }

        return  $catcher;
    }
    public function simplifyBadges($badges)
    {
        $catcher = [];
        foreach($badges as $i){
            foreach($i as $badge){
                $catcher[] = $badge;
            }
        }
        return  $catcher;
    }
}
