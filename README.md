# WeChat

- 微信基础授权
- 微信用户信息
- 微信token
- 微信模板
- 微信自定义菜单生产
- 微信JDK
- 微信关键字回复
- 微信模板消息发送
- 基础功能

> 本扩展功能的运行环境要求`PHP5.6`以上。
> 本扩展 `1.0.5` 及以上版本，运行环境要求`PHP7.2`以上。

>### 使用 `git` 安装

~~~

    码云   ：git@github.com:china-wangyu/WeChat.git

~~~

>### 使用 `composer`  安装

#### 由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议通过下面的方式使用国内镜像。打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：
~~~

    composer config -g repo.packagist composer https://packagist.phpcomposer.com
~~~

#### 使用： 在composer.json添加

    "require": {
        "china-wangyu/WeChat": "^1.0.0"
    },

#### 然后(命令行)：

    composer update


## 接口使用说明

### 接口目录

~~~
WeChat         模块目录

├─ Core        核心目录
        ├─Base.php               抽象基类，主要用户放置一些公用的方法体
        
        ├─User.php               获取微信授权、用户openid、用户信息
        
        ├─Token.php              获取微信access_token (考虑token时限，已用 $_SESSION['access_token'] 储存)
        
        ├─Ticket.php             获取微信jsapi_ticket、获取微信JDK签名 （考虑微信jsapi_ticket时限、已用 $_SESSION['jsapi_ticket'] 储存）
        
        ├─Template.php           获取微信所有消息模板、格式化微信消息模板 （考虑微信消息模板变量问题、及消息发送，以将需要参数存放在$变量名['param']）
        
        ├─Send.php               微信模板消息发送、微信关键字回复、微信自定义菜单生成
        
        ├─QrCode.php             微信生成二维码
        
        ├─Menu.php               微信菜单
        
├─ Extend         依赖目录

         ├─File.php                 文件存储类。
                
         ├─Json.php                 Json返回类。
        
         ├─Request.php              curl请求封装类。
         
         ├─Tool.php              工具类。
~~~


## 微信用户 `User`

### 微信授权、获取 `code`
~~~
    * [code 重载http,获取微信授权]
    * @param  string   $appid           [微信公众号APPID]

    \WeChat\Core\User::code('微信appid');  # 重载微信授权
~~~


### 微信用户 `openid`
~~~

    * [openid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \WeChat\Core\User::openid(input('get.code'), '微信appid', '微信appSecret');
~~~


### 微信用户信息 `userinfo` (1种： 没有获取`openid`时)
~~~

    * [openid 获取用户 OPENID]
    * @param  string  $code                         [微信授权CODE]
    * @param  string  $appid                        [微信appid]
    * @param  string  $appSecret                    [微信appSecret]
    * @param  boolen  $type                         [true:获取用户信息  false:用户openid]
    * @return [array] [用户信息 用户openid]

    \WeChat\Core\User::openid('获取GET方式的参数code', '微信appid', '微信appSecret', true);
~~~


### 微信用户信息 `userinfo` (2种： 获取`openid`时)
~~~

    * [userInfo 获取用户信息]
    * @param  [type] $access_token   [授权获取用户关键参数：access_token]
    * @param  [type] $openid         [用户openid]

    \WeChat\Core\User::userInfo($access_token, $openid);
~~~

### 微信用户信息 `newuserinfo` (3种： 获取`access_token`时)
~~~

    * [userInfo 获取用户信息]
    * @param  [type] $access_token   [普通access_token]
    * @param  [type] $openid         [用户openid]

    \WeChat\Core\User::newUserinfo($access_token, $openid);
~~~


## 微信 `Token`

### 获取 `access_token`
~~~

    * [gain 获取微信access_token]
    * @param  string   $appid                 [微信AppID]
    * @param  string   $appSecret             [微信AppSecret]
    * @return [string] [微信access_token]

    \WeChat\Core\Token::gain('微信appid', '微信appSecret');  # 获取微信access_token
~~~


## 微信 `Ticket`

### 微信 `jsapi_ticket`
~~~

    * [gain 微信jsapi_ticket]
    * @param  string   $access_token          [微信token]
    * @return [string] [微信jsapi_ticket]

    \WeChat\Core\Ticket::gain('微信普通token');
~~~


### 微信 `JDK` 签名
~~~

    * [sign 获取微信JSDK]
    * @param  [string] $ticket        [获取微信JSDK签名]
    * @return [array]  [微信JSDK]

    \WeChat\Core\Ticket::sign('微信jsapi_ticket');
~~~


## 微信模板消息 `Template`

### 获取所有模板 `gain`
~~~

    * [gain 获取所有消息模板内容]
    * @param  string $accessToken    [微信token]
    * @return [type] [description]

    \WeChat\Core\Template::gain('微信token');
~~~


## 微信推送 `Send`

### 关键字推送 `keyWord`
~~~

    * [keyWord 关键字回复]
    * @param  array           $paramObj       [参数数组]
    * @param  array           $postObj        [微信对象]
    * @param  boolean         $template       [关键字模板 图文：true | 文本： false]
    * @return [string|boolen] [description]

    \WeChat\Core\Send::keyWord($paramObj = [], $postObj = [], $template = false);

    例如：

    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', ExtendXML_NOCDATA);
    $paramObj['content'] = '来啊~';
    \WeChat\Core\Send::keyWord($paramObj, $postObj);
~~~


### 模板消息推送 `msg`
~~~

    * [msg 发送模板消息]
    * @param  string $accessToken [微信token]
    * @param  string $templateid [模板ID]
    * @param  string $openid     [用户openid]
    * @param  array  $data       [模板参数]
    * @param  string $url        [模板消息链接]
    * @param  string $topcolor   [微信top颜色]
    * @return [ajax] [boolen]

    \WeChat\Core\Send::msg($accessToken = '', $templateid = '', $openid = '', $data = [], $url = '', $topcolor = '#FF0000');
~~~

## 微信菜单 `Menu`

### 获取菜单 `gain`
~~~

    * [gain 获取菜单]
    * @param  string $accessToken [微信token]                              [菜单内容 ]
    * @return [array] [微信返回值：状态值数组]

    \WeChat\Core\Menu::gain($accessToken = '', $menu = []);
~~~


### 设置菜单 `set`
~~~

    * [set 生成菜单]
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

    \WeChat\Core\Menu::set( array $menu = [], $accessToken = '');
~~~


## 二维码 `Qrcode`

### 微信带参二维码 `wechat`
~~~

    /**
     * 创建微信二维码生成
     * @param string $accessToken 授权TOKEN
     * @param string $scene_str 字符串
     * @param string $scene_str_prefix  字符串前缀
     * @return array|bool|string
     */
    \WeChat\Core\QrCode::wechat(string $accessToken,string $scene_str, string $scene_str_prefix = 'wene_')
~~~


### 创建二维码 `create`
~~~

     /**
          * 生成二维码
          * @param string|null $text  二维码内容 默认：
          * @param string|null $label    二维码标签 默认：null
          * @param string|null $filePath 二维码储存路径 默认：null
          * @param string|null $logoPath 二维码设置logo 默认：null
          * @param int $size     二维码宽度，默认：300
          * @param int $margin   二维码点之间的间距 默认：10
          * @param string $byName    生成图片的后缀名 默认：png格式
          * @param string $encoding  编码语言，默认'UTF-8',基本不用更改
          * @param array $foregroundColor    前景色
          * @param array $backgroundColor    背景色
          * @param int $logoWidth    二维码logo宽度
          * @param int $logoHeight   二维码logo高度
          * @return bool|string  返回值
          * @throws \Endroid\QrCode\Exception\InvalidPathException
          */
         public static function create(string $text = '', string $label = null, string $filePath = null,
                                       string $logoPath = null, int $size = 300, int $margin = 15, string $byName = 'png',
                                       string $encoding = 'UTF-8',array $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0],
                                       array $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0],
                                       int $logoWidth = 100,int $logoHeight = 100)
                                       
         使用方式：
         
            1. 生成二维码，但不生成二维码文件
            $qrocde = \WeChat\Core\QrCode::create('二维码内容');
            
            2. 生成二维码文件
            $qrocde = \WeChat\Core\QrCode::create('二维码内容','文件存放路径');
~~~


##  文件参数储存 `File`
~~~

      * 文件参数储存，可扩展
      * @param string $var  key
      * @param array $val value
    
    // 存值
    \WeChat\Extend\File::param('key','value');
    
    // 取值
    \WeChat\Extend\File::param('key');
~~~


>| 注：如有疑问，请联系邮箱 china_wangyu@aliyun.com


>| 或，请联系QQ 354007048 / 354937820