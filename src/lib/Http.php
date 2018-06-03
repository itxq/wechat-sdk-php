<?php
/**
 *  ==================================================================
 *        文 件 名: Http.php
 *        概    要: Http处理类
 *        作    者: IT小强
 *        创建时间: 2018/6/3 14:31
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\lib;

/**
 * Http处理类
 * Class Http
 * @package itxq\wechat
 */
class Http
{
    
    /**
     * 发送POST请求
     * @param string $url - 请求的URL
     * @param $data - POST数据
     * @param bool $ssl
     * @return mixed
     */
    public static function requestPost($url, $data, $ssl = true) {
        return self::_curlRequest($url, $data, $ssl);
    }
    
    /**
     * 发送GET请求的方法
     * @param $url ,请求的URL
     * @param bool $ssl ,是否为ssl.默认为true
     * @return bool|mixed ,请求成功返回响应结果为json格式,失败返回false
     */
    public static function requestGet($url, $ssl = true) {
        return self::_curlRequest($url, NULL, $ssl);
    }
    
    /**
     * Curl发送请求
     * @param $url ,请求的URL
     * @param null $data ,GET请求此项为NULL,POST请求此项为POST数据
     * @param bool $ssl
     * @return mixed
     */
    protected static function _curlRequest($url, $data = NULL, $ssl = true) {
        /* 利用 Curl 完成 GET/POST 请求 */
        $curl = curl_init();
        /* curl 配置项 */
        /* 请求 URL */
        curl_setopt($curl, CURLOPT_URL, $url);
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        /* user_agent，请求代理信息 */
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
        /* referer头，请求来源 */
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        /* ssl相关选项 */
        if ($ssl) {
            /* 禁用后 Curl 将终止从服务端进行验证 */
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            /* 检查服务器SSL证书中是否存在一个公用名(common name) */
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
        /* post相关选项 */
        if ($data != NULL) {
            /* 是否为POST请求 */
            curl_setopt($curl, CURLOPT_POST, true);
            /* 处理请求数据 */
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        /* 是否处理响应头 ->否 */
        curl_setopt($curl, CURLOPT_HEADER, false);
        /* curl_exec()是否返回响应结果 ->是 */
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        /* 发送请求 */
        $response = curl_exec($curl);
        // if (false === $response) {
        //     /* 存储错误信息 */
        //     $this->message = curl_error($curl);
        // }
        return $response;
    }
}