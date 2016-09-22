<?php
/**
 * ApiResponse.php
 * @author  lmh <lmh@fshows.com|Q:991564110>
 * @link http://www.51youdian.com/
 * @copyright 2015-2016 51youdian.com
 * @package ApiResponse\ApiResponse
 * @since 1.0
 * @date: 2016/8/19- 10:28
 */

namespace ApiResponse;

class ApiResponse
{
    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * API请求Method名
     * @var string
     */
    protected $method;

    /**
     * app_id
     * @var string
     */
    protected $appId;

    /**
     * app_secret
     * @var string
     */
    protected $appSecret;

    /**
     * 回调数据格式
     * @var string
     */
    protected $format = 'json';

    /**
     * 签名方法
     * @var string
     */
    protected $signMethod = 'md5';

    /**
     * 是否输出错误码
     * @var boolean
     */
    protected $errorCodeShow = false;

    /**
     * 接口分组 可以按照项目分组
     * @var
     */
    public $group = 'open';
    public $groupNameSpace = __NAMESPACE__;

    /**
     *
     * 初始化
     * ResponseService constructor.
     */
    public function __construct()
    {
        $this->error = new ErrorResponse();
    }

    /**
     * api服务入口执行
     * @return response
     */
    public function run()
    {
        // A.1 初步校验
        $rules = [
            'app_id' => 'required',
            'method' => 'required',
            //'format' => 'json',
            //'sign_method' => 'md5',
            'nonce' => 'required',
            'sign' => 'required',
            //'biz_content'=>'required'
        ];
        $msg = [
            'app_id' => '1001',
            'method' => '1003',
            //'format' => '1004',
           // 'sign_method' => '1005',
            'nonce' => '1010',
            'sign' => '1006',
            //'biz_content'=>'400'
        ];
        if(!$this->params){
            return $this->response(['status' => false, 'code' => '402']);
        }
        $messages = static::validator($this->params, $rules, $msg);
        if ($messages) {
            return $this->response(['status' => false, 'code' => array_shift($messages)['errorCode']]);
        }
        // A.2 赋值对象
        $this->format = !empty($this->params['format']) ? $this->params['format'] : $this->format;
        $this->signMethod = !empty($this->params['sign_method']) ? $this->params['sign_method'] : $this->signMethod;
        $this->appId = $this->params['app_id'];
        $this->method = $this->params['method'];

        // C. 校验签名
        $signRes = $this->checkSign($this->params);
        if (!$signRes || !$signRes['status']) {
            return $this->response(['status' => false, 'code' => $signRes['code']]);
        }
        // D. 校验接口名
        // D.1 通过方法名获取类名
        $className = $this->getClassName($this->method);

        // D.2 判断类名是否存在
        $classPath = $this->groupNameSpace . '\\' . $this->group . '\\' . $className;

        if (!$className || !class_exists($classPath)) {
            return $this->response(['status' => false, 'code' => '500']);
        }

        // D.3 判断方法是否存在
        if (!method_exists($classPath, 'run')) {
            return $this->response(['status' => false, 'code' => '500']);
        }
        // E. api接口分发
        $class = new $classPath;
        return $this->response((array)$class->run($this->params));
    }

    /**
     *
     * 校验签名
     * @param $params
     * @return array
     */
    protected function checkSign($params)
    {
        $sign = array_key_exists('sign', $params) ? $params['sign'] : '';

        if (empty($sign)) {
            return array('status' => false, 'code' => '1006');
        }
        unset($params['sign']);

        if ($sign != $this->generateSign($params)) {
            return array('status' => false, 'code' => '1007');
        }
        return array('status' => true, 'code' => '200');
    }

    /**
     * 生成签名
     * @param  array $params 待校验签名参数
     * @return string|false
     */
    protected function generateSign($params)
    {
        if ($this->signMethod == 'md5') {
            return $this->generateMd5Sign($params);
        }
        return false;
    }

    /**
     * md5方式签名
     * @param  array $params 待签名参数
     * @return string
     */
    protected function generateMd5Sign($params)
    {
        ksort($params);

        $attachString = "";
        foreach ($params as $k => $v) {
            $attachString .= $k . "=" . trim($v) . "&";
        }
        $string = trim($attachString, "&") . $this->appSecret;
        return strtoupper(md5($string));
    }


    /**
     * 通过方法名转换为对应的类名
     * @param  string $method 方法名
     * @return string|false
     */
    protected function getClassName($method)
    {
        $methods = explode('.', $method);
        if (!is_array($methods)) {
            return false;
        }
        //第一段作为接口分组
        $this->group = array_shift($methods);
        $tmp = array();
        foreach ($methods as $value) {
            $tmp[] = ucwords($value);
        }
        return implode('', $tmp);
    }

    /**
     * 输出结果
     * @param  array $result 结果
     * @return response
     */
    public function response(array $result)
    {
        if (!array_key_exists('msg', $result) && array_key_exists('code', $result)) {
            $result['msg'] = $this->getError($result['code']);
        }
        if ($this->format == 'json') {
            return json_encode($result);
        }
        return false;
    }

    /**
     * 返回错误内容
     * @param  string $code 错误码
     * @return string
     */
    protected function getError($code)
    {
        return $this->error->getError($code, $this->errorCodeShow);
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     *
     */
    public function setParams()
    {
        $this->params = json_decode(file_get_contents('php://input'), true);
        //$this->params = json_decode(isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '', true);
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    public static function validator($params, $rules, $msg)
    {
        $messages = [];
        foreach ($rules as $k => $v) {
            if (isset($params[$k])) {
                if ($v == 'required') {
                    if ('' == $params[$k]) {
                        $messages[$k]['errorCode'] = $msg[$k];
                    }
                } else {
                    if ($v != $params[$k]) {
                        $messages[$k]['errorCode'] = $msg[$k];
                    }
                }
            } else {
                $messages[$k]['errorCode'] = $msg[$k];
            }
        }
        return $messages;
    }
}
