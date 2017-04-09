<?php

    if (defined('LOADED') == false)
        exit;

    if (defined('SP') == false)
        define('SP', DIRECTORY_SEPARATOR);

    if (defined('ROOT') == false)
        define('ROOT', '');

    require_once(ROOT . 'librarys' . SP . 'Boot.php');

    $boot         = new Librarys\Boot(require_once(ROOT . 'assets' . SP . 'config' . SP . 'app.php'));
    $appChecker   = new Librarys\App\AppChecker   ($boot);
    $appConfig    = new Librarys\App\AppConfig    ($boot, ROOT . 'assets' . SP . 'config' . SP . 'manager.php');
    $appUser      = new Librarys\App\AppUser      ($boot, ROOT . 'assets' . SP . 'config' . SP . 'user.php');
    $appAlert     = new Librarys\App\AppAlert     ($boot, ROOT . 'assets' . SP . 'define' . SP . 'alert.php');
    $appDirectory = new Librarys\App\AppDirectory ($boot);

    if ($appChecker->execute()->isAccept() == false) {
        if ($appChecker->isInstallDirectory() == false)
            trigger_error('Bạn đang cài đặt ứng dụng trên thư mục gốc, hãy cài đặt vào một thư mục con.');
        else if ($appChecker->isDirectoryPermissionExecute() == false)
            trigger_error('Có vẻ host bạn cài ứng dụng không thể thực thi được, bạn vui lòng kiểm tra lại.');
        else if ($appChecker->isConfigValidate() == false)
            trigger_error('Cấu hình cho ứng dụng không tồn tại, bạn hãy xóa bỏ ứng dụng và cài lại thử xem.');
        else
            trigger_error('Không thể xác định lỗi, bạn hãy kiểm tra lại, hoặc cài lại ứng dụng');

        exit(0);
    }

    $appConfig->execute();
    $appUser->execute();

    if ($boot->getCFSRToken()->validatePost() !== true) {
        trigger_error('CFSR Token không hợp lệ');
        exit(0);
    }

    $appDirectory->execute();

?>
