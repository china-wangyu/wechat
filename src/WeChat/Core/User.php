<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace WeChat\Core;

/**
 * Class WxUser 微信用户类
 * @package wechat
 */
class User extends Base
{

    /**
     * [code 重载http,获取微信授权]
     * @param  string $appid [微信公众号APPID]
     * @return [header] [重载链接]
     */
    public static function code($appid = '')
    {
        empty($appid) && self::error('请设置管理端微信公众号开发者APPID ~ !');
        //当前域名
        $service_url = urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $weixin_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $service_url . '&response_type=code&scope=snsapi_userinfo&state=state&connect_redirect=1#wechat_redirect';
        self::header($weixin_code_url);
    }

    /**
     * 获取用户 OPENID
     * @param string $code     微信授权CODE
     * @param string $appid    微信appid
     * @param string $appSecret    微信appSecret
     * @param bool $type    true:获取用户信息 | false:用户openid
     * @return array    用户信息|用户openid
     */
    public static function getOpenid($code, $appid, $appSecret, $type = false)
    {
        //验证参数
        (empty($appid) or empty($appSecret)) && self::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
        empty($code) && self::error('请验证是否传了正确的参数 code ~ !');
        //获取用户数据
        $weixin_oauth2_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appSecret . '&code=' . $code . '&grant_type=authorization_code';
        $result = self::get($weixin_oauth2_url);

        return $type == false ? $result : self::getUserinfo($result['access_token'], $result['openid']);
    }


    /**
     * 获取用户信息
     * @param string $access_token 授权获取用户关键参数：access_token
     * @param string $openid   用户openid
     * @return array
     */
    public static function getUserinfo($access_token, $openid)
    {
        (empty($access_token) or empty($openid)) && self::error('getOpenid()方法设置参数~ !');
        $weixin_userinfo = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
        return self::get($weixin_userinfo);
    }

}
