<?php
/**
 * @author  Lukas Giegerich <lgiegerich@simplyzesty.com>
 * @version 24/04/2023
 */

namespace App\Services;


use HelloNico\Twig\DumpExtension;
use Timber\Image;
use Timber\ImageHelper;
use Timber\Twig_Function;
use Timber\Twig_Filter;
use Timber\URLHelper;
use Twig\Environment;
use Twig\TwigFunction;

use function Env\env;

/**
 * Class TwigService
 *
 * @package App\Services
 */
class TwigService
{
    public function load(): void
    {
        add_filter('get_twig', [$this, 'addDumpExtension']); // register dump extension for local dev only
        add_filter('get_twig', [$this, 'addEnvFunction']);
        add_filter('get_twig', [$this, 'currentURL']);
        add_filter('get_twig', [$this, 'getTemplateName']);
        add_filter('get_twig', [$this, 'breadcrumbs']);
        add_filter('get_twig', [$this, 'fetchPosts']);
        add_filter('get_twig', [$this, 'lpad']);
        add_filter('get_twig', [$this, 'fallbackImage']);
        add_filter('get_twig', [$this, 'alt']);
        add_filter('get_twig', [$this, 'safeTowebp']);
    }

    /**
     * Register the {{ dump($obj) }} capability to twig
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function addDumpExtension(Environment $twig): Environment
    {
        //only use var_dumper in dev. Extension is a composer require-dev extension
        if (WP_ENV === 'development') {
            $twig->addExtension(new DumpExtension()); //get symfony var dumper formatting in dev
        }

        return $twig;
    }

    /**
     * make {{ env('ENV_VAR_NAME_HERE')  }} work in twig
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function addEnvFunction(Environment $twig): Environment
    {
        //only use var_dumper in dev. Extension is a composer require-dev extension
        $twig->addFunction(
            new Twig_Function('env', function ($key) {
                return env($key);
            })
        ); //get symfony var dumper formatting in dev

        return $twig;
    }

    /**
     * Simple twig custom function to detect the current URL
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function currentUrl(Environment $twig): Environment
    {
        //only use var_dumper in dev. Extension is a composer require-dev extension
        $twig->addFunction(
            new Twig_Function('get_current_url', function () {
                return URLHelper::get_current_url();
            })
        ); //get symfony var dumper formatting in dev

        return $twig;
    }

    /**
     * Gets the name of the template
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function getTemplateName(Environment $twig): Environment
    {
        $twig->addFunction(
            new Twig_Function('getTemplateName', function () {
                global $template;
                $pathinfo = pathinfo($template);

                return $pathinfo['filename'];
            })
        );

        return $twig;
    }


    /**
     * @param Environment $twig
     *
     * @return Environment
     */
    public function breadcrumbs(Environment $twig): Environment
    {
        // If you need to hijack any breadcrumbs or correct any incorrect URLs
        // add_filter('wpseo_breadcrumb_links', function($links) {
        //   check here
        //   $custom_breadcrumb = [
        //      [
        //        'url' => 'home_url('/custom-path/'),
        //        'text' => 'My Thing'
        //      ]
        //   ];
        //   array_splice($links, 1,-1, $custom_breadcrumb);
        //   return $links
        //}


        if (!function_exists('yoast_breadcrumb')) {
            // just return null if Yoast is not installed or activated.
            $twig->addFunction(
                new TwigFunction('breadcrumbs', function () {
                    return null;
                })
            );

            return $twig;
        }

        // this make {{breadcrumb }} output the standard Yoast Breadcrumb trail.
        $twig->addFunction(
            new TwigFunction('breadcrumbs', function () {
                return yoast_breadcrumb('<ul class="breadcrumbs yseo "><li>', '</li></ul>', false);
            }
            )
        );

        return $twig;
    }


    /**
     * Use Sparingly - best restricted to perhaps custom gutenberg blocks.
     * Actual Controllers should use their own methods to fetch
     * correct post collections.
     *
     * @param Environment $twig
     *
     * @return Environment
     * @example {% set latestNewsPosts = fetchPosts('post',fields.news_by_tag|default(), 12) %}
     *
     */
    public function fetchPosts(Environment $twig): Environment
    {
        $twig->addFunction(
            new TwigFunction('fetchPosts', function ($postType = 'post', $tag = [], $limit = -1) {
                $args = [
                    'post_type'      => $postType,
                    'posts_per_page' => $limit ?: 6,
                    //'post_status' => 'any',
                ];

                if (!empty(($tag))) {
                    $args['tag__in'] = implode(', ', $tag);
                }

                // NB - if Intuitive Custom Post Type Order plugin is used
                // The results are ordered as arranged in the Admin
                $query = new \WP_Query($args);

                return $query->get_posts();
            }
            )
        );

        return $twig;
    }

    /**
     * Wrapper for Left Pad
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function lpad(Environment $twig): Environment
    {
        $twig->addFilter(
            new Twig_Filter('lpad', function ($str = '', $iLength = 1, $strPad = '.') {
                return str_pad($str, $iLength, $strPad, STR_PAD_LEFT);
            }
            )
        );

        return $twig;
    }

    /**
     * Adds a filter for injecting a fallback image source:
     * 'string' | fallback
     *
     * @param Environment $twig
     *
     * @return Environment
     */
    public function fallbackImage(Environment $twig): Environment
    {
        $fn = static function (?string $imageSrc): ?string {
            if (false === empty($imageSrc)) {
                return $imageSrc;
            }

            return env('FALLBACK_IMAGE'); // will return null if the env isn't set
        };

        $twig->addFilter(
            new Twig_Filter('fallback', $fn)
        );

        return $twig;
    }

    public function alt(Environment $twig): Environment
    {
        $fn = static function (
            null|string|array|Image $image,
            ?string $fallback = null
        ): string {
            // make sure fallback is a string
            $fallback = $fallback ?? '';

            // use fallback if no image was provided
            if (null === $image) {
                return $fallback;
            }

            // if a string is passed, turn it into a compatible array structure for processing
            if (true === is_string($image)) {
                $image = [
                    'filename' => $image,
                ];
            }

            // if a Timber/Image is passed, turn it into a compatible array structure for processing
            if ($image instanceof Image) {
                $image = [
                    'alt'      => $image->alt(),
                    'title'    => $image->title(),
                    'filename' => $image->file,
                ];
            }

            // if an alt has been provided, we can return it
            if (false === empty($image['alt'])) {
                return $image['alt'];
            }

            // if a title has been provided, we can return it
            if (false === empty($image['title'])) {
                return $image['title'];
            }

            // parse the filename into something human-readable

            $fileName = pathinfo($image['filename'])['basename'];

            $withoutExt = preg_replace('#\.[^.\s]{3,4}$#', '', $fileName);

            $withoutDashes = preg_replace('#[-_]#', ' ', $withoutExt);

            $splitCamelCase = implode(' ', preg_split('/(?=[A-Z][a-z])/', $withoutDashes));

            $imageAlt = $splitCamelCase;

            return $imageAlt ?: $fallback;
        };

        $twig->addFilter(
            new Twig_Filter('alt', $fn)
        );

        return $twig;
    }

    public function safeTowebp(Environment $twig): Environment
    {
        $fn = static function (
            ?string $image,
        ): string {
            if (null === $image) {
                return '';
            }

            try {
                $image = ImageHelper::img_to_webp($image);
            } catch (Exception $e) {
            }

            return $image;
        };

        $twig->addFilter(
            new Twig_Filter(
                'safeTowebp',
                $fn
            )
        );

        return $twig;
    }
}
