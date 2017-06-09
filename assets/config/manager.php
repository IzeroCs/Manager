<?php

    if (defined('LOADED') == false)
        exit;

    return [
        'auto_redirect' => [
            'file_chmod' => true,
            'file_rename' => true,
            'create_directory' => true,
            'create_file' => true,
            'create_database' => true,
            'rename_database' => true,
        ],

        'enable_disable' => [
            'button_save_on_javascript' => true,
            'auto_focus_input_last' => true,
            'count_checkbox_file_javascript' => true,
            'count_checkbox_mysql_javascript' => true,
        ],

        'login' => [
            'enable_forgot_password' => false,
            'enable_lock_count_failed' => false,
            'max_lock_count' => 5,
            'time_lock' => 180,
            'time_login' => 604800,
        ],

        'paging' => [
            'file_edit_text' => 50,
            'file_edit_text_line' => 20,
            'file_home_list' => 50,
            'file_view_zip' => 0,
            'mysql_list_data' => 5,
        ],

        'cache' => [
            'css' => [
                'enable' => true,
                'minify' => true,
                'lifetime' => 31536000,
            ],

            'js' => [
                'enable' => true,
                'minify' => true,
                'lifetime' => 31536000,
            ],

        ],

        'tmp' => [
            'lifetime' => 180,
            'limit' => 20,
        ],

        'theme' => [
            'directory' => 'default',
        ],

        'check_update' => [
            'enable' => true,
            'time'   => 86400
        ],

        'language' => 'vi',


        'http_host' => 'http://izerocs.mobi/Manager',
    ];

?>