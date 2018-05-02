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
 * @version 1.0.3
 * @authors wene (china_wangyu@aliyun.com)
 */
namespace wechat;

require_once '/lib/phpqrcode.php';

class Qrcode extends WxBase
{

    /**
     * [file 生成二维码文件]
     * @param  string $fileDir [文件目录]
     * @param  string $url      [二维码网址]
     * @param  array  $param    [二维码参数]
     * @return [string]           [二维码地址]
     */
    public static function file($fileDir = '', $url = '', $param = [])
    {
        empty($fileDir) && \wechat\lib\Abnormal::error('文件路径不能为空~！');
        is_dir($fileDir) ? '' : mkdir($fileDir, 0755);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            \wechat\lib\Abnormal::error('URL格式不正确~！');
        }
        $filepath = $fileDir . '/' . time() . '.jpg';
        switch ($param) {
            case count($param) == 0 or !is_array($param):
                QRcode::png(urlencode($url), $filepath);
                break;
            default:
                QRcode::png(urlencode($url . '?' . self::ToUrlParams($param)), $filepath, '', 5);
                break;
        }
        return $filepath;
    }

    /**
     * [url 生成二维码链接]
     * @param  [type] $url    [二维码网址]
     * @param  array  $param  [二维码参数]
     * @param  string $width  [二维码宽度]
     * @param  string $height [二维码高度]
     * @return [string]         [参数加密后的二维码链接]
     */
    public static function url($url = '', $param = [], $width = '300', $height = '300')
    {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            \wechat\lib\Abnormal::error('URL格式不正确~！');
        }
        is_array($param) && $url .= '?' . self::ToUrlParams($param);
        return 'http://pan.baidu.com/share/qrcode?w=' . $width . '&h=' . $height . '&url=' . urlencode($url);
    }

    /**
     * [html 生成二维码html <img src=''> 标签]
     * @param  [type] $url    [二维码网址]
     * @param  array  $param  [二维码参数]
     * @param  string $width  [二维码宽度]
     * @param  string $height [二维码高度]
     * @return [string]         [二维码<img src=''> 标签]
     */
    public static function html($url = '', $param = [], $width = '300', $height = '300')
    {
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            \wechat\lib\Abnormal::error('URL格式不正确~！');
        }
        is_array($param) && $url .= '?' . self::ToUrlParams($param);
        return '<img alt="二维码" src="http://pan.baidu.com/share/qrcode?w=' . $width . '&h=' . $height . '&url=' . urlencode($url) . ' />"';
    }

}
