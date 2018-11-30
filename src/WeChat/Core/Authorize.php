<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/29 Time: 16:43
 */

namespace WeChat\Core;


class Authorize extends Base implements \WeChat\Extend\Authorize
{

    /**
     * 设置微信的认证字符
     * @var string $token
     */
    protected $token = 'WangYu';

    /**
     * 微信的数据集合
     * @var $config
     */
    protected $config;

    /**
     * 返回用户数据格式集合
     * @var array $returnTypes
     */
    protected $returnTypes = ['text','image','voice','video','music','news'];

    /**
     * 回复用户的消息数据
     * @var array $returnData
     */
    protected $returnData = array(
        'MsgType' => 'text',  // 可选类型[text: 文本|image: 图片|voice: 语音|video: 视频|music: 音乐|news: 图文]
        'Title' => '',  // 标题
        'Content' => '',    // 	回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
        'PicUrl' => '',     // 图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
        'Url' => '',    // 点击图文消息跳转链接
        'MediaId' => '',    // 	通过素材管理中的接口上传多媒体文件，得到的id。
        'Description' => '',    // 	视频消息的描述
        'MusicURL' => '',   // 音乐链接
        'HQMusicUrl' => '',     // 	高质量音乐链接，WIFI环境优先使用该链接播放音乐
        'ThumbMediaId' => '',   // 缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
        'ArticleCount' => '',   // 图文消息个数；当用户发送文本、图片、视频、图文、地理位置这五种消息时，开发者只能回复1条图文消息；其余场景最多可回复8条图文消息
        'Articles' => '',   // 图文消息信息，注意，如果图文数超过限制，则将只发限制内的条数
    );

    /**
     * 设置与微信对接的TOKEN凭证字符
     * Authorize constructor.
     * @param string $token
     */
    public function __construct(string $token = '')
    {
        if (!empty($token)) $this->token = $token;
    }

    /**
     * 微信授权
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 这里填写的是你在微信上设置的TOKEN，但是必须保证与微信公众平台-接口配置信息一致
        $echoStr = $_REQUEST['echostr'];
        if (!isset($echoStr)) {
            $this->responseMsg();
        } else {
            $this->valid();
        }

    }

    /**
     * 若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，否则接入失败。
     */
    private function valid()
    {
        $echoStr = $_REQUEST['echostr'];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    /**
     * 开发者通过检验signature对请求进行校验
     * @return bool
     */
    private function checkSignature()
    {
        $tmpArr = array($this->token, trim($_REQUEST['timestamp']), trim($_REQUEST['nonce']));
        sort($tmpArr);
        $tmpStr = sha1(implode($tmpArr));
        return ($tmpStr == trim($_REQUEST['signature'])) ? true: false;
    }

    /**
     * 公众号的消息推送，回复
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function responseMsg()
    {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->config = json_decode(json_encode($postObj), true); // 微信提醒数组
            $paramObj = [];  // 消息数组
            $resToken = Token::gain(config('DHKJ_WECHAT.app_id'), config('DHKJ_WECHAT.app_secret')); // token
            $userInfo = User::newUserInfo($resToken, $this->config['FromUserName']); // 微信用户信息
            switch ($this->config) {
                case  $this->config['MsgType'] == 'event' :
                    $this->sendEventMsg($paramObj, $this->config, $userInfo);
                    break;
                case !empty(trim($this->config['Content'])):
                    $this->sendKeywordMsg($paramObj, $this->config, $userInfo);
                    break;
            }
        } else {
            echo '';
            exit;
        }
    }

    /**
     * 微信事件推送器
     * @param $paramObj
     * @param $postArr
     * @param $userInfo
     */
    private function sendEventMsg($paramObj, $postArr, $userInfo)
    {
        $params = explode('_', trim($postArr['EventKey']));
        switch ($postArr['Event']) {
            case $postArr['Event'] == 'subscribe' and !isset($params[1]):
                $paramObj['content'] = '您好，感谢您关注都汇康健

都汇康健是国内领先的大健康管理及慢病教育运营商，为广大亚健康人群
及慢病人群提供优质的健康管理服务和慢病教育服务。

我们在体重管理、睡眠管理、健脑益智、心脑血管等众多领域为国人提供专业的医学教育及科普服务，通过系统的慢病解决方案及专业的医学级健康产品，持续为广大国人创造健康价值，提升生活品质。

健康管理咨询：400-870-9690';
                Send::keyWord($paramObj, $postArr);
                break;
            case $postArr['Event'] == 'subscribe' and isset($params[1]):
                $paramObj['content'] = '扫码关注' . json_encode($postArr);
                Send::keyWord($paramObj, $postArr);
                break;
            case 'user_scan_product_enter_session':
                $paramObj['content'] = '用户商品扫码' . json_encode($postArr);
                Send::keyWord($paramObj, $postArr);
                break;
            case 'CLICK':
                $this->sendClickMsg($paramObj, $postArr, $userInfo);
                break;
            case 'SCAN':
                $paramObj['content'] = '扫码进入' . json_encode($postArr);
                Send::keyWord($paramObj, $postArr);
                break;
        }
    }

    /**
     * 发送关键字消息
     * @param $paramObj
     * @param $postArr
     * @param $userInfo
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function sendKeywordMsg($paramObj, $postArr, $userInfo)
    {
        $type = 2;
        try {
            $keywordInfo = DoctorWxMenu::where(['name' => trim($postArr['Content'])])
                ->order('id desc')
                ->limit(1)
                ->find();
            if (empty($keywordInfo)) {
                $paramObj['content'] = '谢谢您的关心陪伴，系统升级中，给你带来的不便请见谅，爱你，么么哒~' . json_encode($userInfo);
            } else {
                if ($keywordInfo['ctype'] == 1 and !empty($keywordInfo['click_img'])) {
                    $paramObj['title'] = isset($keywordInfo['name']) ? '' : $keywordInfo['name'];
                    $paramObj['content'] = isset($keywordInfo['click_content']) ? '' : $keywordInfo['click_content'] . json_encode($userInfo);
                    $paramObj['imgurl'] = isset($keywordInfo['click_img']) ? '' : $keywordInfo['click_img'];
                    $paramObj['jumpurl'] = isset($keywordInfo['click_url']) ? '' : $keywordInfo['click_url'];
                    $type = $keywordInfo['ctype'] == 1 and !empty($keywordInfo['click_img']) ? 1 : 2;
                } else {
                    $paramObj['content'] = '谢谢您的关心陪伴，系统升级中，给你带来的不便请见谅，爱你，么么哒~' . json_encode($userInfo);
                }
            }
        } catch (\Exception $exception) {
            $paramObj['content'] = '谢谢您的关心陪伴，系统升级中，给你带来的不便请见谅，爱你，么么哒~' . json_encode($userInfo);
        }
        Send::keyWord($paramObj, $postArr, $type);
    }

    /**
     * 点击事件发送消息
     * @param $paramObj
     * @param $postArr
     * @param $userInfo
     */
    private function sendClickMsg($paramObj, $postArr, $userInfo)
    {
        $type = 2;
        try {
            $keywordInfo = DoctorWxMenu::where(['name' => trim($postArr['EventKey'])])
                ->order('id desc')
                ->limit(1)
                ->find();
            if (!empty($keywordInfo)) {
                if ($keywordInfo['ctype'] == 1 and !empty($keywordInfo['click_img'])) {
                    $paramObj['title'] = isset($keywordInfo['name']) ? '' : $keywordInfo['name'];
                    $paramObj['content'] = isset($keywordInfo['click_content']) ? '' : $keywordInfo['click_content'] . json_encode($userInfo);
                    $paramObj['imgurl'] = isset($keywordInfo['click_img']) ? '' : $keywordInfo['click_img'];
                    $paramObj['jumpurl'] = isset($keywordInfo['click_url']) ? '' : $keywordInfo['click_url'];
                    $type = $keywordInfo['ctype'] == 1 and !empty($keywordInfo['click_img']) ? 1 : 2;
                } else {
                    $paramObj['content'] = '谢谢您的关心陪伴，系统升级中，给你带来的不便请见谅，爱你，么么哒~' . json_encode($userInfo);
                }
                Send::keyWord($paramObj, $postArr, $type);
            }
        } catch (\Exception $exception) {
            $paramObj['content'] = '谢谢您的关心陪伴，系统升级中，给你带来的不便请见谅，爱你，么么哒~' . json_encode($userInfo);
            Send::keyWord($paramObj, $postArr, $type);
        }
    }

    /**
     * 用户操作方法
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->config 微信数据包
     * @return mixed
     */
    public function handle()
    {
        // TODO: Implement handle() method.
    }


    /**
     * 发送文本消息
     * @param string $content 回复的文本内容
     */
    protected function text(string $content = '这是个友好的回复~')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['Content'] = $content;
    }

    /**
     * 发送图片消息
     * @param string $mediaId 素材ID
     */
    protected function image(string $mediaId)
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
    }

    /**
     * 发送语音消息
     * @param string $mediaId 素材ID
     */
    protected function voice(string $mediaId)
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
    }

    /**
     * 发送视频消息
     * @param string $mediaId 素材ID
     * @param string $title 视频标题
     * @param string $description   视频消息的描述
     */
    protected function video(string $mediaId,string $title = '这是一个标题',string $description = '消息的描述')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
        $this->returnData['Title'] = $title;
        $this->returnData['Description'] = $description;
    }

    /**
     * 发送音乐消息
     * @param string $mediaId
     * @param string $title
     * @param string $description
     * @param string $musicURL
     * @param string $HQMusicUrl
     * @param string $ThumbMediaId
     */
    protected function music(string $mediaId,string $title = '这是一个标题',string $description = '消息的描述',
                             string $musicURL = '', string $HQMusicUrl = '', string $ThumbMediaId = '')
    {
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['MediaId'] = $mediaId;
        $this->returnData['Title'] = $title;
        $this->returnData['Description'] = $description;
        $this->returnData['MusicURL'] = $musicURL;
        $this->returnData['HQMusicUrl'] = $HQMusicUrl;
        $this->returnData['ThumbMediaId'] = $ThumbMediaId;
    }


    /**
     * 发送图文消息
     * @param array $Articles
     */
    protected function news(array $Articles = [])
    {
        if (!isset($Articles[0]['Title'])) {
            echo '';
            die;
        }
        $this->returnData['MsgType'] = __FUNCTION__;
        $this->returnData['ArticleCount'] = count($Articles);
        $this->returnData['Articles'] = $Articles;
    }



}