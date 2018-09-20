<?php
/**
 * Created by wene. Date: 2018/9/20
 */
namespace wechat;

require_once '/lib/phpqrcode.php';

/**
 * Class Qrcode 二维码类
 * @package wechat
 */
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
        empty($fileDir) && self::error('文件路径不能为空~！');
        is_dir($fileDir) ? '' : mkdir($fileDir, 0755);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%
    =~_|]/i", $url)) {
            self::error('URL格式不正确~！');
        }
        $filepath = $fileDir . '/' . time() . '.jpg';
        switch ($param) {
            case count($param) == 0 or !is_array($param):
                QRcode::png(urlencode($url), $filepath);
                break;
            default:
                QRcode::png(urlencode($url . '?' . self::url_splice_array($param)), $filepath, '', 5);
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
            self::error('URL格式不正确~！');
        }
        is_array($param) && $url .= '?' . self::url_splice_array($param);
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
            self::error('URL格式不正确~！');
        }
        is_array($param) && $url .= '?' . self::url_splice_array($param);
        return '<img alt="二维码" src="http://pan.baidu.com/share/qrcode?w=' . $width . '&h=' . $height . '&url=' . urlencode($url) . ' />"';
    }

}
