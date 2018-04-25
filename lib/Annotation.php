<?php

/**
 * 反射
 * @authors china_wangyu (china_wangyu@aliyun.com)
 * @date    2018-04-22 16:36:00
 * @version 1.0.2
 *
 *  *  ** 求职区 **
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

class Annotation
{
    private $reflectionMethod;
    private $must_name     = 'must';
    private $optional_name = 'optional';

    /**
     * [__construct ]
     * @param [type] $className [类名]
     * @param [type] $funcName  [方法名]
     */
    public function __construct($className, $funcName)
    {
        $reflectionClass = new \ReflectionClass($className);
        try {
            $this->reflectionMethod = $reflectionClass->getMethod($funcName);
        } catch (\Exception $e) {
            \wechat\lib\Abnormal::error("无效的请求方法名");
        }
    }

    /**
     * [tagList description]
     * @return [type] [description]
     */
    public function tagList()
    {
        $document = trim($this->reflectionMethod->getDocComment());
        $param    = [];
        if (!empty($document)) {
            //Must param
            preg_match("#@" . $this->must_name . "\(([^\(\)]*)\)#", $document, $must);
            if (!empty($must)) {
                $param['mustParam'] = $must[1];
            }

            //optional param
            preg_match("#@" . $this->optional_name . "\(([^\(\)]*)\)#", $document, $optional);
            if (!empty($optional)) {
                $param['optionalParam'] = $optional[1];
            }
            return $param;
        }
    }

}
