<?php

namespace App\Services;


use App\Shortcodes\LeftNavMenu;
use App\Shortcodes\ShortcodeInterface;

class RegisterShortcodesService
{
    public $shortcodes = [];

    public function __construct()
    {
        // Add 'AsideMenu Shortcode'
        $this->shortcodes = [
            LeftNavMenu::class,
        ];


        // now intialise each
        try {
            // register custom blocks
            $this->initShortcodes();
            // uncomment to restrict this site to our own custom blocks
            // add_filter( 'allowed_block_types_all', [$this,'restrictToOurBlocks'], 10, 2 );
        } catch (\Exception $e) {
            // do nothing for now.
        }
    }


    public function initShortcodes(): void
    {
        if (!is_iterable($this->shortcodes) || empty($this->shortcodes)) {
            throw new \Exception('No Shortcodes to register');
        }

        /** @var ShortCodeInterface $block */
        foreach ($this->shortcodes as $shortcode) {
            $shortcode::init();
        }
    }

}
