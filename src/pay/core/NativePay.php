<?php
/**
 *  ==================================================================
 *        文 件 名: NativePay.php
 *        概    要: 扫描支付实现类
 *        作    者: IT小强
 *        创建时间: 2017/8/26 12:11
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace plugins\wechat\pay;

/**
 * Class NativePay -扫描支付实现类
 * @package plugins\wechat\pay
 */
class NativePay
{
    /**
     * 生成扫描支付URL,模式一
     * @param \itxq\wechat\pay\WxPayConfig $wxPayConfig
     * @param $productId - 商品ID，商户自行定义
     * @return string
     * @throws \Exception
     */
    public function getPrePayUrl($wxPayConfig, $productId) {
        $biz = new \WxPayBizPayUrl();
        $biz->SetProduct_id($productId);
        $values = \WxpayApi::bizpayurl($wxPayConfig, $biz);
        $url = 'weixin://wxpay/bizpayurl?' . $this->toUrlParams((array)$values);
        return $url;
    }
    
    /**
     * 参数数组转换为url参数
     * @param array $urlObj - 数组
     * @return string url参数
     */
    private function toUrlParams($urlObj) {
        $buff = '';
        foreach ($urlObj as $k => $v) {
            $buff .= $k . '=' . $v . '&';
        }
        $buff = trim($buff, '&');
        return $buff;
    }
}