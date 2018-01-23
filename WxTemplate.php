<?php
/**
 *
 * @date    2018-01-23 17:13:04
 * @version 1.0
 * @authors wene (china_wangyu@aliyun.com)
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
        $param             = self::trim_template($template['content']);
        $template['param'] = [
            'touser'      => '', // 用户OPENID
            'template_id' => $template['template_id'], //模板ID
            'url'         => '', // 跳转的url地址
            'topcolor'    => '',
            'data'        => $param, //模板必须参数
        ];
        return $template;
    }

    /**
     * [getTemplateAll 获取所有消息模板内容]
     * @param  string $accessToken    [description]
     * @return [type] [description]
     */
    public static function getTemplateAll($accessToken = '')
    {
        /****************      验证微信普通token   ******************/
        if (empty($accessToken)) {
            $accessToken = WxToken::getToken();
        }
        $template_url = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=' . $accessToken;
        $result       = self::curl_request($template_url, true);
        foreach ($result['template_list'] as $key) {
            $templateObj[] = self::initTemplate($key);
        }
        return $templateObj;
    }

    /**
     * [trim_template 获取模板需要的参数name]
     */
    private static function trim_template($string)
    {
        $string = preg_replace('/([\x80-\xff]*)/i', '', $string);
        $trim   = array(" ", "　", "\t", "\n", "\r", '.DATA', '}}');
        $arr    = explode('{{', str_replace($trim, '', $string));
        unset($arr[0]);
        return array_values($arr);
    }

}
