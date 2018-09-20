<?php
/**
 * Created by wene. Date: 2018/9/20
 */

namespace wechat\lib;

/**
 * Class File 微信存储类
 * @package wechat\lib
 */
class File
{

    /**
     * 定义常量 / 路径连接符
     */
    private static $ext = '/';

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
        $file_dir = dirname(__FILE__) . static::$ext . 'log' ;
        !is_dir($file_dir) &&  mkdir($file_dir, 0775);
        $file_dir .=  static::$ext . date('Y-m-d-H') . static::$ext;
        if ($type == 'param') {
            $file_dir = dirname(__FILE__) . static::$ext . 'log' . static::$ext . 'param' . static::$ext;
        }

        $file_name = $type . '.log';
        !is_dir($file_dir) && mkdir($file_dir, 0775);

        if (!is_file($file_dir . $file_name)) {
            file_put_contents($file_dir . $file_name, '');
        }
        return $file_dir . $file_name;
    }
}
