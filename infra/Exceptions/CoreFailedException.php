<?php
namespace infra\exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * 致命错误需要记录日志
 * Created by PhpStorm.
 * User: mouyj
 * Date: 2020/4/28
 * Time: 22:35
 */
class CoreFailedException extends \Exception
{
    private $data;

    /**
     * HttpFailedException constructor.
     * @param array $data data
     * @param string $message message
     * @param int $code code
     * @param Throwable|null $previous previous
     */
    public function __construct(array $data, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * @return void
     */
    public function report()
    {
        Log::stack(['corefailed'])->error($this->message, $this->data);
    }
}
