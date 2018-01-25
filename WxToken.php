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

use think\Validate;

class WxToken extends WxBase
{

    /**
     * [set 设置微信access_token]
     */
    public static function getToken($appid = '', $appSecret = '')
    {
        if (!isset($_SESSION['access_token']) or empty($_SESSION['access_token']) and time() - $_SESSION['access_token_time'] > 7100) {
            /****************      进行微信AppID 和 AppSecret的验证   ******************/
            if (empty($appid) or empty($appSecret)) {
                self::json(400, '请设置管理端微信公众号开发者APPID 和 APPSECRET~ !');
            }
            /****************      获取参数验证规则      ******************/
            $rule = [
                'appid'     => ['require', 'alphaNum', 'length' => 18],
                'appSecret' => ['require', 'alphaNum', 'length' => 32],
            ];
            $validate = new Validate($rule);
            if ($validate->check(['appid' => $appid, 'appSecret' => $appSecret])) {
                $access_token_url              = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appSecret;
                $result                        = self::curl_request($access_token_url, true);
                $_SESSION['access_token']      = $result['access_token'];
                $_SESSION['access_token_time'] = time();
                return $_SESSION['access_token'];
            } else {
                self::json(400, '请设置正确格式的微信公众号开发者APPID 和 APPSECRET~ !');
            }
        } else {
            return $_SESSION['access_token'];
        }
    }

}
