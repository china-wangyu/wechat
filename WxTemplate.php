<?php
/**
 * 微信模板类
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:36:00
 * @version 1.0.3
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

class WxTemplate extends WxBase
{

    /**
     * [initTemplate 格式化消息模板内容]
     * @param  array   $template              [模板内容]
     * @return [array] [消息模板内容]
     */
    public static function initTemplate($template = [])
    {
        $param = self::trim_template($template['content']);
        $template['param'] = [
            'touser' => '', // 用户OPENID
            'template_id' => $template['template_id'], //模板ID
            'url' => '', // 跳转的url地址
            'topcolor' => '',
            'data' => $param, //模板必须参数
        ];
        return $template;
    }

    /**
     * [getAllTemplate 获取所有消息模板内容]
     * @param  string $accessToken    [微信token]
     * @return [type] [description]
     */
    public static function getAllTemplate($accessToken = '')
    {
        /****************      验证微信普通token   ******************/
        empty($accessToken) && $accessToken = WxToken::getToken();
        $template_url = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=' . $accessToken;
        $result = self::get($template_url);
        foreach ($result['template_list'] as $key) {
            $templateObj[] = self::initTemplate($key);
        }
        return $templateObj;
    }

    /**
     * [trim_template 获取模板需要的参数name]
     * @param  [type] $string [过滤包含参数的字符串]
     * @return [type]         [不带其它字符的参数数组]
     */
    private static function trim_template($string)
    {
        $string = preg_replace('/([\x80-\xff]*)/i', '', $string);
        $trim = array(" ", "　", "\t", "\n", "\r", '.DATA', '}}');
        $arr = explode('{{', str_replace($trim, '', $string));
        unset($arr[0]);
        return array_values($arr);
    }

}
