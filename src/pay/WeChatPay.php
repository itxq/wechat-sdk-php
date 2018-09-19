<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatPay.php
 *        概    要: 微信支付调用接口
 *        作    者: IT小强
 *        创建时间: 2018/5/22 12:07
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\pay;

require_once __DIR__ . '/lib/WxPay.Api.php';
require_once __DIR__ . '/lib/WxPay.Notify.php';

use itxq\wechat\lib\Tools;
use itxq\wechat\pay\core\JsApiPay;

/**
 * 微信支付调用接口
 * Class WeChatPay
 * @package itxq\wechat\pay
 */
class WeChatPay
{
    /**
     * @var \itxq\wechat\pay\WxPayConfig
     */
    private static $wxPayConfig = [];
    
    /**
     * WeChatPay 构造函数.
     * @param $wxPayConfig - 微信支付配置信息
     * @throws \Exception
     */
    public function __construct($wxPayConfig) {
        date_default_timezone_set('PRC');
        self::$wxPayConfig = new WxPayConfig($wxPayConfig);
    }
    
    /**
     * 微信支付接口
     * @param $params - 接口相关参数
     * $params = [
     *      // 设置商品ID,设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     *      'id'   => '',
     *       // 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|* 且在同一个商户号下唯一。
     *      'out_trade_no'   => '',
     *      // 设置商品或支付单简要描述
     *      'body'   => '',
     *      // 设置订单总金额，只能为整数，详见支付金额(单位：分)
     *      'fee'    => 1,
     *      // 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
     *      'attach' => '',
     *      // 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
     *      'tag'    => '',
     *      // 设置支付类型 取值如下：JSAPI，NATIVE，APP，MWEB 详细说明见参数规定
     *      'type'   => '',
     * ];
     * @return array['code'=>1,'msg'=>''] // code为2表示订单为已支付订单，无需重复下单，1表示生产支付页面成功，0表示失败
     * @throws \Exception
     */
    public function pay($params) {
        $t = time();
        $tools = new JsApiPay(self::$wxPayConfig);
        $isMicroMessenger = Tools::isMicroMessenger();
        $getTradeType = Tools::getSubValue('type', $params, false);
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        //超时时间
        $timeout_express = 1800;
        // 商户订单号
        $out_trade_no = Tools::getSubValue('out_trade_no', $params, '');
        // 设置商品或支付单简要描述
        $input->SetBody(Tools::getSubValue('body', $params, ''));
        // 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetAttach(Tools::getSubValue('attach', $params, ''));
        //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetOut_trade_no($out_trade_no);
        // 设置订单总金额，只能为整数，详见支付金额(单位：分)
        $input->SetTotal_fee(Tools::getSubValue('fee', $params, 1));
        // 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        $input->SetTime_start(date('YmdHis', $t));
        // 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
        $input->SetTime_expire(date('YmdHis', $t + $timeout_express));
        // 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
        $input->SetGoods_tag(Tools::getSubValue('tag', $params, 1));
        // 设置接收微信支付异步通知回调地址
        $input->SetNotify_url(self::$wxPayConfig->NOTIFY_URL);
        if ($isMicroMessenger) {
            // 获取用户openid
            $openId = $tools->GetOpenid();
            // 设置trade_type=JSAPI，此参数必传
            //用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
            $input->SetOpenid($openId);
            $tradeType = 'JSAPI';
        } else if (Tools::isMobile()) {
            $tradeType = 'MWEB';
        } else {
            $tradeType = 'NATIVE';
        }
        // 设置取值如下：JSAPI，NATIVE，APP，MWEB 详细说明见参数规定
        $tradeType = in_array($getTradeType, ['JSAPI', 'NATIVE', 'MWEB', 'APP']) ? $getTradeType : $tradeType;
        $input->SetTrade_type($tradeType);
        if ($tradeType == 'NATIVE') {
            // 设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
            $input->SetProduct_id(Tools::getSubValue('id', $params, Tools::cmRound(8)));
        }
        $order = \WxPayApi::unifiedOrder(self::$wxPayConfig, $input);
        if (Tools::getSubValue('return_code', $order, 'FAIL') != 'SUCCESS') {
            return ['code' => 0, 'msg' => '支付请求失败'];
        }
        if (Tools::getSubValue('result_code', $order, 'FAIL') != 'SUCCESS') {
            $re = ['code' => 0, 'msg' => Tools::getSubValue('err_code_des', $order, '支付请求失败')];
            if (Tools::getSubValue('err_code', $order, 'FAIL') == 'ORDERPAID') {
                $re['code'] = 2;
            }
            return $re;
        }
        $var = [
            'return_url'      => self::$wxPayConfig->RETURN_URL,
            'body'            => $input->GetBody(),
            'fee'             => intval($input->GetTotal_fee()) / 100,
            'out_trade_no'    => $out_trade_no,
            'ctime'           => $t,
            'timeout_express' => intval($timeout_express / 60) . '分钟' . intval($timeout_express % 60) . '秒'
        ];
        switch ($tradeType) {
            case 'MWEB':
                $template = 'h5_pay_api';
                $var['h5_pay_api_url'] = Tools::getSubValue('mweb_url', $order, '');
                if (empty($var['h5_pay_api_url'])) {
                    exit('支付链接生成失败');
                }
                break;
            case 'JSAPI':
                $template = 'js_pay_api';
                $var['jsApiParameters'] = $tools->GetJsApiParameters((array)$order, false);
                break;
            default:
                $template = 'native_pay_api';
                $qrCode = Tools::getSubValue('code_url', $order, '');
                if (empty($qrCode)) {
                    exit('支付二维码生成失败');
                }
                $var['native_pay_api_url'] = $qrCode;
                break;
        }
        $template = realpath(__DIR__ . '/view/' . $template . '.php');
        return ['code' => 1, 'msg' => require_once $template];
    }
    
    /**
     * 微信支付回调数据
     * @return array
     * @throws \Exception
     */
    public function notify() {
        //获取通知的数据
        $xml = file_get_contents('php://input');
        //如果返回成功则验证签名
        try {
            $result = \WxPayResults::Init(self::$wxPayConfig, $xml);
        } catch (\WxPayException $e) {
            $result = [];
        }
        if (!is_array($result) || count($result) < 1) {
            return ['code' => 0, 'msg' => '签名验证失败'];
        }
        /* 验证应用ID是否正确 */
        
        $defaultAppId = self::$wxPayConfig->APPID;
        $returnAppId = trim(Tools::getSubValue('appid', $result, ''));
        if (empty($defaultAppId) || empty($returnAppId) || $defaultAppId != $returnAppId) {
            return ['code' => 0, 'msg' => '应用ID验证失败'];
        }
        /* 验证商户ID是否正确 */
        $defaultMchId = self::$wxPayConfig->MCHID;
        $returnMchId = trim(Tools::getSubValue('mch_id', $result, ''));
        if (empty($defaultMchId) || empty($returnMchId) || $defaultMchId != $returnMchId) {
            return ['code' => 0, 'msg' => '商户ID验证失败'];
        }
        $msg = Tools::getSubValue('return_msg', $result, '未知错误');
        $resultCode = Tools::getSubValue('result_code', $result, '');
        if ($resultCode != 'SUCCESS') {
            $data = [
                'code' => 0,
                'msg'  => Tools::getSubValue('err_code_des', $result, $msg)
            ];
            return $data;
        }
        $info = [
            // 商户内部订单号
            'out_trade_no'   => Tools::getSubValue('out_trade_no', $result, ''),
            // 微信订单号
            'transaction_id' => Tools::getSubValue('transaction_id', $result, ''),
            // 订单金额
            'fee'            => (Tools::getSubValue('total_fee', $result, 0)) / 100,
            // 支付类型（JSAPI - 公众号支付，NATIVE - 扫码支付，APP - APP支付，MWEB - h5支付 ALI -支付宝支付）
            'type'           => Tools::getSubValue('trade_type', $result, 0),
            // 订单状态
            'status'         => true,
            // 原样返回数据
            'attach'         => Tools::getSubValue('attach', $result, ''),
            // 用户在商户appid下的唯一标识
            'openid'         => Tools::getSubValue('openid', $result, ''),
            // 是否关注公众号
            'is_subscribe'   => Tools::getSubValue('is_subscribe', $result, 'N'),
        ];
        $data = [
            'code' => 1,
            'msg'  => '接收支付结果成功',
            'info' => $info
        ];
        return $data;
    }
    
    /**
     * 处理支付结果后返回信息给微信
     * @param bool $code 状态 - true（接收成功）false（接收失败）
     * @param string $msg 信息字符串
     * @return bool
     * @throws \Exception
     */
    public function replyNotify($code = false, $msg = '接收失败') {
        $return_code = ($code === true) ? 'SUCCESS' : 'ERROR';
        if (empty($msg)) {
            $return_msg = ($code === true) ? 'OK' : 'FALSE';
        } else {
            $return_msg = $msg;
        }
        $notify = new \WxPayNotify();
        $notify->SetReturn_code($return_code);
        $notify->SetReturn_msg($return_msg);
        $returnXml = $notify->ToXml();
        return $returnXml;
    }
}