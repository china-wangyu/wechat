<?php
/**
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
 * @date    2018-01-31 17:13:04
 * @version 1.0
 * @authors wene (china_wangyu@aliyun.com)
 */
namespace wechat;

require_once '/lib/phpqrcode.php';

class Qrcode extends WxBase
{

    public static function file($filepath = '', $url = '', $param = [])
    {
        empty($filepath) ? self::json(400, '@ 文件路径不能为空~！') : '';
        $filedir = dirname($filepath);
        is_dir($filedir) ? '' : mkdir($filedir, 0755);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            self::json(400, '@ URL格式不正确~！');
        }
        switch ($param) {
            case count($param) == 0 or !is_array($param):
                QRcode::png($url, $filepath);
                .
                break;
            default:
                QRcode::png($url . '/' . self::ToUrlParams($param), $filepath, '', 5);
                break;
        }
    }

    public static function url($url, $param = [], $width = '300', $height = '300')
    {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            self::json(400, '@ URL格式不正确~！');
        }
        switch ($param) {
            case count($param) == 0 or !is_array($param):
                break;
            default:
                $url .= '?' . self::ToUrlParams($param);
                break;
        }
        $imgUrl = 'http://pan.baidu.com/share/qrcode?w=' . $width . '&h=' . $height . '&url=' . urlencode($url);
        return $imgUrl;
    }

    public static function div($url, $param = [], $width = '300', $height = '300')
    {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            self::json(400, '@ URL格式不正确~！');
        }
        switch ($param) {
            case count($param) == 0 or !is_array($param):
                break;
            default:
                $url .= '?' . self::ToUrlParams($param);
                break;
        }
        $imgDiv = '<img alt="二维码" src="http://pan.baidu.com/share/qrcode?w=' . $width . '&h=' . $height . '&url=' . urlencode($url) . ' />"';
        return $imgDiv;
    }

}
