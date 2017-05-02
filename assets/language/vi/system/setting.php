<?php

    return [
        'title_page' => 'Cài đặt hệ thống',

        'form' => [
            'input' => [
                'paging_file_home_list'      => 'Phân trang danh sách tập tin:',
                'paging_file_view_zip'       => 'Phân trang xem tập tin zip:',
                'paging_file_edit_text'      => 'Phân trang sửa văn bản:',
                'paging_file_edit_text_line' => 'Phân trang sửa văn bản theo dòng:',

                'enable_disable_label'                     => 'Bật/Tắt',
                'enable_forgot_password'                   => 'Lấy lại mật khẩu',
                'enable_disable_button_save_on_javascript' => 'Phím Ctrl + S để lưu lại',

                'auto_redirect_label'                   => 'Chuyển hướng',
                'enable_auto_redirect_file_rename'      => 'Khi thay đổi tên tập tin, thư mục',
                'enable_auto_redirect_file_chmod'       => 'Khi phân quyền tập tin, thư mục',
                'enable_auto_redirect_create_directory' => 'Khi tạo thư mục',
                'enable_auto_redirect_create_file'      => 'Khi tạo tập tin',
                'enable_auto_redirect_create_database'  => 'Khi tạo cơ sở dữ liệu'
            ],

            'button' => [
                'save'   => 'Lưu lại',
                'cancel' => 'Quay lại'
            ],

            'placeholder' => [
                'input_paging_file_home_list'      => 'Nhập phân trang danh sách tập tin',
                'input_paging_file_view_zip'       => 'Nhập phân trang xem tập tin zip',
                'input_paging_file_edit_text'      => 'Nhập phân trang sửa văn bản',
                'input_paging_file_edit_text_line' => 'Nhập phân trang sửa văn bản theo dòng'
            ]
        ],

        'alert' => [
        	'tips'                 => 'Phân trang cài <strong>0</strong> sẽ không phân trang, ' .
                                      'bật tắt chuyển hướng cho tạo thư mục, tập tin, cơ sở dữ liệu thì khi tạo những mục này thành công sẽ tự động chuyển hướng tới mục vừa được tạo',
            'save_setting_failed'  => 'Lưu cài đặt thất bại',
            'save_setting_success' => 'Lưu cài đặt thành công'
        ],

        'menu_action' => [
            'setting_theme'   => 'Cài đặt giao diện',
            'setting_profile' => 'Cài đặt tài khoản',
            'manager_user'    => 'Quản lý người dùng'
        ]
    ];

?>