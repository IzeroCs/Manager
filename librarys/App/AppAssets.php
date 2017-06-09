<?php

    namespace Librarys\App;

    use Librarys\File\FileInfo;
    use Librarys\App\Config\AppAssetsConfig;
    use Librarys\Minify\Css as MinifyCss;
    use Librarys\Minify\Js as MinifyJs;

    final class AppAssets
    {

        private $pathDirectory;
        private $filename;
        private $config;
        private $buffer;

        const LOAD_CSS = 2;
        const LOAD_JS  = 4;

        const CACHE_CSS_MIME = 'css';
        const CACHE_JS_MIME  = 'js';

        public function __construct($pathDirectory, $filename)
        {
            $this->setPathDirectory($pathDirectory);
            $this->setFilename($filename);
        }

        public function setPathDirectory($pathDirectory)
        {
            $this->pathDirectory = $pathDirectory;
        }

        public function getPathDirectory()
        {
            return $this->pathDirectory;
        }

        public function setFilename($filename)
        {
            $this->filename = $filename;
        }

        public function getFilename()
        {
            return $this->filename;
        }

        public static function makeURLResourceTheme($themeDirectory, $filename)
        {
            global $boot;

            $filename = str_ireplace('.css', null, basename($filename));
            $buffer   = env('app.http.host') . '/asset.php';
            $buffer  .= '?' . ASSET_PARAMETER_THEME_URL        . '=' . $themeDirectory;
            $buffer  .= '&' . ASSET_PARAMETER_CSS_URL          . '=' . $filename;
            $buffer  .= '&' . $boot->getCFSRToken()->getName() . '=' . $boot->getCFSRToken()->getToken();

            if (env('app.dev.enable'))
                $buffer  .= '&' . ASSET_PARAMETER_RAND_URL . '=' . intval($_SERVER['REQUEST_TIME']);

            return $buffer;
        }

        public static function makeURLResourceJavascript($filename)
        {
            global $boot;

            $filename = str_ireplace('.js', null, basename($filename));
            $buffer  = env('app.http.host') . '/asset.php';
            $buffer .= '?' . ASSET_PARAMETER_JS_URL           . '=' . $filename;
            $buffer .= '&' . $boot->getCFSRToken()->getName() . '=' . $boot->getCFSRToken()->getToken();

            if (env('app.dev.enable'))
                $buffer  .= '&' . ASSET_PARAMETER_RAND_URL . '=' . intval($_SERVER['REQUEST_TIME']);

            return $buffer;
        }

        public static function makeURLResourceIcon($themeDirectory, $filename)
        {
            return env('app.http.theme') . '/' . $themeDirectory . '/icon/' . $filename;
        }

        public function loadCss()
        {
            return $this->load(self::LOAD_CSS);
        }

        public function loadJs()
        {
            return $this->load(self::LOAD_JS);
        }

        private function load($loadType)
        {
            global $boot, $appConfig;

            $filepath = FileInfo::filterPaths($this->pathDirectory . SP . $this->filename);

            if (FileInfo::isTypeFile($filepath) == false)
                return false;

            if ($this->loadCache($loadType, $filepath))
                return true;

            $envpath = null;

            if ($loadType === self::LOAD_CSS)
                $envpath = FileInfo::filterPaths($this->pathDirectory . SP . env('resource.filename.config.env_theme'));
            else if ($loadType === self::LOAD_JS)
                $envpath = FileInfo::filterPaths($this->pathDirectory . SP . env('resource.filename.config.env_javascript'));

            $this->buffer = FileInfo::fileReadContents($filepath);

            if (FileInfo::isTypeFile($envpath))
                $this->config = new AppAssetsConfig($boot, $envpath);

            if ($this->buffer == false)
                return false;

            if ($this->config !== null && preg_match_all('/(\$|\#)\[(.+?)\]/si', $this->buffer, $matches) != false) {
                foreach ($matches[2] AS $index => $env) {
                    $value = $this->config->get($env);

                    if (($value == null || empty($value) || is_string($value) == false) && $value !== '0')
                        die('Not value for key "' . $env);
                    else if ($matches[1][$index] === '#')
                        $this->buffer = str_replace($matches[0][$index], '#' . $value, $this->buffer);
                    else
                        $this->buffer = str_replace($matches[0][$index], $value, $this->buffer);
                }
            }

            $minify = null;

            if ($loadType === self::LOAD_CSS && $appConfig->get('cache.css.minify', true))
                $minify = new MinifyCss($this->buffer);
            else if ($loadType === self::LOAD_JS && $appConfig->get('cache.js.minify', true))
                $minify = new MinifyJs($this->buffer);

            if ($minify !== null)
                $this->buffer = $minify->minify();

            if ($this->loadCache($loadType, $filepath, true) && $this->buffer !== false)
                return true;

            return false;
        }

        private function loadCache($loadType, $filepath, $writeCache = false)
        {
            global $appConfig;

            $minify        = null;
            $cacheEnable   = true;
            $cacheLifetime = 86400;
            $cacheMime     = null;

            if ($loadType === self::LOAD_CSS) {
                header('Content-Type: text/css');

                $cacheEnable   = $appConfig->get('cache.css.enable',   true);
                $cacheLifetime = $appConfig->get('cache.css.lifetime', 86400);
                $cacheMime     = self::CACHE_CSS_MIME;
            } else if ($loadType === self::LOAD_JS) {
                header('Content-Type: text/javascript');

                $cacheEnable   = $appConfig->get('cache.js.enable',   true);
                $cacheLifetime = $appConfig->get('cache.js.lifetime', 86400);
                $cacheMime     = self::CACHE_JS_MIME;
            }

            if ($cacheEnable) {
                $timeNow              = time();
                $cacheDirectory       = env('app.path.cache');
                $cacheFilename        = md5($filepath);
                $cacheFilepath        = FileInfo::filterPaths($cacheDirectory . SP . $cacheFilename . '.' . $cacheMime);
                $cacheFiletime        = 0;
                $fileResourceTime     = 0;
                $cacheDirectoryExists = true;
                $isRemoveCache        = false;

                if (FileInfo::isTypeDirectory($cacheDirectory) == false && FileInfo::mkdir($cacheDirectory, true) == false)
                    $cacheDirectoryExists = false;

                if ($cacheDirectoryExists) {
                    if (FileInfo::isTypeFile($cacheFilepath))
                        $cacheFiletime = FileInfo::fileMTime($cacheFilepath);

                    if (FileInfo::isTypeFile($filepath))
                        $fileResourceTime = FileInfo::fileMTime($filepath);


                    if ($fileResourceTime >= $cacheFiletime || $timeNow - $cacheFiletime >= $cacheLifetime) {
                        if ($writeCache == false || FileInfo::fileWriteContents($cacheFilepath, $this->buffer) == false)
                            return false;

                        $isRemoveCache = true;
                    } else {
                        $this->buffer = FileInfo::fileReadContents($cacheFilepath);
                    }
                }
            } else if ($writeCache == false) {
                return false;
            }

            if ($isRemoveCache) {
                header('Cache-Control: private, max-age=0, no-cache, no-store, must-revalidate');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header('Pragma: no-cache');
                header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s ', time()) . 'GMT');
                header('Etag: "' . md5(time()) . '"');
            } else {
                header("Cache-Control: max-age={$cacheLifetime}, public");
                header('Date:    ' . gmdate('D, d M Y H:i:s ', time()) . 'GMT');
                header('Expires: ' . gmdate('D, d M Y H:i:s ', time() + $cacheLifetime) . 'GMT');
                header('ContentLength: ' . strlen($this->buffer));
            }

            return true;
        }

        public function display($isPrint = true)
        {
            if ($isPrint == false)
                return $this->buffer;

            echo $this->buffer;
            echo "\n\n";
        }

    }

?>