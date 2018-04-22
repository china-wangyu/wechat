<?php
/**
 * 微信ticket类 含签名生成
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:36:00
 * @version 1.0.2
 *
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
 */
namespace wechat;

class WxTicket extends WxBase
{

    /**
     * [getTicket 设置微信jsapi_ticket]
     * @param  string   $access_token          [微信普通token]
     * @return [string] [微信jsapi_ticket]
     */
    public static function getTicket($accessToken = '')
    {
        /****************      验证微信普通token   ******************/
        empty($accessToken) && $accessToken = WxToken::getToken();
        if (!isset($_SESSION['jsapi_ticket']) or empty($_SESSION['jsapi_ticket']) or time() - $_SESSION['jsapi_ticket_time'] > 7100) {
            $wechat_jsapi_ticket_url       = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=' . $accessToken;
            $result                        = self::curl_request($wechat_jsapi_ticket_url, true);
            $_SESSION['jsapi_ticket']      = $result['ticket'];
            $_SESSION['jsapi_ticket_time'] = $result['ticket'];
            return $_SESSION['jsapi_ticket'];
        } else {
            return $_SESSION['jsapi_ticket'];
        }

    }

    /**
     * [getSign 获取微信JSDK]
     * @param  [string] $ticket        [获取微信JSDK签名]
     * @return [array]  [微信JSDK]
     */
    public static function getSign($ticket = '')
    {
        empty($ticket) && $ticket = self::getTicket();
        $data['url']              = $_SERVER['REQUEST_SCHEME'] . '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $data['timestamp']        = time();
        $data['nonceStr']         = md5('timestamp=' . $data['timestamp']);
        $data['jsapi_ticket']     = $ticket;
        $param['rawString']       = join('&', $data);
        $param['signature']       = sha1($param['rawString']);
        $param['nonceStr']        = $data['nonceStr'];
        $param['timestamp']       = $data['timestamp'];
        $param['url']             = $data['url'];
        return $param;
    }

}
