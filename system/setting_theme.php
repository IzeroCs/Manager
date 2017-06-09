<?php

    use Librarys\App\AppUser;
    use Librarys\File\FileInfo;
    use Librarys\App\Config\AppAssetsConfig;

    define('LOADED', 1);
    require_once('global.php');

    $title  = lng('system.setting_theme.title_page');
    $themes = [ env('resource.filename.theme.setting') ];
    $appAlert->setID(ALERT_SYSTEM_SETTING_THEME);
    require_once(ROOT . 'incfiles' . SP . 'header.php');

    $directory = env('app.path.theme');
    $handle    = FileInfo::scanDirectory($directory);

    if ($handle === false)
        $appAlert->danger(lng('system.setting_theme.alert.error_list_theme'), ALERT_INDEX, env('app.http.host'));

    asort($handle);

?>

    <form action="#" method="post">
        <ul class="theme-list">
            <?php $countTheme = 0; ?>

            <?php foreach ($handle AS $index => $themeName) { ?>
                <?php if ($themeName != '.' && $themeName != '..') { ?>
                    <?php $themePath    = FileInfo::filterPaths($directory . SP . $themeName); ?>
                    <?php $themeEnvPath = FileInfo::filterPaths($themePath . SP . env('resource.filename.config.env_theme')); ?>

                    <?php if (FileInfo::isTypeDirectory($themePath) && FileInfo::isTypeFile($themeEnvPath)) { ?>
                        <?php $countTheme++; ?>
                        <?php $themeEnv  = new AppAssetsConfig($boot, $themeEnvPath); ?>

                        <li class="theme-entry">
                            <div class="theme-preview">
                                <div class="theme-header"     style="background-color: #<?php echo $themeEnv->get('color_header',     'ffffff'); ?>;"></div>
                                <div class="theme-background" style="background-color: #<?php echo $themeEnv->get('color_background', 'ffffff'); ?>;"></div>
                            </div>

                            <div class="theme-name">
                                <input type="radio" name="theme" value="<?php echo $themeName; ?>" id="theme-<?php echo $themeName; ?>"/>
                                <label for="theme-<?php echo $themeName; ?>">
                                    <span><?php echo $themeEnv->get('name'); ?></span>
                                </label>
                            </div>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </ul>

    <ul class="menu-action">
        <li>
            <a href="<?php echo env('app.http.host'); ?>/system/setting.php">
                <span class="icomoon icon-config"></span>
                <span><?php echo lng('system.setting.menu_action.setting_system'); ?></span>
            </a>
        </li>
        <li>
            <a href="<?php echo env('app.http.host'); ?>/user/setting.php">
                <span class="icomoon icon-config"></span>
                <span><?php echo lng('system.setting.menu_action.setting_profile'); ?></span>
            </a>
        </li>
        <li class="hidden">
            <a href="<?php echo env('app.http.host'); ?>/user/manager.php">
                <span class="icomoon icon-user"></span>
                <span><?php echo lng('system.setting.menu_action.manager_user'); ?></span>
            </a>
        </li>
    </ul>

<?php require_once(ROOT . 'incfiles' . SP . 'footer.php'); ?>