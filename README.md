# apiModule
``` json
   composer require lmh/api-module
```

``` json
    "require": {
        "lmh/api-module":  "*"
    }
```


执行如下SQL语句
## 部署说明

> 可根据文档自行调整，以适用其他框架下使用
> 项目用到了缓存可以改成其框架下的缓存也可以直接使用该项目的缓存组件进行修改redis mysql等

```sql
CREATE TABLE `prefix_apps` (
  `id` INT(10) NOT NULL AUTO_INCREMENT COMMENT '自增长',
  `app_id` VARCHAR(60) NOT NULL COMMENT 'appid',
  `app_secret` VARCHAR(100) NOT NULL COMMENT '密钥',
  `app_name` VARCHAR(200) NOT NULL COMMENT 'app名称',
  `app_desc` TEXT COMMENT '描述',
  `status` TINYINT(2) DEFAULT '0' COMMENT '生效状态',
  `created_at` INT(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` INT(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`),
  KEY `status` (`status`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='应用表';
```

#### 请求地址及请求方式

> 请求地址：`/api/test`;
>
> 请求方式：`POST`

#### 公共参数

|参数名|类型|是否必须|描述|
|----|----|----|----|
|app_id|string|是|应用ID|
|method|string|是|接口名称|
|format|string|否|回调格式，默认：json（目前仅支持）|
|sign_method|string|否|签名类型，默认：md5（目前仅支持）|
|nonce|string|是|随机字符串，长度1-32位任意字符|
|sign|string|是|签名字符串，参考[签名规则](#签名规则)|

#### 业务参数

> API调用除了必须包含公共参数外，如果API本身有业务级的参数也必须传入，每个API的业务级参数请考API文档说明。



#### 签名规则

- 对所有API请求参数（包括公共参数和请求参数，但除去`sign`参数），根据参数名称的ASCII码表的顺序排序。如：`foo=1, bar=2, foo_bar=3, foobar=4`排序后的顺序是`bar=2, foo=1, foo_bar=3, foobar=4`。
- 将排序好的参数名和参数值拼装在一起，根据上面的示例得到的结果为：bar=2&foo=1&foo_ba=3&foobar=4。
- 把拼装好的字符串采用utf-8编码，使用签名算法对编码后的字节流进行摘要。如果使用`MD5`算法，则需要在拼装的字符串前后加上app的`secret`后，再进行摘要，如：md5(secret+bar=2&foo=1&foo_bar=3&foobar=4+secret)
- 将摘要得到的字节结果使用大写表示

#### 返回结果

```json
// 成功
{
    "status": true,
    "code": "200",
    "msg": "成功",
    "data": {
        "time": "2016-08-02 12:07:09"
    }
}

// 失败
{
    "status": false,
    "code": "1001",
    "msg": "[app_id]缺失"
}
```
#### API接口命名规范（method）

 接口命名规范
    - 命名字母按功能或模块从大到小划分，依次编写；如后台用户修改密码：'admin.user.password.update'
    - 字母最后单词为操作。查询:`get`;新增:`add`;更新:`update`;删除:`delete`;上传:`upload`;等
    - 第一位是分组 案例中使用了open分组 open.xx.xx
    
    
#### API DEMO 示例

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
