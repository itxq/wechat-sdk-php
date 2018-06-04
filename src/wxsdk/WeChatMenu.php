<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatMenu.php
 *        概    要: 微信公众号菜单接口
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:36
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 微信公众号菜单接口
 * Class WeChatMenu
 * @package itxq\wechat\wxsdk
 */
class WeChatMenu extends WeChat
{
    /**
     * 创建微信公众号菜单
     * @param string ,$menuData(json格式字符串)
     * @return bool,成功返回true,失败返回false
     */
    public function createMenu($menuData) {
        $url = $this->apiUrl . 'cgi-bin/menu/create?access_token=';
        $url .= $this->_getAccessToken();
        $result = Http::requestPost($url, $menuData);
        $result_obj = json_decode($result);
        if ($result_obj->errcode == 0) {
            return true;
        } else {
            $this->message = $result_obj->errmsg;
            return false;
        }
    }
    
    /**
     * 删除微信公众号菜单
     * @return bool,成功返回true,失败返回false
     */
    public function deleteMenu() {
        $url = $this->apiUrl . 'cgi-bin/menu/delete?access_token=';
        $url .= $this->_getAccessToken();
        $result = Http::requestGet($url);
        $result_obj = json_decode($result);
        return $result_obj->errcode == 0 ? true : false;
    }
    
    /**
     * 获取微信公众号菜单
     * @param string $type - 数据类型（array|json）
     * @return mixed
     */
    public function getMenu($type = 'array') {
        $url = $this->apiUrl . 'cgi-bin/get_current_selfmenu_info?access_token=';
        $url .= $this->_getAccessToken();
        $result = Http::requestGet($url);
        if ($type === 'array') {
            $result = json_decode($result, true);
        }
        return $result;
    }
}