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

    // 被动回复微信数据
    protected static $triggerConfig;

    // 被动回复用户数据
    protected static $triggerData;

    // 被动回复消息模板
    protected static $triggerTemplate;

    // 被动回复消息模板公共部分
    protected static $triggerTemplateStart = '<ToUserName>< ![CDATA[toUser] ]></ToUserName>
                                                <FromUserName>< ![CDATA[fromUser] ]></FromUserName>
                                                <CreateTime>12345678</CreateTime>
                                                <MsgType>< ![CDATA[news] ]></MsgType>';

    /**
     * 被动回复消息
     * @param array $triggerConfig  微信消息对象
     * @param array $triggerData    用户数据
     * @throws \Exception
     */
    public static function trigger(array $triggerConfig = [],array $triggerData = [])
    {
        static::$triggerConfig = $triggerConfig;
        static::$triggerData = $triggerData;
        try{
            static::$triggerTemplateStart = sprintf(static::$triggerTemplateStart, static::$triggerConfig['FromUserName'], static::$triggerConfig['ToUserName'],
                time(), $triggerData['MsgType']);
            static::setTriggerMsgTemplate();
            echo static::$triggerTemplate;die;
        }catch (\Exception $exception){
            static::$triggerTemplateStart = sprintf(static::$triggerTemplateStart, static::$triggerConfig['FromUserName'], static::$triggerConfig['ToUserName'],
                time(), 'text');
            static::$triggerData['Content'] = $exception->getMessage();
            static::setTriggerMsgTemplate();
            echo static::$triggerTemplate;die;
        }
    }


    /**
     * 主动发送模板消息
     * @inheritdoc 详细文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277
     * @param string $accessToken
     * @param string $pushTemplateId 模板ID
     * @param string $openid 用户openid
     * @param array $pushData 模板参数
     * @param string $url 模板消息链接
     * @param string $topColor 微信top颜色
     * @return array
     */
    public static function push(string $accessToken, string $pushTemplateId, string $openid, array $pushData = [],
                                string $url = '', string $topColor = '#FF0000')
    {
        // 验证微信普通token
        empty($accessToken) && $accessToken = Token::gain();

        // 检测参数
        if (empty($pushData) or empty($openid) or empty($pushTemplateId)) {
            self::error('请设置正确的参数 $triggerTemplate or $value~ !');
        }

        // 准备数据
        $pushTemplate['template_id'] = $pushTemplateId;
        $pushTemplate['touser'] = $openid;
        $pushTemplate['url'] = empty($url) ? '' : $url;
        $pushTemplate['topcolor'] = empty($topColor) ? '' : $topColor;
        $pushTemplate['data'] = $pushData;
        $send_url = str_replace('ACCESS_TOKEN', $accessToken, static::$setMsgUrl);

        // 发送请求，并返回
        return self::post($send_url, json_encode($pushTemplate, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 设置被动消息模板
     * @param string $type 属性，可选类型[text: 文本|image: 图片|voice: 语音|video: 视频|music: 音乐|news: 图文]
     * @inheritdoc 微信消息文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140543
     * @throws \Exception
     */
    protected static function setTriggerMsgTemplate()
    {
        $msgType = static::$triggerData['MsgType'];
        switch ($msgType) {
            case 'text':
                self::setTriggerTextMsgTemplate();
                break;
            case 'image':
                self::setTriggerImageMsgTemplate();
                break;
            case 'voice':
                self::setTriggerVoiceMsgTemplate();
                break;
            case 'video':
                self::setTriggerVideoMsgTemplate();
                break;
            case 'music':
                static::setTriggerMusicMsgTemplate();
                break;
            case 'news':
                static::setTriggerNewsMsgTemplate();
                break;
            default :
                self::setTriggerTextMsgTemplate();
                break;
        }
    }

    /**
     * 设置文本消息
     * @throws \Exception
     */
    private static function setTriggerTextMsgTemplate()
    {
        $msgTemplate =  '<Content><![CDATA['.static::$triggerData['Content'].']]></Content><FuncFlag>0</FuncFlag>';
        static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
    }

    /**
     * 设置图片消息
     * @throws \Exception
     */
    private static function setTriggerImageMsgTemplate()
    {
        $msgTemplate =  '<Image><MediaId>< ![CDATA['.static::$triggerData['MediaId'].']]></MediaId></Image>';
        static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
    }

    /**
     * 设置语音消息
     * @throws \Exception
     */
    private static function setTriggerVoiceMsgTemplate()
    {
        $msgTemplate =  '<Voice><MediaId>< ![CDATA['.static::$triggerData['MediaId'].']]></MediaId></Voice>';
        static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
    }

    /**
     * 设置视频消息
     * @throws \Exception
     */
    private static function setTriggerVideoMsgTemplate()
    {
        $msgTemplate =  '<Video>
                        <MediaId>< ![CDATA['.static::$triggerData['MediaId'].'] ]></MediaId>
                        <Title>< ![CDATA['.static::$triggerData['Title'].'] ]></Title>
                        <Description>< ![CDATA['.static::$triggerData['Description'].'] ]></Description>
                        </Video>';
        static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
    }

    /**
     * 设置音乐消息
     * @throws \Exception
     */
    private static function setTriggerMusicMsgTemplate()
    {
        $msgTemplate =  '<Music>
                        <Title>< ![CDATA['.static::$triggerData['Title'].'] ]></Title>
                        <Description>< ![CDATA['.static::$triggerData['Description'].'] ]></Description>
                        <MusicUrl>< ![CDATA['.static::$triggerData['MusicUrl'].'] ]></MusicUrl>
                        <HQMusicUrl>< ![CDATA['.static::$triggerData['HQMusicUrl'].'] ]></HQMusicUrl>
                        <ThumbMediaId>< ![CDATA['.static::$triggerData['ThumbMediaId'].'] ]></ThumbMediaId>
                        </Music>';
        static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
    }

    /**
     * 设置图文消息
     * @throws \Exception
     */
    private static function setTriggerNewsMsgTemplate()
    {
        $newCount = count(static::$triggerData['Articles']);
        if ($newCount < 1) throw new \Exception('图文消息发送失败，请检查数据结构~');
        try{
            $msgTemplate =  "<ArticleCount>'.$newCount.'</ArticleCount>";
            $msgTemplate .=  "<Articles>";
            foreach (static::$triggerData['Articles'] as $article)
            {
                $msgTemplate .=  '<item>
                                  <Title><![CDATA['.$article['Title'].']]></Title>
                                  <Description><![CDATA['.$article['Description'].']]></Description>
                                  <PicUrl><![CDATA['.$article['PicUrl'].']]></PicUrl>
                                  <Url><![CDATA['.$article['Url'].']]></Url>
                                  </item>';
            }
            $msgTemplate .= '</Articles>';
            static::$triggerTemplate = '<xml>'.static::$triggerTemplateStart.$msgTemplate.'</xml>';
        }catch (\Exception $exception){
            throw new \Exception('图文消息发送失败，错误信息：'.$exception->getMessage());
        }

    }
}
