<?php

namespace App\Services;

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
