<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/12/1 Time: 11:25
 */

namespace WeChat\Core;


/**
 * Class Material 微信素材类
 * @package WeChat\Core
 */
class Material extends Base
{
    // 获取素材总数
    private static $getMaterialCount = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=ACCESS_TOKEN';

    // 获取素材列表
    private static $getMaterialList = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=ACCESS_TOKEN';

    /**
     * 获取素材总数
     * @param string $access_token 普通授权token
     * @return array
     */
    public function getMaterialCount(string $access_token)
    {
        static::$getMaterialCount = str_replace('ACCESS_TOKEN',$access_token,static::$getMaterialCount);
        return self::get(static::$getMaterialCount);
    }

    /**
     * 获取素材列表
     * @param string $access_token 普通授权token
     * @return array
     */
    public function getMaterialList(string $access_token)
    {
        static::$getMaterialList = str_replace('ACCESS_TOKEN',$access_token,static::$getMaterialList);
        return self::get(static::$getMaterialList);
    }
}