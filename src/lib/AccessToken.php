<?php
/**
 *  ==================================================================
 *        文 件 名: AccessToken.php
 *        概    要: 获取 AccessToken
 *        作    者: IT小强
 *        创建时间: 2018/6/3 14:30
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\lib;

/**
 * 获取 AccessToken
 * Class AccessToken
 * @package itxq\wechat
 */
class AccessToken
{
    /**
     * @var string - AccessToken存放目录
     */
    protected static $path = __DIR__ . '/../data/';
    
    /**
     * @var string - 接口请求地址
     */
    protected static $apiUrl = 'https://api.weixin.qq.com/';
    
    /**
     * 获取AccessToken
     * @param string $appId
     * @param string $appSecret
     * @param string $apiUrl - 微信API
     * @return bool|string
     */
    public static function get($appId, $appSecret, $apiUrl = '') {
        $path = self::$path;
        if (!is_dir(realpath($path))) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
        }
        $tokenFile = $path . 'access_token_' . md5($appId) . '.json';
        /* 判断AccessToken文件是否存在 */
        if (!is_file(realpath($tokenFile))) {
            // 文件不存在时通过接口获取
            return self::getAccessToken($appId, $appSecret, $apiUrl);
        }
        // 获取文件信息
        $fileInfo = json_decode(file_get_contents(realpath($tokenFile)), true);
        // 判断 AccessToken 是否过期
        if ((time() - filemtime($tokenFile)) > ($fileInfo['expires_in'] - 60)) {
            // AccessToken过期时通过接口获取
            return self::getAccessToken($appId, $appSecret, $apiUrl);
        }
        return $fileInfo['access_token'];
    }
    
    /**
     * 通过接口获取AccessToken
     * @param $appId
     * @param $appSecret
     * @param string $apiUrl - 微信API
     * @return bool|string
     */
    protected static function getAccessToken($appId, $appSecret, $apiUrl = '') {
        // 接口地址
        $apiUrl = empty($apiUrl) ? self::$apiUrl : $apiUrl;
        $url = $apiUrl . 'cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        /* 向该URL发送GET请求 */
        $result = Http::requestGet($url);
        /* 判断获取响应结果是否成功 */
        if (!$result) {
            /* 获取响应结果失败时返回 false */
            return false;
        }
        /* 获取响应结果成功时 ,对响应结果进行处理 json转对象 */
        $result = @json_decode($result, true);
        // 检查是否获取成功
        if (!isset($result['access_token']) || !isset($result['expires_in'])) {
            return false;
        }
        // 保存到文件
        $path = self::$path;
        if (!is_dir(realpath($path))) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
        }
        $tokenFile = $path . 'access_token_' . md5($appId) . '.json';
        if (!@file_put_contents($tokenFile, json_encode($result))) {
            return false;
        }
        return $result['access_token'];
    }
}