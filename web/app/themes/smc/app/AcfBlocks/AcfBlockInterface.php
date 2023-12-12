<?php

namespace App\AcfBlocks;


interface AcfBlockInterface
{

    public function getBlockTitle(): string;

    public function getBlockDescription(): string;

    public function getBlockDefinition(): array;

    public static function acf_block_render_callback(
        array $block,
        string $content = '',
        bool $is_preview = false
    ): void;

}
