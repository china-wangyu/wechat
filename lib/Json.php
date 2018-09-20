<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace wechat\lib;

use Throwable;
class Json extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * 请求失败
     * @param string $msg
     */
    public static function error(string $msg = '请求失败')
    {
        self::return_abnormal(400, $msg);
    }

    /**
     * 请求成功
     * @param string $msg 返回消息
     * @param array $data 返回data数据
     * @param array $options 多选主参数
     */
    public static function success(string $msg = '请求成功', array $data = [],$options = [])
    {
        self::return_abnormal(200, $msg, $data,$options);
    }

    /**
     * [return_abnormal 输出异常]
     * @param  [type] $code [状态码]
     * @param  [type] $msg  [原因]
     * @param  array  $data [输出数据]
     * @param array $options
     */
    public static function return_abnormal($code, $msg, $data = [],$options = [])
    {
        $code_state = $code == 200 ? 'OK' : 'Bad Request';
        $param      = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        $param = array_merge($param,$options);
        header("HTTP/1.1 " . $code . " " . $code_state);
        header('Content-Type:application/json;charset=utf-8');
        if ($param !== null) {
            echo json_encode($param, JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
}