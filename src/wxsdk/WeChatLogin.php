<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatLogin.php
 *        概    要: 微信公众号网页授权获取用户信息
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:26
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 微信公众号网页授权获取用户信息
 * Class WeChatLogin
 * @package itxq\wechat\wxsdk
 */
class WeChatLogin extends WeChat
{
    /**
     * 微信公众号登录调用接口
     * @param string $callbackUrl - 回调处理地址
     * @param string $state - 额外的参数
     * @param string $lang - 语言选择(zh_CN 简体，zh_TW 繁体，en 英语)
     * @return bool|mixed
     * @throws \Exception
     */
    public function login($callbackUrl = '', $state = '', $lang = 'zh_CN') {
        $callbackUrl = empty($callbackUrl) ? $this->getUrl() : $callbackUrl;
        /* 获取请求时附带的参数 */
        $params = $_GET;
        /* 微信公众号登录所必要的code */
        $code = strip_tags($this->getArrayData('code', $params, ''));
        if (!empty($code)) {
            return $this->getWeChatUserInfo($code, $lang);
        }
        /* 获取用于换取token所必须的code */
        $url = $this->getWeChatCode($callbackUrl, $state);
        header('location:' . $url);
        exit();
    }
    
    /**
     * 获取微信公众号登录所必要的code
     * @param $redirectUrl
     * @param string $scope
     * @param string $state
     * @return string
     */
    private function getWeChatCode($redirectUrl, $state = '', $scope = 'snsapi_userinfo') {
        $redirectUrl = urlencode($redirectUrl);
        $params = [
            'appid'         => self::$appId,
            'redirect_uri'  => $redirectUrl,
            'scope'         => $scope,
            'response_type' => 'code',
            'state'         => empty($state) ? $redirectUrl : urlencode($state)
        ];
        $params = $this->arraySerialize($params);
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize' . $params . '#wechat_redirect';
        return $url;
    }
    
    /**
     * 获取微信用户信息
     * @param string $code - 微信公众号登录所必要的code
     * @param string $lang - 语言选择(zh_CN 简体，zh_TW 繁体，en 英语)
     * @return bool|mixed
     * @throws \Exception
     */
    private function getWeChatUserInfo($code, $lang = 'zh_CN') {
        if (empty($code)) {
            throw new \Exception('参数错误：code不存在');
        }
        $params = [
            'appid'      => self::$appId,
            'secret'     => self::$appSecret,
            'code'       => $code,
            'grant_type' => 'authorization_code',
        
        ];
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token' . $this->arraySerialize($params);
        $data = Http::requestGet($url);
        if (!$data) {
            return false;
        }
        $data = json_decode($data, true);
        if (!isset($data['openid']) || empty($data['openid']) || !isset($data['access_token']) || empty($data['access_token'])) {
            return false;
        }
        $openid = trim(strip_tags($data['openid']));
        $token = trim(strip_tags($data['access_token']));
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token . '&openid=' . $openid . '&lang=' . $lang;
        $data = Http::requestGet($url);
        return $data;
    }
}