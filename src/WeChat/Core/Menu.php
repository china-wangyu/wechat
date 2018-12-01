<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;


class Menu extends Base
{
    // 获取菜单
    private static $getMenuUrl = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=ACCESS_TOKEN';

    // 设置菜单
    private static $setMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';

    /**
     * 获取菜单
     * @param string $token
     * @return array|bool
     */
    public static function gain(string $token = '')
    {
        // 验证微信普通token,没有就刷新
        empty($accessToken) && $token = Token::gain();

        // 拼装获取菜单链接
        $getMenuUrl = str_replace('ACCESS_TOKEN', $token, static::$getMenuUrl);

        // 发送获取菜单，获取结果
        $result = self::get($getMenuUrl);

        // 验证结果菜单
        if ($result['errcode'] == 0) {
            return $result;
        } else {
            return false;
        }
    }


    /**
     * 设置菜单
     * @param array $menu
        例如：$menu =[
                       [
                            'type'=> 'click', //
                            'name'=> '这是第一级button',
                            'list' => [
                               [
                                    'type'=> 'miniprogram',
                                    'name'=> 'xx小程序',
                                    'url' => 'http://www.baidu.com',
                                    'appid' => 'asdasdas', 小程序APPID
                                    'pagepath' => '/page/index/index', // 小程序页面链接
                                ]
                            ],
                       ],
                        [
                            'type'=> 'miniprogram',
                            'name'=> 'xx小程序',
                            'url' => 'http://www.baidu.com',
                            'appid' => 'asdasdas', 小程序APPID
                            'pagepath' => '/page/index/index', // 小程序页面链接
                        ]
                    ];
     * @param string $token
     * @return array
     */
    public static function set(array $menu, string $token = '')
    {
        // 验证并获取微信普通token
        empty($accessToken) && $accessToken = Token::gain();
        (!is_array($menu) or count($menu) < 1) && self::error('请设置正确的参数 $menu ~ !');

        // 组装参数
        $format_param['button'] = self::format($menu);

        // 替换token
        $setMenuUrl = str_replace('ACCESS_TOKEN', $token, static::$setMenuUrl);

        // 生成菜单
        return self::post($setMenuUrl, json_encode($format_param, JSON_UNESCAPED_UNICODE));
    }


    /**
     * 格式化菜单数组
     * @param array $menu 菜单数组
     * @return array
     */
    public static function format(array $menu)
    {
        $button =[];
        foreach ($menu as $key => $val) {

            if (!isset($val['list'])) {
                $button[$key] = static::getTypeParam($val['type'],$val);
            } else {
                $button[$key]['name'] = $val['name'];
                $button[$key]['sub_button'] = static::format($val['list']);
            }
        }
        return $button;
    }


    /**
     * 获取自定义菜单参数
     * @param string $type  类型
     * @param array $item   数组
     * @return array
     */
    private static function getTypeParam(string $type,array $item)
    {
        switch (strtolower($type))
        {
            case 'click':
                return array(
                    'type' => 'click',
                    'name' => $item['name'],
                    'key' => $item['name'], // 关键词
                );
                break;
            case 'view':
                return array(
                    'type' => 'view',
                    'name' => $item['name'],
                    'url' => $item['url'],  // 原文链接
                );
                break;
            case 'miniprogram': // 小程序
                return array(
                    'type' => 'miniprogram',
                    'name' => $item['name'],    // 菜单名称
                    'url' => $item['url'],      // 小程序链接
                    'appid' => $item['appid'],      // 小程序APPID
                    'pagepath' => $item['pagepath'],    // 小程序页面路径
                );
                break;
        }
    }
}