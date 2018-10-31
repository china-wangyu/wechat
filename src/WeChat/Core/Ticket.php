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

    /**
     * [getTicket 设置微信jsapi_ticket]
     * @param  string   $access_token          [微信普通token]
     * @return [string] [微信jsapi_ticket]
     */
    public static function getTicket($accessToken = '')
    {
        /****************      验证微信普通token   ******************/
        empty($accessToken) && $accessToken = Token::getToken();
        $param = \WeChat\Lib\File::param('ticket');
        if ($param === null or (isset($param['time']) and time() - $param['time'] > 7150)) {
            $wechat_jsapi_ticket_url       = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=' . $accessToken;
            $result                        = self::get($wechat_jsapi_ticket_url);
            if(isset($result['ticket'])){
                \WeChat\Lib\File::param('ticket',$result);
                return $result['ticket'];
            }else{
                return false;
            }
        } else {
            return $param['ticket'];
        }

    }

    /**
     * [getSign 获取微信JSDK]
     * @param  [string] $ticket        [获取微信JSDK签名]
     * @return [array]  [微信JSDK]
     */
    public static function getSign($ticket = '',$redirect_url = '')
    {
        empty($ticket) && $ticket = self::getTicket();
        $url              = empty($redirect_url) ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] :$redirect_url;
        $timestamp        = time();
        $nonceStr        = self::createNonceStr();
        $string = 'jsapi_ticket='.$ticket.'&noncestr='.$nonceStr.'&timestamp='.$timestamp.'&url='.$url;
        $param['rawString']       = $string;
        $param['signature']       = sha1($param['rawString']);
        $param['nonceStr']        = $nonceStr;
        $param['timestamp']       = $timestamp;
        $param['url']             = $url;
        return $param;
    }

    private static function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
