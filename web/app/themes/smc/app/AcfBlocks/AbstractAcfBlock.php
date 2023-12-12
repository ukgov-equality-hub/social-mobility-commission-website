<?php

namespace App\AcfBlocks;

use Timber\Timber;

abstract class AbstractAcfBlock implements AcfBlockInterface
{
    protected static string $template;

    public string           $blockTitle;

    public string           $blockDescription;

    /**
     *  This is the callback that displays the block.
     *
     * @param array  $block      The block settings and attributes.
     * @param string $content    The block content (defaults to empty string).
     * @param bool   $is_preview True during AJAX preview.
     */
    public static function acf_block_render_callback(
        array $block,
        string $content = '',
        bool $is_preview = false
    ): void {
        $context               = Timber::context();
        $context['block']      = $block;
        $context['fields']     = get_fields();
        $context['is_preview'] = $is_preview;

        // Render the block with twig -
        // NB. uses late static binding to access template defied by extending class
        // notes use of static:: rather than self::
        Timber::render(static::$template, $context);
    }

    protected static function getTemplate(): string
    {
        return self::$template;
    }

    public function getBlockTitle(): string
    {
        return $this->blockTitle;
    }

    public function getBlockDescription(): string
    {
        return $this->blockDescription;
    }

}
