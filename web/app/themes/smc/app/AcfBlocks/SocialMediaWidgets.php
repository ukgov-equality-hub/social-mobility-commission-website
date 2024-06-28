<?php

namespace App\AcfBlocks;

use Timber\Timber;

/**
 * ----------------------------------------------------------
 * Location : Block value acf/zsty-social-media-widgets
 */
class SocialMediaWidgets extends AbstractAcfBlock
{
    // IF NOT ALREADY DONE SO _ READ THE CLASS COMMENTS ABOVE AND THE README.

    protected static string $template         = 'acf-blocks/social-media-widgets.twig';

    public string           $blockTitle       = 'Social Media Widgets';

    public string           $blockDescription = 'Block for the Social Media Widgets';

    public function getBlockDefinition(): array
    {
        // this is the array that is fed into acf_register_block() function

        return [
            // name must be namespaced eg zsty/
            // and it must start with a LETTER
            'name'            => 'zsty/social-media-widgets',
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
