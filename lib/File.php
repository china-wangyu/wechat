<?php
/**
 * 微信存储类
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:36:00
 * @version 1.0.3
 *
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
 */

namespace wechat\lib;

class File
{

    /**
     * 定义常量 / 路径连接符
     */
    const EXT = '/';

    /**
     * 存储对象文件，可扩展
     * @param string $var  key
     * @param array $val value
     * @return mixed
     */
    public static function param(string $var, array $val = [])
    {
        $file_path = self::mkdir('param');
        $fileCont = json_decode(file_get_contents($file_path), true);
        switch ($fileCont) {
            case empty($fileCont) and empty($val):
                return null;
                break;
            case isset($fileCont[$var]) and !empty($val):
                $val['time'] = time();
                $fileCont[$var] = $val;
                file_put_contents($file_path, json_encode($fileCont));
                break;
            case isset($fileCont[$var]) and empty($val):
                $returnObj = $fileCont[$var];
                return $returnObj['time'] - time() <= 7100 ? $returnObj : null;
                break;
            case !isset($fileCont[$var]) and !empty($val):
                $val['time'] = time();
                $fileCont[$var] = $val;
                file_put_contents($file_path,json_encode($fileCont));
                break;
        }
    }

    /**
     * 支付日志
     * [paylog description]
     * @return [type] [description]
     */
    public static function paylog(string $type = 'wechat', array $param = [])
    {
        $file_path = self::mkdir('wechat');
        if (!empty($type) and empty($param)) {
            return json_decode(file_get_contents($file_path), true);
        }
        $data = '['.date('Y-m-d H:i:s').'] => '.json_encode($param) . PHP_EOL;
        file_put_contents($file_path, $data, FILE_APPEND);
    }

    /**
     * [mkdir 创建日志类型文件]
     * @param  string $type [description]
     * @return [type]       [description]
     */
    private static function mkdir(string $type = 'param')
    {
        $file_dir = dirname(__FILE__) . EXT . 'log' . EXT . date('Y-m-d-H') . EXT;
        if ($type == 'param') {
            $file_dir = dirname(__FILE__) . EXT . 'log' . EXT . 'param' . EXT;
        }

        $file_name = $type . '.log';
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0775);
        }

        if (!is_file($file_dir . $file_name)) {
            file_put_contents($file_dir . $file_name, '');
        }
        return $file_dir . $file_name;
    }
}
