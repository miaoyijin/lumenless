<?php

namespace App\Http\Controllers;

use domains\home\services\IndexService;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * Class Controller
 * @package App\Http\Controllers
 * @property IndexService indexService
 */
class Controller extends BaseController
{
    use \infra\librarys\utils\AutoMake;

    protected $errorCode = -1;

    /**
     * @param array  $data data
     * @param string $message message
     * @param int    $code code
     * @return array
     */
    public static function success(array $data = [], string $message = "OK", int $code = 0)
    {
        return [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];
    }


    /**
     * @param string $message message
     * @param array  $data data
     * @param int    $code code
     * @param array  $traces traces
     * @return array
     */
    public static function error(string $message, array $data = [], int $code = -1, array $traces = [])
    {
        $json = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,

        ];
        if (config('env') != 'production') {
            $json['trace'] = $traces;
        }
        return $json;
    }

    /**
     * 默认控制器
     * @param Request $request request
     * @return string
     */
    public function index(Request $request)
    {
        return 'welcome';
    }
}
