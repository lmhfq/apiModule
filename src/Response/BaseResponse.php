<?php
/**
 * BaseResponse.php
 * @author  lmh <lmh@fshows.com|Q:991564110>
 * @link http://www.51youdian.com/
 * @copyright 2015-2016 51youdian.com
 * @package ApiResponse\BaseResponse
 * @since 1.0
 * @date: 2016/8/19- 10:27
 */

namespace ApiResponse;


abstract class BaseResponse
{
    protected $method;

    /**
     * 返回接口名称
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}