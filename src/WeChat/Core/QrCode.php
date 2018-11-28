<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Core;

use Endroid\QrCode\{QrCode as EndroidQrCode, ErrorCorrectionLevel};

/**
 * Class Qrcode 二维码类
 * @package wechat
 */
class QrCode extends Base
{
    // 获取微信公众二维码
    private static $setQrCodeUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN';

    // 显示微信公众号二维码
    private static $showqrcodeUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=JSAPI_TICKET';

    /**
     * 生成二维码
     * @param string|null $text 二维码内容 默认：
     * @param string|null $filePath 二维码储存路径 默认：null
     * @param string|null $label 二维码标签 默认：null
     * @param string|null $logoPath 二维码设置logo 默认：null
     * @param int $size 二维码宽度，默认：300
     * @param int $margin 二维码点之间的间距 默认：10
     * @param string $byName 生成图片的后缀名 默认：png格式
     * @param string $encoding 编码语言，默认'UTF-8',基本不用更改
     * @param array $foregroundColor 前景色
     * @param array $backgroundColor 背景色
     * @param int $logoWidth 二维码logo宽度
     * @param int $logoHeight 二维码logo高度
     * @return bool|string  返回值
     * @throws \Endroid\QrCode\Exception\InvalidPathException
     */
    public static function create(string $text = '', string $filePath = null, string $label = null, string $logoPath = null,
                                  int $size = 300, int $margin = 15, string $byName = 'png',
                                  string $encoding = 'UTF-8', array $foregroundColor = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0],
                                  array $backgroundColor = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0],
                                  int $logoWidth = 100, int $logoHeight = 100)
    {
        try {
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
            if (!is_null($label)) {
                $qrCode->setLabel($label);
            }
            if (!is_null($logoPath)) {
                $qrCode->setLogoPath($logoPath);
                $qrCode->setLogoSize($logoWidth, $logoHeight);
            }
            $qrCode->setRoundBlockSize(true);
            $qrCode->setValidateResult(true);
            $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

            if (!is_null($filePath)) {
                // Save it to a file
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 755);
                }
                $qrCode->writeFile($filePath);
                return $filePath ? True : False;
            } else {
                // Directly output the QR code
                header('Content-Type: ' . $qrCode->getContentType());
                return $qrCode->writeString();
            }
        } catch (\WeChat\Extend\Json $exception) {
            return $exception->getMessage();
        }

    }

    /**
     * 创建微信二维码生成
     * @param string $accessToken 授权TOKEN
     * @param string $scene_str 字符串
     * @param string $scene_str_prefix  字符串前缀
     * @return array|bool|mixed
     */
    public static function wechat(string $accessToken,string $scene_str, string $scene_str_prefix = 'wene_')
    {
        $result = false;
        // 验证微信普通token
        empty($accessToken) && $accessToken = Token::gain();

        //创建加密字符
        $strLen = strlen($scene_str) + strlen($scene_str_prefix);

        // 验证字符长度
        if ($strLen <= 64 and $strLen > 1) {
            // 准备参数
            $setQrCodeUrl = str_replace('TOKEN', $accessToken, static::$setQrCodeUrl);
            $qrCodeParam = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "' . $scene_str_prefix . $scene_str . '"}}}';

            // 获取对应数据
            $result = self::post($setQrCodeUrl, $qrCodeParam);
            if (isset($result['ticket'])) {
                $result = str_replace('JSAPI_TICKET',$result['ticket'],static::$showqrcodeUrl);
            }
        }
        // 返回结果
        return $result;
    }
}
