<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use domains\home\services\HomeService;
use App\Http\Controllers\Controller as BaseController;

/**
 * @author mouyj
 */
class IndexController extends BaseController
{
    /**
     * @param Request $request request
     * @return array
     */
    public function index(Request $request)
    {
        return self::success($this->indexService->index());
    }
}
