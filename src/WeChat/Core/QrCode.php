<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace WeChat\Core;

use Endroid\QrCode\{QrCode as EndroidQrCode, ErrorCorrectionLevel};

/**
 * Class Qrcode 二维码类
 * @package wechat
 */
class QrCode extends Base
{

    /**
     * 生成二维码
     * @param string|null $text  二维码内容 默认：
     * @param string|null $filePath 二维码储存路径 默认：null
     * @param string|null $label    二维码标签 默认：null
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
    public static function create(string $text = '', string $filePath = null, string $label = null, string $logoPath = null,
                                  int $size = 300, int $margin = 15, string $byName = 'png',
                                  string $encoding = 'UTF-8',array $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0],
                                  array $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0],
                                  int $logoWidth = 100,int $logoHeight = 100)
    {
        try{
            // Create a basic QR code
            $qrCode = new EndroidQrCode($text);

            // Set advanced options
            $qrCode->setSize($size);
            $qrCode->setWriterByName($byName); // File suffix name
            $qrCode->setMargin($margin);
            $qrCode->setEncoding($encoding);
            $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
            $qrCode->setForegroundColor($foregroundColor);
            $qrCode->setBackgroundColor($backgroundColor);
            if (!is_null($label)){
                $qrCode->setLabel($label);
            }
            if (!is_null($logoPath)){
                $qrCode->setLogoPath($logoPath);
                $qrCode->setLogoSize($logoWidth, $logoHeight);
            }
            $qrCode->setRoundBlockSize(true);
            $qrCode->setValidateResult(true);
            $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

            if (!is_null($filePath)){
                // Save it to a file
                if (!is_dir(dirname($filePath))){
                    mkdir(dirname($filePath),755);
                }
                $qrCode->writeFile($filePath);
                return $filePath ? True : False;
            }else{
                // Directly output the QR code
                header('Content-Type: ' . $qrCode->getContentType());
                return $qrCode->writeString();
            }
        }catch (\WeChat\Extend\Json $exception){
            return $exception->getMessage();
        }

    }


    /**
     * [url 生成二维码链接]
     * @param  [type] $url    [二维码网址]
     * @param  array $param [二维码参数]
     * @param  string $width [二维码宽度]
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
     * @param  array $param [二维码参数]
     * @param  string $width [二维码宽度]
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
