<?php
/**
 * 异常处理
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:45:19
 * @version 1.0.3
 *
 *  *  *  ** 求职区 **
 *  期望城市： 成都
 *  期望薪资： 8k - 12k
 *
 *  个人信息
 *
 *  工作经验: 3年
 *  开发语言: PHP / Python
 *
 *  联系方式：china_wangyu@aliyun.com
 */
namespace wechat\lib;

class Abnormal
{

    /**
     * [error 请求失败]
     * @param  int|integer $code [description]
     * @param  string      $msg  [description]
     * @return [type]            [description]
     */
    public static function error(string $msg = '请求失败')
    {
        self::return_abnormal(400, $msg);
    }

    /**
     * [success 请求成功]
     * @param  string $msg  [description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public static function success(string $msg = '请求成功', array $data = [])
    {
        self::return_abnormal(200, $msg, $data);
    }

    /**
     * [return_abnormal 输出异常]
     * @param  [type] $code [状态码]
     * @param  [type] $msg  [原因]
     * @param  array  $data [输出数据]
     * @return [type]       [json]
     */
    public static function return_abnormal($code, $msg, $data = [])
    {
        $code_state = $code == 200 ? 'OK' : 'Bad Request';
        $param      = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        header("HTTP/1.1 " . $code . " " . $code_state);
        header('Content-Type:application/json;charset=utf-8');
        if ($param !== null) {
            echo json_encode($param, JSON_UNESCAPED_UNICODE);
        }
        exit();
    }
}
