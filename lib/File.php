<?php
/**
 * 微信存储类
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:36:00
 * @version 1.0.2
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
     * 存储对象文件，可扩展
     * @param string $var  key
     * @param array $val value
     * @return mixed
     */
    public  static function param(string $var,array $val = [])
    {
        $file_dir = dirname(__FILE__).'/log/';
        $file_name = 'param.log';
        if (!is_dir($file_dir)) mkdir($file_dir,0775);
        if (!is_file($file_dir.$file_name)) file_put_contents($file_dir.$file_name,'');
        $fileCont = json_decode(file_get_contents($file_dir.$file_name),true);
        switch ($fileCont){
            case empty($fileCont) and empty($val):
                return null;
                break;
            case isset($fileCont[$var]) and !empty($val):
                $val['time'] = time();
                $fileCont[$var] = $val;
                file_put_contents($file_dir.$file_name,json_encode($fileCont));
                break;
            case isset($fileCont[$var]) and empty($val):
                $returnObj = $fileCont[$var];
                return  $returnObj['time'] - time() <= 7100 ? $returnObj : null;
                break;
        }
    }
}