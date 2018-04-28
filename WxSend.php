<?php
/**
 * 微信推送类
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

class WxSend extends WxBase
{
    /**
     * [sendKeyWord 关键字回复]
     * @param  array           $paramObj       [参数数组]
     * @param  array           $postObj        [微信对象]
     * @param  boolean         $template       [关键字模板 图文：true | 文本： false]
     * @return [string|boolen] [description]
     */
    public static function sendKeyWord(array $paramObj = [], $postObj = [], $template = 2)
    {
        (empty($paramObj) or empty($postObj)) && \wechat\lib\Abnormal::error('请设置正确的参数 $paramObj or $postObj~ !');
        $templateString = self::getKeyWordTemplate($template);
        $fromUsername   = $postObj->FromUserName;
        $toUsername     = $postObj->ToUserName;
        $time           = time();
        switch ($template) {
            case 1:
                if (empty($paramObj['title']) or empty($paramObj['content']) or empty($paramObj['imgurl']) or empty($paramObj['jumpurl'])) {
                    \wechat\lib\Abnormal::error('请设置正确的参数值~!');
                }
                $resultStr = sprintf($templateString, $fromUsername, $toUsername, $time, $paramObj['title'], $paramObj['content'], $paramObj['imgurl'], $paramObj['jumpurl']);
                break;
            case 2:
                empty($paramObj['content']) && \wechat\lib\Abnormal::error('请设置正确的参数值~!');
                $resultStr = sprintf($templateString, $fromUsername, $toUsername, $time, 'text', $paramObj['content']);
                break;
            case 3:
                $resultStr = sprintf($templateString, $fromUsername, $time);
                break;
        }

        echo $resultStr;
    }

    /**
     * [sendMsg 发送模板消息]
     * @param  string $templateid [模板ID]
     * @param  string $openid     [用户openid]
     * @param  array  $data       [模板参数]
     * @param  string $url        [模板消息链接]
     * @param  string $topcolor   [微信top颜色]
     * @return [ajax] [boolen]
     */
    public static function sendMsg($accessToken = '', $templateid = '', $openid = '', $data = [], $url = '', $topcolor = '#FF0000')
    {
        /****************      验证微信普通token   ******************/
        empty($accessToken) && $accessToken = WxToken::getToken();
        (empty($data) or empty($openid) or empty($templateid)) && \wechat\lib\Abnormal::error('请设置正确的参数 $template or $value~ !');

        $template['template_id'] = $templateid;
        $template['touser']      = $openid;
        $template['url']         = empty($url) ? '' : $url;
        $template['topcolor']    = empty($topcolor) ? '' : $topcolor;
        $template['data']        = $data;
        $send_url                = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
        return self::curl_request($send_url, true, 'post', json_encode($template));
    }

    /**
     * [send_menu 生成菜单]
     * 例如：$menu =[
     *     'menu_name'=> '掌上商城',
     *     'menu_status'=> 0; //0表示view
     *     'menu_url' => 'http://www.baidu.com',
     *     'chind' => [
     *         'menu_name'=> '掌上商城',
     *         'menu_status'=> 0; //0表示view
     *         'menu_url' => 'http://www.baidu.com',
     *         ],
     *     ];
     * @param  array   $menu                                  [菜单内容 ]
     * @return [array] [微信返回值：状态值数组]
     */
    public static function sendMenu($accessToken = '', $menu = [])
    {
        /****************      验证微信普通token   ******************/
        empty($accessToken) && $accessToken = WxToken::getToken();
        (!is_array($menu) or count($menu) == 0) && \wechat\lib\Abnormal::error('请设置正确的参数 $menu ~ !');

        $format_param['button'] = self::format_param($menu);
        $send_url               = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $accessToken;
        return self::curl_request($send_url, true, 'post', json_encode($format_param, JSON_UNESCAPED_UNICODE));
    }

    /**
     * [format_param 格式化菜单数组]
     * @param  [array] $menu      [数组]
     * @return [array] [数组]
     */
    private static function format_param($menu)
    {
        $button = [];
        foreach ($menu as $key => $val) {
            $button[$key]['name'] = $val['menu_name'];
            if (empty($val['chind'])) {
                $button[$key]['type']                                   = $val['menu_status'] == 0 ? 'view' : 'click';
                $button[$key][$val['menu_status'] == 0 ? 'url' : 'key'] = $val['menu_url'];
            } else {
                foreach ($val['chind'] as $chind => $value) {
                    $button[$key]['sub_button'][$chind]['name']                                     = $value['menu_name'];
                    $button[$key]['sub_button'][$chind]['type']                                     = $value['menu_status'] == 0 ? 'view' : 'click';
                    $button[$key]['sub_button'][$chind][$chind['menu_status'] == 0 ? 'url' : 'key'] = $value['menu_url'];
                }
            }
        }
        return $button;
    }

    /**
     * [getKeyWordTemplate 获取模板关键字模板]
     * @param  boolean  $type            [图文：true | 文本： false]
     * @return [string] [模板内容]
     */
    private static function getKeyWordTemplate($type = 1)
    {
        switch ($type) {
            case 1: // true : 图文
                return "<xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[news]]></MsgType>//消息类型为news（图文）
                  <ArticleCount>1</ArticleCount>//图文数量为1（单图文）
                  <Articles>
                  <item>//第一张图文消息
                  <Title><![CDATA[%s]]></Title> //标题
                  <Description><![CDATA[%s]]></Description>//描述为空（懒得描述）
                  <PicUrl><![CDATA[%s]]></PicUrl>//打开前的图片链接地址
                  <Url><![CDATA[%s]]></Url>//点击进入后显示的图片链接地址
                  </item>
                  </Articles>
                  </xml> ";
                break;

            case 2: // false ： 文字
                return "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
                break;
            case 3:
                return '<xml>
                          <ToUserName><![CDATA[%s]]></ToUserName>
                          <FromUserName><![CDATA[%s]]></FromUserName>
                          <CreateTime>%s</CreateTime>
                          <MsgType><![CDATA[event]]></MsgType>
                          <Event><![CDATA[subscribe]]></Event>
                          <EventKey><![CDATA[scanbarcode]></EventKey>
                        </xml>';
        }
    }
}
