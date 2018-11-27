<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace WeChat\Core;

/**
 * Class WxSend 微信推送类
 * @package wechat
 */
class Send extends Base
{

    private static $setMsgUrl = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN';

    /**
     * 关键字回复
     * @param array $paramObj 参数数组
     * @param array $postObj 微信对象
     * @param int $type 关键字模板 图文：true | 文本： false
     */
    public static function keyWord(array $paramObj, array $postObj, int $type = 2)
    {
        // 验证参数
        if (empty($paramObj) or empty($postObj)) {
            self::error('请设置正确的参数 $paramObj or $postObj~ !');
        }
        $templateString = self::getKeyWordTemplate($type);
        $fromUsername = $postObj['FromUserName'];
        $toUsername = $postObj['ToUserName'];
        $time = time();
        switch ($type) {
            case 1:
                if (empty($paramObj['title']) or empty($paramObj['content']) or empty($paramObj['imgurl']) or empty($paramObj['jumpurl'])) {
                    self::error('请设置正确的参数值~!');
                }
                $resultStr = sprintf($templateString, $fromUsername, $toUsername, $time, $paramObj['title'],
                    $paramObj['content'], $paramObj['imgurl'], $paramObj['jumpurl']);
                break;
            case 2:
                empty($paramObj['content']) && self::error('请设置正确的参数值~!');
                $resultStr = sprintf($templateString, $fromUsername, $toUsername, $time, 'text', $paramObj['content']);
                break;
            case 3:
                $resultStr = sprintf($templateString, $fromUsername, $time);
                break;
        }
        echo $resultStr;
        die;
    }


    /**
     * 发送模板消息
     * @param string $accessToken
     * @param string $templateId 模板ID
     * @param string $openid 用户openid
     * @param array $data 模板参数
     * @param string $url 模板消息链接
     * @param string $topColor 微信top颜色
     * @return array
     */
    public static function msg(string $accessToken, string $templateId, string $openid, array $data = [], string $url = '', string $topColor = '#FF0000')
    {
        // 验证微信普通token
        empty($accessToken) && $accessToken = Token::gain();

        // 检测参数
        if (empty($data) or empty($openid) or empty($templateId)) {
            self::error('请设置正确的参数 $template or $value~ !');
        }

        // 准备数据
        $template['template_id'] = $templateId;
        $template['touser'] = $openid;
        $template['url'] = empty($url) ? '' : $url;
        $template['topcolor'] = empty($topColor) ? '' : $topColor;
        $template['data'] = $data;
        $send_url = str_replace('ACCESS_TOKEN', $accessToken, static::$setMsgUrl);

        // 发送请求，并返回
        return self::post($send_url, json_encode($template, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 获取模板关键字模板
     * @param int $type
     * @return string
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
