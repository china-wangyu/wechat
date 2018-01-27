# wechat
微信基础授权、微信用户信息、微信token、微信模板、微信自定义菜单生产、微信JDK、微信关键字回复、微信模板消息发送等基础功能

> 本扩展功能的运行环境要求PHP5.6以上。

>### 使用 `git` 安装

~~~

    Github ：git@github.com:china-wangyu/wechat.git
    Coding ：git@github.com:china-wangyu/wechat.git
    码云   ：git@github.com:china-wangyu/wechat.git

~~~

>### 使用 `composer`  安装

~~~

    由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议通过下面的方式使用国内镜像。

    打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：

    composer config -g repo.packagist composer https://packagist.phpcomposer.com

    使用： 在composer.json添加

    "require": {
        "china-wangyu/wechat": "^1.0"
    },

    然后(命令行)：

    composer update

~~~


>### 使用 `源码` 安装

#### **直接下载到项目目录 `vendor/` 下，文件夹需以 `wechat` 命名，也可自行修改**




## 接口使用说明

### 接口目录

~~~
wechat         模块目录

├─WxBase.php               抽象基类，主要用户放置一些公用的方法体

├─WxUser.php               获取微信授权、用户openid、用户信息

├─WxToken.php              获取微信access_token (考虑token时限，已用 $_SESSION['access_token'] 储存)

├─WxTicket.php             获取微信jsapi_ticket、获取微信JDK签名 （考虑微信jsapi_ticket时限、已用 $_SESSION['jsapi_ticket'] 储存）

├─WxTemplate.php           获取微信所有消息模板、格式化微信消息模板 （考虑微信消息模板变量问题、及消息发送，以将需要参数存放在$变量名['param']）

├─WxSend.php               微信模板消息发送、微信关键字回复、微信自定义菜单生成
~~~


## 微信用户相关

### 微信授权、获取 `code`

~~~
    * [code 重载http,获取微信授权]
    * @param  string   $appid           [微信公众号APPID]

    \wechat\WxUser::code('微信appid');  # 重载微信授权

~~~


### 微信用户 `openid`

~~~

    * [getOpenid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \wechat\WxUser::getOpenid(input('get.code'), '微信appid', '微信appSecret');

~~~

### 微信用户信息 `userinfo` (1种： 没有获取`openid`时)

~~~

    * [getOpenid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \wechat\WxUser::getOpenid(input('get.code'), '微信appid', '微信appSecret', true);

~~~

### 微信用户信息 `userinfo` (2种： 获取`openid`时)

~~~

    * [getUserinfo 获取用户信息]
    * @param  [type] $access_token   [授权获取用户关键参数：access_token]
    * @param  [type] $openid         [用户openid]

    \wechat\WxUser::getUserinfo($access_token, $openid);

~~~


## 微信Token

### 获取 `access_token`

~~~

    * [getToken 获取微信access_token]
    * @param  string   $appid                 [微信AppID]
    * @param  string   $appSecret             [微信AppSecret]
    * @return [string] [微信access_token]

    \wechat\WxToken::getToken('微信appid', '微信appSecret');  # 获取微信access_token

~~~


## 微信Ticket

### 微信 `jsapi_ticket`

~~~

    * [getTicket 微信jsapi_ticket]
    * @param  string   $access_token          [微信token]
    * @return [string] [微信jsapi_ticket]

    \wechat\WxTicket::getTicket('微信普通token');

~~~

### 微信 `JDK` 签名

~~~

    * [getSign 获取微信JSDK]
    * @param  [string] $ticket        [获取微信JSDK签名]
    * @return [array]  [微信JSDK]

    \wechat\WxTicket::getSign('微信jsapi_ticket');

~~~

## 微信Template

### 微信 `getTemplateAll`

~~~

    * [getTemplateAll 获取所有消息模板内容]
    * @param  string $accessToken    [微信token]
    * @return [type] [description]

    \wechat\WxTemplate::getTemplateAll('微信token');

~~~

## 微信Send

### 微信 `sendKeyWord`

~~~

    * [sendKeyWord 关键字回复]
    * @param  array           $paramObj       [参数数组]
    * @param  array           $postObj        [微信对象]
    * @param  boolean         $template       [关键字模板 图文：true | 文本： false]
    * @return [string|boolen] [description]

    \wechat\WxSend::sendKeyWord($paramObj = [], $postObj = [], $template = false);

    例如：

    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    $paramObj['content'] = '来啊~';
    \wechat\WxSend::sendKeyWord($paramObj, $postObj);

~~~

### 微信 `sendMsg`

~~~

    * [sendMsg 发送模板消息]
    * @param  string $accessToken [微信token]
    * @param  string $templateid [模板ID]
    * @param  string $openid     [用户openid]
    * @param  array  $data       [模板参数]
    * @param  string $url        [模板消息链接]
    * @param  string $topcolor   [微信top颜色]
    * @return [ajax] [boolen]

    \wechat\WxSend::sendMsg($accessToken = '', $templateid = '', $openid = '', $data = [], $url = '', $topcolor = '#FF0000');

~~~

### 微信 `sendMenu`

~~~

    * [send_menu 生成菜单]
    * @param  string $accessToken [微信token]
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

    \wechat\WxSend::sendMenu($accessToken = '', $menu = []);

~~~



>| 注：如有疑问，请联系邮箱 china_wangyu@aliyun.com


>| 或，请联系QQ 354007048 / 354937820