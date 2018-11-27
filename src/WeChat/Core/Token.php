<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace WeChat\Core;

/**
 * Class WxToken 微信Token类
 * @package wechat
 */
class Token extends Base
{
    private static $getTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';

    /**
     * [gain 获取微信access_token]
     * @param  string $appid [微信AppID]
     * @param  string $appSecret [微信AppSecret]
     * @return [string] [微信access_token]
     */
    public static function gain(string $appid = '',string $appSecret = '')
    {
        $param = \WeChat\Extend\File::param('access_token');
        if ($param === null or (isset($param['time']) and time() - $param['time'] > 7150)) {
            /****************      进行微信AppID 和 AppSecret的验证   ******************/
            if(empty($appid) or empty($appSecret)){
                self::error('请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
            }
            if(!is_string($appid) or !is_string($appSecret)){
                self::error('微信公众号开发者APPID 和 APPSECRET格式错误~ !');
            }
            /****************      获取参数验证规则      ******************/
            if (strlen(trim($appid)) == 18 or strlen(trim($appSecret)) == 18) {

                static::$getTokenUrl = str_replace('APPID', $appid, static::$getTokenUrl);
                static::$getTokenUrl = str_replace('APPSECRET', $appSecret, static::$getTokenUrl);

                $result = self::get(static::$getTokenUrl);
                if ($result['errcode'] == 0){
                    \WeChat\Extend\File::param('access_token', $result);
                    return $result['access_token'];
                }
                self::error('获取失败~'.$result['errmsg']);
            } else {
                self::error('请设置正确格式的微信公众号开发者APPID 和 APPSECRET~ !');
            }
        } else {
            return $param['access_token'];
        }
    }

}
