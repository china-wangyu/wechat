<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/12/1 Time: 14:21
 */

class Wechat extends \WeChat\Core\Authorize
{
    // 初始化设置微信开发模式TOKEN
    public function __construct(string $token = 'TOKEN')
    {
        parent::__construct($token);
    }

    /**
     * 首次关注事件
     * @return mixed|void
     */
    public function follow()
    {
        // TODO: Implement follow() method.
        $sendMsg = '您好，感谢您关注都汇康健

都汇康健是国内领先的大健康管理及慢病教育运营商，为广大亚健康人群
及慢病人群提供优质的健康管理服务和慢病教育服务。

我们在体重管理、睡眠管理、健脑益智、心脑血管等众多领域为国人提供专业的医学教育及科普服务，通过系统的慢病解决方案及专业的医学级健康产品，持续为广大国人创造健康价值，提升生活品质。

健康管理咨询：400-870-9690';
        $this->text($sendMsg);
    }

    /**
     * 扫码关注事件
     * @return mixed|void
     */
    public function scanFollow()
    {
        // TODO: Implement scanFollow() method.
        $this->text('扫码关注' . json_encode($this->config));
    }

    /**
     * 点击事件
     * @return mixed|void
     */
    public function click()
    {
        // TODO: Implement click() method.
        $this->text('这个是用户点击事件~'. json_encode($this->config));
    }

    /**
     * 扫码商品事件
     * @return mixed|void
     */
    public function scanProduct()
    {
        // TODO: Implement scanProduct() method.
        $this->text('用户商品扫码' . json_encode($this->config));
    }

    /**
     * 扫码事件
     * @return mixed|void
     */
    public function scan()
    {
        // TODO: Implement scan() method.
        $this->text('扫码进入' . json_encode($this->config));
    }

    /**
     * 用户输入
     * @return mixed|void
     */
    public function input()
    {
        // TODO: Implement input() method.
        $this->text('用户输入' . json_encode($this->config));
    }
}