<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

/**
 * Class WxSend 微信推送类
 * @package wechat
 */
class Send extends Base
{
    // 微信发送模板消息API
    private static $setMsgUrl = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=ACCESS_TOKEN';

    // 配置
    protected static $config;

    // 数据
    protected static $data;

    // 模板
    protected static $template;

    /**
     * 被动回复消息
     * @param array $config 微信消息对象
     * @param array $data   用户数据
     */
    public static function trigger(array $config = [],array $data = [])
    {
        static::$config = $config;
        static::$data = $data;
        try{
            static::$template = static::getMsgTemplate($data['MsgType']);
            echo static::setMsgTemplate();die;
        }catch (\Exception $exception){
            static::$template = static::getMsgTemplate('text');
            static::$data['Content'] = $exception->getMessage();
            echo static::setMsgTemplate();die;
        }
    }


    /**
     * 主动发送模板消息
     * @param string $accessToken
     * @param string $templateId 模板ID
     * @param string $openid 用户openid
     * @param array $data 模板参数
     * @param string $url 模板消息链接
     * @param string $topColor 微信top颜色
     * @return array
     */
    public static function push(string $accessToken, string $templateId, string $openid, array $data = [], string $url = '', string $topColor = '#FF0000')
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
     * @param string $type 属性，可选类型[text: 文本|image: 图片|voice: 语音|video: 视频|music: 音乐|news: 图文]
     * @inheritdoc 微信消息文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140543
     * @return string
     */
    protected static function getMsgTemplate()
    {
        $msgType = static::$data['MsgType'];
        $msgTemplate = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime>';
        $msgTemplate .= '<MsgType><![CDATA['.$msgType.']]></MsgType>';
        switch ($msgType) {
            case 'text':
                $msgTemplate .= "<Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag>";
                break;
            case 'image':
                $msgTemplate .= "<Image><MediaId>< ![CDATA[%s]]></MediaId></Image>";
                break;
            case 'voice':
                $msgTemplate .= "<Voice><MediaId>< ![CDATA[%s]]></MediaId></Voice>";
                break;
            case 'video':
                $msgTemplate .= "<Video>
                                    <MediaId>< ![CDATA[%s]]></MediaId>
                                    <Title>< ![CDATA[%s]]></Title>
                                    <Description>< ![CDATA[%s]]></Description>
                                    </Video>";
                break;
            case 'music':
                $msgTemplate .= "<Music>
                                <Title>< ![CDATA[%s]]></Title>
                                <Description>< ![CDATA[%s]]></Description>
                                <MusicUrl>< ![CDATA[%s]]></MusicUrl>
                                <HQMusicUrl>< ![CDATA[%s]]></HQMusicUrl>
                                <ThumbMediaId>< ![CDATA[%s]]></ThumbMediaId>
                                </Music>";
                break;
            case 'news': // true : 图文
                $msgTemplate .=  "
                  <ArticleCount>%s</ArticleCount>
                  <Articles>
                  <item>
                  <Title><![CDATA[%s]]></Title>
                  <Description><![CDATA[%s]]></Description>
                  <PicUrl><![CDATA[%s]]></PicUrl>
                  <Url><![CDATA[%s]]></Url>
                  </item>
                  </Articles>";
                break;
            default :
                $msgTemplate .= "<Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag>";
                break;
        }
        $msgTemplate .= '</xml>';
        return $msgTemplate;
    }

    /**
     * 设置模板消息
     */
    protected static function setMsgTemplate()
    {
        $msgType = static::$data['MsgType'];
        $time = time();
        switch ($msgType) {
            case 'text':
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['Content']);
                break;
            case 'image':
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['MediaId']);
                break;
            case 'voice':
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['MediaId']);
                break;
            case 'video':
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['MediaId'],static::$data['Title'],static::$data['Description']);
                break;
            case 'music':
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['Title'],static::$data['Description'],static::$data['MusicURL'],
                    static::$data['HQMusicUrl'],static::$data['ThumbMediaId']);
                break;
            case 'news': // true : 图文
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,1,static::$data['Title'],static::$data['Description'],
                    static::$data['PicUrl'],static::$data['Url']);
                break;
            default :
                $msgTemplate = sprintf(static::$template,static::$data['FromUserName'],static::$data['ToUserName'],
                    $time,static::$data['Content']);
                break;
        }
        return $msgTemplate;
    }
}
