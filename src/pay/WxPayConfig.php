<?php
/**
 *  ==================================================================
 *        文 件 名: WxPayConfig.php
 *        概    要: 微信支付配置类
 *        作    者: IT小强
 *        创建时间: 2017/8/25 16:43
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\pay;

use itxq\wechat\lib\Tools;

/**
 * 微信支付配置类
 * Class WxPayConfig
 * @package itxq\wechat\pay
 */
class WxPayConfig extends \WxPayConfigInterface
{
    
    /**
     * @var string - 绑定支付的APPID（必须配置，开户邮件中可查看）
     */
    public $APPID = 'wx426b3015555a46be';
    
    /**
     * @var string - 商户号（必须配置，开户邮件中可查看）
     */
    public $MCHID = '1900009851';
    
    /**
     * @var string - 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     */
    public $KEY = '8934e7d15453e97507ef794cf7b0519d';
    
    /**
     * @var string - 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
     */
    public $APPSECRET = '7813490da6f1265e4901ffb80afaa36f';
    
    /**
     * @var string - 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     */
    public $SSLCERT_PATH = '../cert/apiclient_cert.pem';
    public $SSLKEY_PATH = '../cert/apiclient_key.pem';
    
    /**
     * @var string - 这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     */
    public $CURL_PROXY_HOST = '0.0.0.0';
    public $CURL_PROXY_PORT = 0;
    
    /**
     * @var int - 接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    public $REPORT_LEVENL = 1;
    
    /**
     * @var string - 异步通知url
     */
    public $NOTIFY_URL = '';
    
    /**
     * @var string - 同步跳转url
     */
    public $RETURN_URL = '';
    
    /**
     * 初始化微信支付配置信息
     * @param $config - 支付配置
     * WxPayConfig 构造函数.
     * @throws \Exception
     */
    public function __construct($config = []) {
        $this->APPID = Tools::getSubValue('APPID', $config, '');
        $this->MCHID = Tools::getSubValue('MCHID', $config, '');
        $this->KEY = Tools::getSubValue('KEY', $config, '');
        $this->APPSECRET = Tools::getSubValue('APPSECRET', $config, '');
        $this->SSLCERT_PATH = Tools::getSubValue('SSLCERT_PATH', $config, '');
        $this->SSLKEY_PATH = Tools::getSubValue('SSLKEY_PATH', $config, '');
        $this->CURL_PROXY_HOST = Tools::getSubValue('CURL_PROXY_HOST', $config, '0.0.0.0');
        $this->CURL_PROXY_PORT = intval(Tools::getSubValue('CURL_PROXY_PORT', $config, 0));
        $this->REPORT_LEVENL = intval(Tools::getSubValue('REPORT_LEVENL', $config, 1));
        $this->NOTIFY_URL = Tools::getSubValue('NOTIFY_URL', $config, '');
        $this->RETURN_URL = Tools::getSubValue('RETURN_URL', $config, '');
    }
    
    //=======【基本信息设置】=====================================
    
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     */
    public function GetAppId() {
        return $this->APPID;
    }
    
    public function GetMerchantId() {
        return $this->MCHID;
    }
    
    //=======【支付相关配置：支付成功回调地址/签名方式】===================================
    
    /**
     * TODO:支付回调url
     * 签名和验证签名方式， 支持md5和sha256方式
     **/
    public function GetNotifyUrl() {
        return $this->NOTIFY_URL;
    }
    
    public function GetSignType() {
        return "HMAC-SHA256";
    }
    
    //=======【curl代理设置】===================================
    
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * @param string $proxyHost
     * @param int $proxyPort
     */
    public function GetProxy(&$proxyHost, &$proxyPort) {
        $proxyHost = $this->CURL_PROXY_HOST;
        $proxyPort = $this->CURL_PROXY_PORT;
    }
    
    
    //=======【上报信息配置】===================================
    
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    public function GetReportLevenl() {
        return $this->REPORT_LEVENL;
    }
    
    
    //=======【商户密钥信息-需要业务方继承】===================================
    /*
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）, 请妥善保管， 避免密钥泄露
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）， 请妥善保管， 避免密钥泄露
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     */
    public function GetKey() {
        return $this->KEY;
    }
    
    public function GetAppSecret() {
        return $this->APPSECRET;
    }
    
    
    //=======【证书路径设置-需要业务方继承】=====================================
    
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 注意:
     * 1.证书文件不能放在web服务器虚拟目录，应放在有访问权限控制的目录中，防止被他人下载；
     * 2.建议将证书文件名改为复杂且不容易猜测的文件名；
     * 3.商户服务器要做好病毒和木马防护工作，不被非法侵入者窃取证书文件。
     * @param string $sslCertPath
     * @param string $sslKeyPath
     */
    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath) {
        $sslCertPath = $this->SSLCERT_PATH;
        $sslKeyPath = $this->SSLKEY_PATH;
    }
}