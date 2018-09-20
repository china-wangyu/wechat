<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace wechat;

/**
 * Class WxToken 微信Token类
 * @package wechat
 */
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
        $param = \wechat\lib\File::param('access_token');
        if ($param === null) {
            /****************      进行微信AppID 和 AppSecret的验证   ******************/
            (empty($appid) or empty($appSecret)) && \wechat\lib\Abnormal::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
            (!is_string($appid) or !is_string($appSecret)) && \wechat\lib\Abnormal::error('微信公众号开发者APPID 和 APPSECRET格式错误~ !');
            /****************      获取参数验证规则      ******************/
            if (strlen(trim($appid)) == 18 or strlen(trim($appSecret)) == 18) {
                $access_token_url              = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret;
                $result                        = self::curl_request($access_token_url, true);
                \wechat\lib\File::param('access_token',$result);
                return $result['access_token'];
            } else {
                \wechat\lib\Abnormal::error('请设置正确格式的微信公众号开发者APPID 和 APPSECRET~ !');
            }
        } else {
            return $param['access_token'];
        }
    }

}
