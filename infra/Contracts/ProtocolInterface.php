<?php
namespace infra\contracts;

/**
 * 协议接口
 * @author mouyj
 * @note 输入转换调用时间在生成控制器方法参数前
 * @note 格式化调用时间在控制方法返回值不是标准响应接口时调用转换
 * @note 输出转换调用时间在控制器方法调用后拿到标准响应后
 */
interface ProtocolInterface
{
    /**
     * 解密
     * @param  string  $data data
     * @return mixed
     */
    public function convertInput(string $data);

    /**
     * 加密
     * @param  string  $data data
     * @return mixed
     */
    public function convertOutput(string $data);
}
