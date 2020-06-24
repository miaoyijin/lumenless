<?php

namespace domains\home\services;

use domains\BaseService;

class IndexService extends BaseService
{
    use \infra\librarys\utils\AutoMake;

    /**
     * @return bool
     */
    public function index()
    {
        return ['success'];
    }
}
