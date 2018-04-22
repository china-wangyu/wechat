<?php
/**
 *  ** 求职区 **
 *  期望城市： 成都
 *  期望薪资： 8k - 12k
 *
 *  个人信息
 *
 *  工作经验: 3年
 *  开发语言: PHP / Python
 *
 *  联系方式：china_wangyu@aliyun.com
 * @date    2018-01-23 17:13:04
 * @version 1.0
 * @authors wene (china_wangyu@aliyun.com)
 */
namespace wechat;

abstract class WxBase
{
    private static $STATUSCODE = [ //常用的HTTP状态码
        200 => 'OK',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unathorized',
        403 => 'ForBidden',
        404 => 'No Found',
        405 => 'Method Not Allowed',
        500 => 'Server Internal Error',
    ];

    /**
     * [response 输出返回数据]
     * @param  [type]       $code [HTTP状态码]
     * @param  [type]       $msg  [返回 数据|错误 描述]
     * @param  array        $data [返回数据]
     * @return [Response]
     */
    public static function json($code, $msg, $data = [])
    {
        $param = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        header("HTTP/1.1 " . $code . " " . self::$STATUSCODE[$code]);
        header('Content-Type:application/json;charset=utf-8');
        if ($param !== null) {
            echo json_encode($param, JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * [curl_request 发送http请求]
     * @param  [url]    $url                                                      [请求地址]
     * @param  boolean  $https                                                    [是否使用HTTPS]
     * @param  string   $method                                                   [请求方式：GET / POST]
     * @param  [array]  $data                                                     [post 数据]
     * @return [result] [成功返回对方返回的结果，是非返回false]
     */
    public static function curl_request($url, $https = false, $method = 'get', $data = null)
    {
        /****************      初始化curl     ******************/
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //结果为字符串且输出到屏幕上
        /****************     发送 https请求     ******************/
        if ($https === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        /********      发送 POST 请求  类型为：application/x-www-form-urlencoded    **********/
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
            // 所需传的数组用http_bulid_query()函数处理一下，就可以传递二维数组了
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        }
        /****************      发送请求    ******************/
        curl_setopt($ch, CURLOPT_URL, $url);
        $result     = curl_exec($ch);
        $url_status = curl_getinfo($ch);
        /****************      关闭连接 并 返回数据    ******************/
        curl_close($ch);
        return intval($url_status["http_code"]) == 200 ? json_decode($result, true) : false;
    }

    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    protected static function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

}
