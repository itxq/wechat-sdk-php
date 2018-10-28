<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatJsApi.php
 *        概    要: 微信JSSDK
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:19
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 微信JSSDK
 * Class WeChatJsApi
 * @package itxq\wechat\wxsdk
 */
class WeChatJsApi extends WeChat
{
    /**
     * @var - ticket有限时间,默认为7100
     */
    private $lifeTime = 7100;
    
    /**
     * JsAPI 调用函数
     * @return array
     */
    public function getSignPackage() {
        $jsapiTicket = $this->_getJsApiTicket();
        $timestamp = time();
        $nonceStr = $this->cmRound(16, 'all');
        $url = $this->getUrl();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $tempArray = [
            'jsapi_ticket' => $jsapiTicket,
            'noncestr'     => $nonceStr,
            'timestamp'    => $timestamp,
            'url'          => $url,
        ];
        $string = substr($this->arraySerialize($tempArray), 1);
        $signature = sha1($string);
        $signPackage = [
            'appId'     => self::$appId,
            'nonceStr'  => $nonceStr,
            'timestamp' => $timestamp,
            'url'       => $url,
            'signature' => $signature,
            'rawString' => $string
        ];
        return $signPackage;
    }
    
    /**
     * 获取调用接口所必须的票据
     * @return bool|string
     */
    private function _getJsApiTicket() {
        $jsSdkTicketFile = __DIR__ . '/../data/js_ticket_' . md5(self::$appId) . '.txt';
        /* 判断jsSdkTicket是否在有效期内 */
        if (file_exists($jsSdkTicketFile) && time() - filemtime($jsSdkTicketFile) < $this->lifeTime) {
            /* 如果jsSdkTicket文件存在,且在有效期内,直接获取文件内容 */
            return file_get_contents($jsSdkTicketFile);
        }
        $url = $this->apiUrl . 'cgi-bin/ticket/getticket?type=jsapi&access_token=' . $this->_getAccessToken();
        /* 向该URL发送GET请求 */
        $result = Http::requestGet($url);
        /* 判断获取响应结果是否成功 */
        if (!$result) {
            $this->message = 'JsApiTicket获取失败';
            /* 获取响应结果失败时返回 false */
            return false;
        }
        /* 获取响应结果成功时 ,对响应结果进行处理 json转数组 */
        $result = json_decode($result, true);
        
        /* 将处理结果写入的 文件中保存 */
        file_put_contents($jsSdkTicketFile, $result['ticket']);
        return $result['ticket'];
    }
}