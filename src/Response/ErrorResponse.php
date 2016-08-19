<?php
/**
 * ErrorResponse.php
 * @author  lmh <lmh@fshows.com|Q:991564110>
 * @link http://www.51youdian.com/
 * @copyright 2015-2016 51youdian.com
 * @package ApiResponse\ErrorResponse
 * @since 1.0
 * @date: 2016/8/19- 10:28
 */

namespace ApiResponse;


class ErrorResponse
{
    /**
     * 错误码
     * @var [type]
     */
    public static $errCodes = [
        // 系统码
        '200' => '成功',
        '400' => '未知错误',
        '401' => '无此权限',
        '402' => '参数错误',
        '500' => '服务器异常',


        // 公共错误码
        '1001' => '[app_id]缺失',
        '1002' => '[app_id]不存在或无权限',
        '1003' => '[method]缺失',
        '1004' => '[format]错误',
        '1005' => '[sign_method]错误',
        '1006' => '[sign]缺失',
        '1007' => '[sign]签名错误',
        '1008' => '[method]方法不存在',
        '1009' => 'run方法不存在，请联系管理员',
        '1010' => '[nonce]缺失',
        '1011' => '[nonce]必须为字符串',
        '1012' => '[nonce]长度必须为1-32位'
    ];

    /**
     *
     * 返回错误码
     * @param string $code
     * @param bool $_
     * @return string
     */
    public static function getError($code = '400', $_ = false)
    {
        if (!isset(self::$errCodes[$code])) {
            $code = '400';
        }
        return ($_ ? "[{$code}]" : '') . self::$errCodes[$code];
    }
}