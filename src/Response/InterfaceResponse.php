<?php
/**
 * InterfaceResponse.php
 * @author  lmh <lmh@fshows.com|Q:991564110>
 * @link http://www.51youdian.com/
 * @copyright 2015-2016 51youdian.com
 * @package ApiResponse\InterfaceResponse
 * @since 1.0
 * @date: 2016/8/19- 10:26
 */

namespace ApiResponse;


Interface InterfaceResponse
{
    /**
     *
     * 执行接口
     * @param $params
     * @return mixed
     */
    public function run(&$params);

    /**
     * 返回接口名称
     * @return string
     */
    public function getMethod();
}