<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/30 Time: 14:54
 */

namespace WeChat\Extend;


interface Authorize
{

    /**
     * 用户操作方法
     * @param \WeChat\Core\Authorize->returnData 返回数据数组
     * @param \WeChat\Core\Authorize->msgArray 微信数据包
     * @return mixed
     */
    public function handle();
}