<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace wechat\lib;

/**
 * Trait Tool 工具类
 * @package wechat\lib
 */
trait Tool
{
    /**
     * 接口 json 成功输出
     * @param string $msg
     * @param array $data
     */
    public static function success(string $msg = '操作成功', array $data = [], array $options = [])
    {
        Json::success($msg, $data, $options);
    }

    /**
     * 接口 json 成功输出
     * @param string $msg
     * @param array $data
     */
    public static function error(string $msg = '操作失败')
    {
        Json::error($msg);
    }

    /**
     * 重载路由
     * @param string $url
     * @param array $params
     */
    public static function header(string $url, array $params = []): void
    {
        Request::header($url, $params);
    }

    /**
     * curl 发送 POST 请求
     * @param string $url
     * @param array $params
     * @return array
     */
    public static function post(string $url,$params = [])
    {
        return Request::request('POST',$url,$params);
    }

    /**
     * curl 发送 GET 请求
     * @param string $url
     * @param array $params
     * @return array
     */
    public static function get(string $url,array $params = [])
    {
        return Request::request('GET',$url,$params);
    }

    /**
     * url拼接数组
     * @param array $params
     * @return string
     */
    public static function url_splice_array(array $params = [])
    {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

}