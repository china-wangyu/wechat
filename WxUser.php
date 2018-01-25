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

class WxUser extends WxBase
{

    /**
     * [code 重载http,获取微信授权]
     * @param  string   $appid           [微信公众号APPID]
     * @return [header] [重载链接]
     */
    public static function code($appid = '')
    {
        empty($appid) ? self::json(400, '请设置管理端微信公众号开发者APPID ~ !') : '';
        $service_url     = urlencode('https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]);
        $weixin_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $service_url . '&response_type=code&scope=snsapi_userinfo&state=state&connect_redirect=1#wechat_redirect';
        header('location: ' . $weixin_code_url);
    }

    /**
     * [getOpenid 获取用户 OPENID]
     * @param  string  $code                         [微信授权CODE]
     * @param  string  $appid                        [微信appid]
     * @param  string  $appSecret                    [微信appSecret]
     * @param  boolen  $type                         [true:获取用户信息 | false:用户openid]
     * @return [array] [用户信息|用户openid]
     */
    public static function getOpenid($code, $appid, $appSecret, $type = true)
    {
        empty($appid) or empty($appSecret) ? self::json(400, '请设置管理端微信公众号开发者APPID 和 APPSECRET~ !') : '';
        empty($code) ? self::json(400, '请验证是否传了正确的参数 code ~ !') : '';
        $weixin_oauth2_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code';
        $result            = self::curl_request($weixin_oauth2_url, true);
        switch ($type) {
            case true:
                return self::getUserinfo($result['access_token'], $result['openid']);
                break;
            case false:
                return $result;
                break;
        }
    }

    /**
     * [getUserinfo 获取用户信息]
     * @param  [type] $access_token   [授权获取用户关键参数：普通access_token]
     * @param  [type] $openid         [用户openid]
     * @return [type] [description]
     */
    public static function getUserinfo($access_token, $openid)
    {
        empty($access_token) or empty($openid) ? self::json(400, 'getOpenid()方法设置参数~ !') : '';
        $weixin_userinfo = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        $result          = self::curl_request($weixin_userinfo, true);
        return $result;
    }

}
