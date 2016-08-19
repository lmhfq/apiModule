<?php
/**
 * Application.php
 * @author  lmh <lmh@fshows.com|Q:991564110>
 * @link http://www.51youdian.com/
 * @copyright 2015-2016 51youdian.com
 * @package ApiResponse\Application
 * @since 1.0
 * @date: 2016/8/19- 14:16
 */

namespace ApiResponse;


use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;

class Application
{
    /**
     * appid
     * @var [type]
     */
    protected $appId;

    /**
     * 缓存key前缀
     * @var string
     */
    protected $cacheKeyPrefix = 'api-app-info';

    /**
     * 初始化
     * @param [type] $app_id [description]
     */
    public function __construct($app_id)
    {
        $this->appId = $app_id;
    }


    /**
     *
     *
     * 获取当前对象
     * @param $app_id
     * @return Application
     */
    public static function getInstance($app_id)
    {
        static $_instances = [];

        if (array_key_exists($app_id, $_instances)) {
            return $_instances[$app_id];
        }
        return $_instances[$app_id] = new self($app_id);
    }

    /**
     *
     * 获取app信息
     * @return mixed
     */
    public function info()
    {
        $cacheKey = $this->cacheKeyPrefix . $this->appId;
        $cacheDir = '/tmp';
        $adapter = new File($cacheDir);
        $adapter->setOption('ttl', 3600);
        $cache = new Cache($adapter);
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }
        return null;
    }

    /**
     * @param $app
     * @return mixed
     */
    public function set($app)
    {
        $cacheDir = './tmp';
        $adapter = new File($cacheDir);
        $adapter->setOption('ttl', 3600);
        $cache = new Cache($adapter);
        $cacheKey = $this->cacheKeyPrefix . $this->appId;
        $cache->set($cacheKey, $app, 3600);
        return $app;
    }


}