<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatTokenValid.php
 *        概    要: Token验证类
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:17
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

/**
 * Token验证类
 * Class WeChatTokenValid
 * @package itxq\wechat\wxsdk
 */
class WeChatTokenValid extends WeChat
{
    /**
     * 验证Token
     * @param string $timestamp - 时间戳
     * @param string $nonce - 随机数
     * @param string $signature - 微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
     * @param string $echoStr - 随机字符串
     * @throws \Exception
     */
    public function check($timestamp = '', $nonce = '', $signature = '', $echoStr = '') {
        if (empty($timestamp) && empty($nonce) && empty($signature) && empty($echoStr)) {
            $timestamp = $_GET['timestamp'];
            $nonce = $_GET['nonce'];
            $signature = $_GET['signature'];
            $echoStr = $_GET['echostr'];
        }
        $token = self::$token;
        if (!$token) {
            throw new \Exception('请配置微信TOKEN');
        }
        /* TOKEN值,时间戳,随机数 */
        $tmpArr = [$token, $timestamp, $nonce];
        /* UES SORT_STRING RULE */
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        $check = ($tmpStr === $signature) ? $echoStr : '';
        echo $check;
        exit();
    }
}