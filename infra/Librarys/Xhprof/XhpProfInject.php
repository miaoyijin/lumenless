<?php
namespace App\Library\Xhprof\Lib;

use App\Services\EnvironmentService;
use infra\Librarys\Utils\Functions;

class XhpProfInject {

    public static function start()
    {
        if (!(env('APP_DEBUG') && Functions::isDev())) {
            return;
        }
        if (request('xhprof', '') == 'true' && extension_loaded('xhprof')) {
            $dir = dirname(__FILE__);
            require_once $dir . '/Xhprof/xhprof_lib/utils/xhprof_lib.php';
            require_once $dir . '/Xhprof/xhprof_lib/utils/xhprof_runs.php';
            xhprof_enable(XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU);
        }
    }

    public static function end()
    {
        if (!env('APP_DEBUG') || Functions::isDev()) {
            return;
        }
        if (request('xhprof', '') == 'true' && extension_loaded('xhprof')) {
            $dir = dirname(__FILE__);
            require_once $dir . '/Xhprof/xhprof_lib/utils/xhprof_lib.php';
            require_once $dir . '/Xhprof/xhprof_lib/utils/xhprof_runs.php';
            xhprof_enable(XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU);
            $xhprofData = xhprof_disable();
            $xhprofRuns = new \XHProfRuns_Default();
            $runId = $xhprofRuns->save_run($xhprofData, 'xhprof_test');
            return self::getUrl() . '/api/xhprof?run=' . $runId . '&source=xhprof_test&all=1';
        }
    }

    public static function laodData()
    {
        if (!env('APP_DEBUG') || Functions::isDev()) {
            return;
        }
        $dir = dirname(__FILE__);
        require_once $dir . '/Xhprof/xhprof_html/index.php';
    }

    /**
     * 获取请求域名
     * @return string
     */
    public static function getUrl()
    {
        $url = 'http://';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://';
        }
        if($_SERVER['SERVER_PORT'] != '80') {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
        } else {
            $url .= $_SERVER['SERVER_NAME'];
        }
        return $url;
    }
}