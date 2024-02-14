<?php

namespace App\AcfBlocks;

use Timber\Timber;

/**

 * ----------------------------------------------------------
 * This is just an example - use in conjunction with example ACF group_6177f2d5476a6
 * to support this example - you;d need to add them
 * THis example was based on ACf Fields
 *  - heading_image : type image
 *  - heading : type text
 *  - block_text : type wysiwyg
 *  - image_repeater : type repeater
 *       "sub_fields"
 *           - side_image : type image
 *  - call_to_action_link : type Link
 *
 *
 * Location : Block value acf/zsty-latest-news-and-media
 */
class LatestNewsAndMediaBlock extends AbstractAcfBlock
{
    // IF NOT ALREADY DONE SO _ READ THE CLASS COMMENTS ABOVE AND THE README.

    protected static string $template         = 'acf-blocks/latest-news-and-media.twig';

    public string           $blockTitle       = 'Latest News And Media';

    public string           $blockDescription = 'Zesty content block, with 3 recent news and meia cards';

    public function getBlockDefinition(): array
    {
        // this is the array that is fed into acf_register_block() function

        return [
            // name must be namespaced eg zsty/
            // and it must start with a LETTER
            'name'            => 'zsty/latest-news-and-media',
            'title'           => __($this->getBlockTitle(), 'app'),
            'description'     => __($this->getBlockDescription(), 'app'),
            'render_callback' => [$this, 'acf_block_render_callback'],
            'category'        => 'text',
            // leave out the dashicons- prefix. autoappended. - use `layout` if in doubt
            'icon'            => 'layout',
            'keywords'        => ['Image', 'Text', 'layout'],
//            'enqueue_style'   => get_stylesheet_directory_uri() . '/App_Themes/blocks/img-left-text-right.css',
        ];
    }


}
