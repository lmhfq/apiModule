# apiModule
``` json
    "require": {
        "lmhdxa/apiModule":  "*"
    }
```
``` php
<?php
namespace Test;
use ApiResponse\ApiResponse;
use ApiResponse\Application;
include_once 'vendor/autoload.php';
class demo
{
    public function index()
    {
        $server = new ApiResponse();
        $server->setParams();
        $params = $server->getParams();
        $app = Application::getInstance($params['app_id'])->info();
        //查询 appid校验
        if (!$app) {
            //查询数据库
            $appInfo = ['app_id' => '88888888888888888', 'app_secret' => 'dasfasfafa41545458'];
            $app = Application::getInstance($params['app_id'])->set($appInfo);
        }
        if (!$app) {
            $response = $server->response(['status' => false, 'code' => '401']);
        } else {
            $server->setAppSecret($app['app_secret']);
            $server->groupNameSpace = __NAMESPACE__;
            $response = $server->run();
        }
        die($response);
    }
}
(new demo())->index();

```
