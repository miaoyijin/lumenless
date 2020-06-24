<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use infra\librarys\utils\Functions;
use infra\contracts\ProtocolInterface;
use infra\librarys\protocoll\RsaProtocoll;
use App\Http\Controllers\Controller as BaseController;

/**
 * 工具
 * @author yangd
 */
class ToolsController extends BaseController
{

    /**
     * 公钥解密工具
     * @param Request $request request
     * @return array
     */
    public function rsaPKeyDecode(Request $request)
    {
        if (!Functions::isDev()) {
            return '404';
        }
        $raw = $request->getContent();
        /**
         * @var RsaProtocoll  $protocol
         */
        $protocol = app(ProtocolInterface::class);
        return $requestData = $protocol->convertInputPKeyTest($raw);
    }

    /**
     * 私钥解密工具
     * @param Request $request request
     * @return array
     */
    public function rsaiKeyDecode(Request $request)
    {
        if (!Functions::isDev()) {
            return '404';
        }
        $raw = $request->getContent();
        /**
         * @var RsaProtocoll  $protocol
         */
        $protocol = app(ProtocolInterface::class);
        return $requestData = $protocol->convertInputIKeyTest($raw);
    }
}
