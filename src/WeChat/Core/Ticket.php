<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace WeChat\Core;

/**
 * Class WxTicket 微信ticket类 含签名生成
 * @package wechat
 */
class Ticket extends Base
{
    // 微信ticket (jsapi)
    private static $getTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi';

    /**
     * 设置微信ticket
     * @param string $accessToken 微信普通token
     * @return bool 微信 ticket|false
     */
    public static function gain(string $accessToken = '')
    {
        // 验证微信普通token
        empty($accessToken) && $accessToken = Token::gain();
        $param = \WeChat\Extend\File::param('ticket');
        if ($param === null or (isset($param['time']) and time() - $param['time'] > 7150)) {

            //
            static::$getTicketUrl = str_replace('ACCESS_TOKEN',$accessToken,static::$getTicketUrl);
            $result = self::get(static::$getTicketUrl);

            if (isset($result['ticket'])) {
                \WeChat\Extend\File::param('ticket', $result);
                return $result['ticket'];
            } else {
                return false;
            }

        } else {
            return $param['ticket'];
        }

    }


    /**
     * 获取微信JSDK
     * @param string $ticket 获取微信JSDK签名
     * @param string $redirect_url 微信JSDK
     * @return mixed
     */
    public static function sign(string $ticket = '', string $redirect_url = '')
    {
        empty($ticket) && $ticket = self::gain();
        $url = empty($redirect_url) ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : $redirect_url;
        $timestamp = time();
        $nonceStr = self::createNonceStr();
        $string = 'jsapi_ticket=' . $ticket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
        $param['rawString'] = $string;
        $param['signature'] = sha1($param['rawString']);
        $param['nonceStr'] = $nonceStr;
        $param['timestamp'] = $timestamp;
        $param['url'] = $url;
        return $param;
    }


    /**
     * 创建随机字符微信版本
     * @param int $length
     * @return string
     */
    private static function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
