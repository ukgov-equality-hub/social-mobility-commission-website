<?php
/**
 * @author  Lukas Giegerich <lgiegerich@simplyzesty.com>
 * @version 12/11/2019
 */

namespace App\Services;

use App\Http\Lumberjack;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * Class ContextService
 *
 * Use this class to inject data into the global content.
 *
 * @package App\Services
 */
class ContextService
{
    /**
     * @param Lumberjack $lumberjack
     */
    public function load(Lumberjack $lumberjack): void
    {
        add_filter('timber/context', [$lumberjack, 'addToContext']); // add lumberjack to the global context
        add_filter('timber/context', [$this, 'acfOptionsPage']); // add ACF Thee Options Pages Values to context
        add_filter('get_twig', [$this,'fetchRecentPosts']);
    }

    /**
     * EXAMPLE - Adding something to the context
     * DELETE ME ONCE YOU UNDERSTAND ME
     *
     * @param array $context
     *
     * @return array
     */
    public function example(array $context): array
    {
        $context['example'] = 'Hello, Web!';

        return $context;
    }

    /**
     * Use Sparingly - best restricted to perhaps custom gutenberg blocks.
     * Actual COntrollers should use their own methods to fetch
     * correct post collections.
     *
     * @example {% set latestNewsPosts = fetchRecentPosts(0, $override) %}
     *
     * @param  Environment  $twig
     *
     * @return Environment
     */
    public function fetchRecentPosts(Environment $twig): Environment
    {
        $twig->addFunction( new TwigFunction('fetchRecentPosts', function($exclude = null, $override = []) {
            $count = 3 - $exclude;
            if ($count !== 0) {
                $args = [
                    'post_type' => array('post', 'blogs', 'speeches', 'press_releases'),
                    'posts_per_page' => $count,
                    //'post_status' => 'any',
                ];
                $query = new \WP_Query($args);
                $posts = $query->get_posts();

                if(!empty($override)){
                    $array = [];
                    $lengthO = count($override);
                    for($i = 1; $i <= $lengthO; $i++){
                        $array[$i] = $override[$i - 1]['post'][0];
                    }
                    $j = $i;
                    foreach($posts as $post){
                        $array[$j] = $post;
                        $j++;
                    }

                    $allPosts = $array;
                }else{
                    $allPosts = $posts;
                }

                return $allPosts;
            }else{
                $array = [];
                $lengthO = count($override);
                for($i = 1; $i <= $lengthO; $i++){
                    $array[$i] = $override[$i - 1]['post'][0];
                }
                return $array;
            }

            // NB - if Intuitive Custom Post Type Order plugin is used
            // The results are ordered as arranged in the Admin


        }
        ));

        return $twig;
    }

    /**
     * Make all values from the ACF Options pages available to twig
     *
     * @param array $context
     *
     * @return array
     */
    public function acfOptionsPage(array $context): array
    {
        /**
         * ACF Options page options can be retreived with get_fields('options')
         * To use a specific option, e.g. copyright_info in a template
         *
         * <footer>{{ options.copyright_info }}</footer>
         */

        $context['options'] = get_fields('options');

        return $context;
    }
}
