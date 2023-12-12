<?php

return [
    /**
     * List of CUSTOM image sizes to register, each image size looks like:
     *  WP Defaults are thumbnail (150x150), medium(300,300) , large (1024,1024)
     *     [
     *         'name' => 'thumb',
     *         'width' => 100,
     *         'height' => 200,
     *         'crop' => true,
     *     ]
     */
    'sizes' => [

        [
            'name' => 'small',
            'width' => 300,
            'height' => 300,
            'crop' => false,
        ],

        [
            'name' => 'xlarge',
            'width' => 1680,
            'height' => 1050,
            'crop' => false,
        ],
        [
            'name' => 'xxlarge',
            'width' => 1920,
            'height' => 1200,
            'crop' => false,
        ],
    ],
];
