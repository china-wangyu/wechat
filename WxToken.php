<?php
/**
 * 微信Token类
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

class WxToken extends WxBase
{

    /**
     * [getToken 获取微信access_token]
     * @param  string   $appid                 [微信AppID]
     * @param  string   $appSecret             [微信AppSecret]
     * @return [string] [微信access_token]
     */
    public static function getToken($appid = '', $appSecret = '')
    {
        if (!isset($_SESSION['access_token']) or empty($_SESSION['access_token']) or time() - $_SESSION['access_token_time'] > 7100) {
            /****************      进行微信AppID 和 AppSecret的验证   ******************/
            (empty($appid) or empty($appSecret)) && \wechat\lib\Abnormal::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
            (!is_string($appid) or !is_string($appSecret)) && \wechat\lib\Abnormal::error('微信公众号开发者APPID 和 APPSECRET格式错误~ !');
            /****************      获取参数验证规则      ******************/
            if (strlen(trim($appid)) == 18 or strlen(trim($appSecret)) == 18) {
                $access_token_url              = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret;
                $result                        = self::curl_request($access_token_url, true);
                $_SESSION['access_token']      = $result['access_token'];
                $_SESSION['access_token_time'] = time();
                return $_SESSION['access_token'];
            } else {
                \wechat\lib\Abnormal::error('请设置正确格式的微信公众号开发者APPID 和 APPSECRET~ !');
            }
        } else {
            return $_SESSION['access_token'];
        }
    }

}
