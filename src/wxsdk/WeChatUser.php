<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatUser.php
 *        概    要: 公众号用户管理
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:44
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 公众号用户管理
 * Class WeChatUser
 * @package itxq\wechat\wxsdk
 */
class WeChatUser extends WeChat
{
    /**
     * 批量获取用户信息
     * @param $openidList - openid数组
     * @param string $lang - 返回语言
     * @return bool
     */
    public function getUserList($openidList, $lang = 'zh_CN') {
        $url = $this->apiUrl . 'cgi-bin/user/info/batchget?access_token=' . $this->_getAccessToken();
        $data = [];
        foreach ($openidList as $v) {
            $data[] = ['openid' => $v, 'lang' => $lang];
        }
        $data = json_decode(Http::requestPost($url, json_encode(['user_list' => $data])), true);
        if (isset($data['errcode']) && $data['errcode'] != 0) {
            $this->errCode = $data['errcode'];
            $this->message = $data['errmsg'];
            return false;
        }
        return $data['user_info_list'];
    }
    
    /**
     * 获取公众号下全部的openid列表
     * @param string $nextOpenId
     * @param array $openidList
     * @return array|bool
     */
    public function getOpenIdList($nextOpenId = '', $openidList = []) {
        $url = $this->apiUrl . 'cgi-bin/user/get?access_token=' . $this->_getAccessToken();
        if (!empty($nextOpenId)) {
            $url .= '&next_openid=' . $nextOpenId;
        }
        $data = json_decode(Http::requestGet($url), true);
        if (isset($data['errcode']) && $data['errcode'] != 0) {
            $this->errCode = $data['errcode'];
            $this->message = $data['errmsg'];
            return false;
        }
        if (isset($data['data']['openid'])) {
            $openidList = array_merge($openidList, $data['data']['openid']);
        }
        if (isset($data['next_openid']) && !empty($data['next_openid'])) {
            $openidList = $this->getOpenIdList($data['next_openid'], $openidList);
        }
        return $openidList;
    }
}