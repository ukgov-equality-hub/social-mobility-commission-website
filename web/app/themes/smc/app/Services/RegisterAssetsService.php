<?php

namespace App\Services;

class RegisterAssetsService
{
    private const CUSTOM_THEME_PATH = '/App_Themes/main';

    public function handle(): void
    {
        add_filter('wp_enqueue_scripts', function () {
            $this->registerHeadAssets();
            $this->registerFooterAssets();
        });

        add_filter('admin_enqueue_scripts', function () {
            $this->registerAdminAssets();
        });

        add_filter('login_enqueue_scripts', function () {
            $this->registerLoginScreenStyles();
        });
    }

    private function registerHeadAssets(): void
    {
        $this->registerStyle('maincss', self::CUSTOM_THEME_PATH . '/css/main.min.css');
        $this->registerStyle('stylecss', '/style.css');
        $this->registerStyle(
            'additional',
            self::CUSTOM_THEME_PATH . '/css/additional.css'
        );
    }

    private function registerFooterAssets(): void
    {

        $this->registerScript(
            'foundation-vendor',
            self::CUSTOM_THEME_PATH . '/foundation/js/vendor.min.js'
        );
        $this->registerScript(
            'foundation',
            self::CUSTOM_THEME_PATH . '/foundation/js/foundation.min.js',
            ['foundation-vendor']
        );
//        $this->registerScript(
//            'jquery-new',
//            self::CUSTOM_THEME_PATH . '/js/jquery.main.js'
//        );
        $this->registerScript(
            'main-bundle',
            self::CUSTOM_THEME_PATH . '/js/dist/main-bundle.js'
        );
//        $this->registerScript(
//            'mapping',
//            self::CUSTOM_THEME_PATH . '/js/dist/mapping.js'
//        );

    }

    private function registerAdminAssets(): void
    {
        $this->registerScript(
            'my-adminjs',
            self::CUSTOM_THEME_PATH . '/js/admin.min.js',
            ['jquery']
        );
    }

    private function registerLoginScreenStyles(): void
    {
        $this->registerStyle(
            'logincss',
            self::CUSTOM_THEME_PATH . '/css/login.min.css'
        );
    }

    private function registerStyle(
        string $handle,
        string $src,
        array $dependencies = [],
        string $media = 'all'
    ): void {
        $this->register('style', null, $media, $handle, $src, $dependencies);
    }

    private function registerScript(
        string $handle,
        string $src,
        array $dependencies = [],
        string $location = 'footer'
    ): void {
        $this->register('script', $location, null, $handle, $src, $dependencies);
    }

    private function register(
        string $type,
        ?string $location,
        ?string $media,
        string $handle,
        string $src,
        array $dependencies
    ): void {
        $src = get_stylesheet_directory_uri() . $src;

        if ($type === 'style') {
            $media = $media ?? 'all';

            wp_register_style($handle, $src, $dependencies, ASSETS_VERSION, $media);
            wp_enqueue_style($handle);
        }

        if ($type === 'script') {
            $inFooter = $location === 'footer';

            wp_register_script($handle, $src, $dependencies, ASSETS_VERSION, $inFooter);
            wp_enqueue_script($handle);
        }
    }
}
