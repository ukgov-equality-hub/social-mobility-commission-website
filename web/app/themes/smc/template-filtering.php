<?php
/*
* Template Name: Filter Template
* Template Description: filter template of CPT
*/


namespace App;

use App\PostTypes\People;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Page;
use Timber\PostQuery;
use Timber\Timber;

class TemplateFilteringController
{
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();

        $page = new Page();

        $context['page'] = $page;
        $context['fields'] = get_fields();
        global $paged;
        if (!isset($paged) || !$paged){
            $paged = 1;
        }

        $context['post']    = $page;
        $context['title']   = $page->title;
        $context['text'] =  $page->intro_text;
        $cpt = get_field('post_type');
        $taxs = get_object_taxonomies( $cpt );

        if(isset($_GET['checker'])) {
            $posts = $this->getFilteredPosts($paged, $cpt, $taxs);
        }else{
            $posts = $this->getAllPosts($paged, $cpt);
        }

        $context['postsOnThisPage'] = count($posts["posts"]);
        $min = 1 ;
        $pagedControl = $paged - 1;
        $control = (12 * $paged) ;
        if ($paged >= '2') {

            $min = $control - 11 ;
            if ($control > $posts['count']) {
                $control = $posts['count'];
            }
        }

        $context['min'] = $min;
        $context['max'] = $control;
        $context['paged'] = $paged;
        $context['posts'] = $posts;
        $filters = $this->getFilters($taxs);
        $context['filters'] = $filters;

        return new TimberResponse( 'templates/listing/filtering-listing.twig', $context);
    }

    public function getAllPosts($paged, $cpt)
    {
        $meta_query = [];
        $args = [
            'post_type' => $cpt,
            'paged' => $paged,
        ];
        $args2 = [
            'post_type' => $cpt,
            'posts_per_page' => -1
        ];

//order differently if custom post type is events
        if($cpt == "events"){

            $args['order'] = 'DESC';
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'event_date';
            $args['meta_type'] = 'DATETIME';

        }

        $args['meta_query'] = $meta_query;

        $allPosts  = new PostQuery($args, Post::class);
        $overallCount = count( new PostQuery($args2, Post::class));

        return [ "posts" => $allPosts ,  "count"=>$overallCount];

    }
    public function getFilters( $taxs)
    {
        $filters = [];
        foreach($taxs as $tax){
            $taxObj = get_taxonomy( $tax );
            $filters[] = ['title' => $taxObj->label,'slug' => $taxObj->name,'terms'=>get_terms( array(
                'taxonomy'   => $tax,
                'hide_empty' => false,
            ) )];
        }

        return $filters;
    }
    public function getFilteredPosts($paged, $cpt, $taxs)
    {
        $meta_query = [];
        $selectedArray = [];
        foreach($taxs as $tax){

            $args = [
                'post_type' => $cpt,
                'paged' => $paged,
            ];
            $args2 = [
                'post_type' => $cpt,
                'posts_per_page' => -1
            ];
            $meta_query = ['relation' => 'AND'];

            if(isset($_GET['filterOption'. $tax])) {


                $selected = $_GET['filterOption' . $tax];
                if($selected == "all"){

                }else {

                    $term1 = get_term($selected, $tax);
                    $tax = preg_replace('/-/', '_', $tax);
                    $selectedArray[] = $term1->term_id;
                    $value = [
                        [
                            'key' => $tax,
                            'value' => $selected,
                            'compare' => 'LIKE',
                        ],
                    ];
                    $meta_query[] = $value;
                }

            }


            $sort="";
            if(isset($_GET['sort'])){
                $sort = $_GET['sort'];
                if(str_contains($sort, "Date")) {
                    $sortDirection = preg_replace('/Date-/', '', $sort);
                    $typeofsort = "date";
                }else{
                    $sortDirection = preg_replace('/A-Z-/', '', $sort);
                    $typeofsort = "title";
                }
                if($cpt == "events") {

                    if($typeofsort == "title"){
                        $args['order'] = $sortDirection;
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = 'title';
                        $args['meta_type'] = 'DATETIME';
                    }
                    else{
                        $args['order'] = $sortDirection;
                        $args['orderby'] = 'meta_value_num';
                        $args['meta_key'] = 'event_date';
                        $args['meta_type'] = 'DATETIME';
                    }


                }else{
                    if($typeofsort == "title"){
                            $args['order'] = $sortDirection;
                            $args['orderby'] = 'meta_value_num';
                            $args['meta_key'] = 'title';
                            $args['meta_type'] = 'DATETIME';
                        }
                        else{
                            $args['order'] = $sortDirection;
                            $args['orderby'] = 'meta_value_num';
                            $args['meta_key'] = 'date';
                            $args['meta_type'] = 'DATETIME';
                        }
                }
            }
        }

        $args['meta_query'] = $meta_query;

        $allPosts  = new PostQuery($args, Post::class);
        $overallCount = count( new PostQuery($args2, Post::class));

        return [ "posts" => $allPosts , "selectedTerms" => $selectedArray, "selectedSort"=>$sort,  "count"=>$overallCount];
    }
}
