<?php

namespace App\AcfBlocks;

use Timber\Timber;

/**
 * ----------------------------------------------------------
 * Location : Block value acf/zsty-childs-play-game
 */
class ChildsPlayGameBlock extends AbstractAcfBlock
{
    // IF NOT ALREADY DONE SO _ READ THE CLASS COMMENTS ABOVE AND THE README.

    protected static string $template         = 'acf-blocks/childs-play-game.twig';

    public string           $blockTitle       = 'Childs Play Game';

    public string           $blockDescription = 'Block for the Childs Play Game';

    public function getBlockDefinition(): array
    {
        // this is the array that is fed into acf_register_block() function

        return [
            // name must be namespaced eg zsty/
            // and it must start with a LETTER
            'name'            => 'zsty/childs-play-game',
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
