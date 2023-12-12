<?php

use Rareloop\Lumberjack\Facades\Router;
use Zend\Diactoros\Response\HtmlResponse;

// Router::get('hello-world', function () {
//     return new HtmlResponse('<h1>Hello World!</h1>');
// });

// OR to register a method on a custom Http/Controllers Class Classname@MethodName
// Router::get('api/import', 'MyAPIController@import');


// Catch WP-JSON User enumeration attempts and redirect to homepage.
Router::group('wp-json/wp/v{vid}/users', function ($group) {
    $group->get('{uid}', function () {
        wp_redirect('/',307);
    })->where('uid','[0-9]+'); // `/prefix/route1`

    $group->get('{uparams}', function () {
        wp_redirect('/',307);
    })->where('uparams','\?*'); // `/prefix/route2`
});
