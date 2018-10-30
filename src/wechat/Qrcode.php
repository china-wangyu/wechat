<?php
/**
 * Created by wene. Date: 2018/9/20
 */
namespace wechat;

use Endroid\QrCode\{QrCode as EndroidQrCode,LabelAlignment,ErrorCorrectionLevel};
use Endroid\QrCode\Response\QrCodeResponse;

/**
 * Class Qrcode 二维码类
 * @package wechat
 */
class Qrcode extends EndroidQrCode
{
    protected $margin = 10;
    protected $ext = 'png';
    protected $width = 300;
    protected $height = 300;
    protected $content = 'png';

    protected $encoding = 'UTF-8';
    protected $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
    protected $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];
    protected $saveFile = __DIR__.'/qrcode.png';

    protected $logoPath = __DIR__.'/qrcode.png';
    protected $logoWidth = 150;
    protected $logoHeight = 200;

    public function __construct(string $text = '')
    {
        parent::__construct($text);
    }

    public static final function style(string $text = '')
    {
        // Create a basic QR code
        $qrCode = new static($text);
        $qrCode->setSize($qrCode->width);

        // Set advanced options
        $qrCode->setWriterByName($qrCode->ext);
        $qrCode->setMargin($qrCode->margin);
        $qrCode->setEncoding($qrCode->encoding);
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setForegroundColor($qrCode->foregroundColor);
        $qrCode->setBackgroundColor($qrCode->backgroundColor);
        $qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
        $qrCode->setLogoPath($qrCode->logoPath);
        $qrCode->setLogoSize($qrCode->logoWidth, $qrCode->logoHeight);
        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        // Directly output the QR code
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();

        // Save it to a file
        $qrCode->writeFile(__DIR__.'/qrcode.png');

        // Create a response object
        $response = new QrCodeResponse($qrCode);
    }

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
