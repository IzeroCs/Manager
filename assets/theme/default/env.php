<?php

    if (defined('LOADED') == false)
        exit;

    return [
        'name'      => 'Default',
        'directory' => 'default',
        'http'      => env('app.http.theme') . '/default',

        'color_background' => 'ffffff',
        'color_header'     => '00897b'
    ];

?>