<?php

    use Librarys\File\FileInfo;
    use Librarys\App\AppDirectory;
    use Librarys\App\AppLocationPath;
    use Librarys\App\AppParameter;

    define('LOADED', 1);
    require_once('global.php');

    if ($appUser->isLogin() == false)
        $appAlert->danger(lng('login.alert.not_login'), ALERT_LOGIN, 'login.php');

    if ($appDirectory->getDirectory() == null || is_dir($appDirectory->getDirectory()) == false)
        $appAlert->danger(lng('home.alert.path_not_exists'), ALERT_INDEX, env('app.http.host'));
    else if ($appDirectory->isPermissionDenyPath($appDirectory->getDirectory()))
        $appAlert->danger(lng('home.alert.path_not_permission', 'path', $appDirectory->getDirectory()), ALERT_INDEX, env('app.http.host'));

    $appLocationPath = new AppLocationPath($appDirectory, 'index.php');
    $appLocationPath->setIsPrintLastEntry(true);
    $appLocationPath->setIsLinkLastEntry(true);

    $appParameter = new AppParameter();
    $appParameter->add(AppDirectory::PARAMETER_DIRECTORY_URL, $appDirectory->getDirectory(), true);
    $appParameter->add(AppDirectory::PARAMETER_PAGE_URL,      $appDirectory->getPage(),      $appDirectory->getPage() > 1);
    $appParameter->add(AppDirectory::PARAMETER_NAME_URL,      $appDirectory->getName(),      true);

    $fileInfo    = new FileInfo($appDirectory->getDirectory() . SP . $appDirectory->getName());
    $isDirectory = $fileInfo->isDirectory();

    if ($isDirectory)
        $title = lng('file_delete.title_page_directory');
    else
        $title = lng('file_delete.title_page_file');

    $themes  = [ env('resource.theme.file') ];
    $appAlert->setID(ALERT_FILE_DELETE);
    require_once('header.php');

    if (isset($_POST['delete'])) {
        if ($isDirectory) {
            $isHasFileAppPermission = false;

            if (FileInfo::rrmdir(FileInfo::validate($appDirectory->getDirectory() . SP . $appDirectory->getName()), null, $isHasFileAppPermission) && $isHasFileAppPermission == false) {
                $appParameter->remove(AppDirectory::PARAMETER_NAME_URL);
                $appAlert->success(lng('file_delete.alert.delete_directory_success', 'filename', $appDirectory->getName()), ALERT_INDEX, 'index.php' . $appParameter->toString(true));
            } else {
                if ($isHasFileAppPermission) {
                    $appParameter->remove(AppDirectory::PARAMETER_NAME_URL);
                    $appParameter->toString(true);

                    $appAlert->warning(lng('file_delete.alert.not_delete_file_app', 'filename', $appDirectory->getName()), ALERT_INDEX);
                    $appAlert->success(lng('file_delete.alert.delete_entry_in_directory_success', 'filename', $appDirectory->getName()), ALERT_INDEX, 'index.php' . $appParameter->toString());
                } else {
                    $appAlert->danger(lng('file_delete.alert.delete_directory_failed', 'filename', $appDirectory->getName()));
                }
            }
        } else {
            if (FileInfo::unlink(FileInfo::validate($appDirectory->getDirectory() . SP . $appDirectory->getName()))) {
                $appParameter->remove(AppDirectory::PARAMETER_NAME_URL);
                $appAlert->success(lng('file_delete.alert.delete_file_success', 'filename', $appDirectory->getName()), ALERT_INDEX, 'index.php' . $appParameter->toString(true));
            } else {
                $appAlert->danger(lng('file_delete.alert.delete_file_failed', 'filename', $appDirectory->getName()));
            }
        }
    }
?>

    <?php $appAlert->display(); ?>
    <?php $appLocationPath->display(); ?>

    <div class="form-action">
        <div class="title">
            <?php if ($isDirectory) { ?>
                <span><?php echo lng('file_delete.title_page_directory'); ?>: <?php echo $appDirectory->getName(); ?></span>
            <?php } else { ?>
                <span><?php echo lng('file_delete.title_page_file'); ?>: <?php echo $appDirectory->getName(); ?></span>
            <?php } ?>
        </div>
        <form action="file_delete.php<?php echo $appParameter->toString(); ?>" method="post">
            <input type="hidden" name="<?php echo $boot->getCFSRToken()->getName(); ?>" value="<?php echo $boot->getCFSRToken()->getToken(); ?>"/>

            <ul>
                <li class="accept">
                    <?php if ($isDirectory) { ?>
                        <span><?php echo lng('file_delete.form.accept_delete_directory', 'filename', $appDirectory->getName()); ?></span>
                    <?php } else { ?>
                        <span><?php echo lng('file_delete.form.accept_delete_file', 'filename', $appDirectory->getName()); ?></span>
                    <?php } ?>
                </li>
                <li class="button">
                    <button type="submit" name="delete">
                        <span><?php echo lng('file_delete.form.button.delete'); ?></span>
                    </button>
                    <a href="index.php<?php echo $appParameter->toString(); ?>">
                        <span><?php echo lng('file_delete.form.button.cancel'); ?></span>
                    </a>
                </li>
            </ul>
        </form>
    </div>

    <ul class="menu-action">
        <li>
            <a href="file_info.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-about"></span>
                <span><?php echo lng('file_info.menu_action.info'); ?></span>
            </a>
        </li>
        <?php if ($isDirectory == false) { ?>
            <li>
                <a href="download.php<?php echo $appParameter->toString(); ?>">
                    <span class="icomoon icon-download"></span>
                    <span><?php echo lng('file_info.menu_action.download'); ?></span>
                </a>
            </li>
        <?php } ?>
        <li>
            <a href="file_rename.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-edit"></span>
                <span><?php echo lng('file_info.menu_action.rename'); ?></span>
            </a>
        </li>
        <li>
            <a href="file_copy.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-copy"></span>
                <span><?php echo lng('file_info.menu_action.copy'); ?></span>
            </a>
        </li>
        <li>
            <a href="file_chmod.php<?php echo $appParameter->toString(); ?>">
                <span class="icomoon icon-key"></span>
                <span><?php echo lng('file_info.menu_action.chmod'); ?></span>
            </a>
        </li>
    </ul>

<?php require_once('footer.php'); ?>

<?php

    /*define('ACCESS', true);

    include_once 'function.php';

    if (IS_LOGIN) {
        $title = 'Xóa tập tin';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);
            $format = getFormat($name);

            if (isset($_POST['accept'])) {
                if (!@unlink($dir . '/' . $name))
                    echo '<div class="notice_failure">Xóa tập tin thất bại</div>';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else if (isset($_POST['not_accept'])) {
                goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            }

            echo '<div class="list">
                <span>' . printPath($dir . '/' . $name) . '</span><hr/>
                <span>Bạn có thực sự muốn xóa tập tin <strong class="file_name_delete">' . $name . '</strong> không?</span><hr/><br/>
                <center>
                    <form action="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                        <input type="submit" name="accept" value="Đồng ý"/>
                        <input type="submit" name="not_accept" value="Huỷ bỏ"/>
                    </form>
                </center>
            </div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/info.png"/> <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Thông tin</a></li>';

                if (isFormatText($name)) {
                    echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa văn bản</a></li>';
                    echo '<li><img src="icon/edit_text_line.png"/> <a href="edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa theo dòng</a></li>';
                } else if (in_array($format, $formats['zip'])) {
                    echo '<li><img src="icon/unzip.png"/> <a href="file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Xem</a></li>';
                    echo '<li><img src="icon/unzip.png"/> <a href="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Giải nén</a></li>';
                } else if (isFormatUnknown($name)) {
                    echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa dạng văn bản</a></li>';
                }

                echo '<li><img src="icon/download.png"/> <a href="file_download.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Tải về</a></li>
                <li><img src="icon/rename.png"/> <a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Đổi tên</a></li>
                <li><img src="icon/copy.png"/> <a href="file_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sao chép</a></li>
                <li><img src="icon/move.png"/> <a href="file_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Di chuyển</a></li>
                <li><img src="icon/access.png"/> <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
                <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
            </ul>';
        }

        include_once 'footer.php';
    } else {
        goURL('login.php');
    }*/

?>