<?php

    if (defined('LOADED') == false)
        exit;

    return [
        'name'      => 'Default dark',
        'directory' => 'default_dark',
        'http'      => env('app.http.theme') . '/default_dark',

        'color_background' => '303030',
        'color_header'     => '558b2f'
    ];

?>