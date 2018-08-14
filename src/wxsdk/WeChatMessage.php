<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatMessage.php
 *        概    要: 微信模板消息发送类
 *        作    者: IT小强
 *        创建时间: 2018/8/13 16:11
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 微信模板消息发送类
 * Class WeChatMessage
 * @package itxq\wechat\wxsdk
 */
class WeChatMessage extends WeChat
{
    /**
     * 发送模板消息
     * @param $templateId - 发送模板消息
     * @param $openid - 微信OPENID
     * @param $title - 模板标题
     * @param array $keywords - 模板内容字段
     * @param string $remark - 备注信息
     * @param string $url - 详情跳转链接
     * @param bool $miniProgram - 小程序信息
     * @return bool
     */
    public function sendTemplateMessage($templateId, $openid, $title, $keywords = [], $remark = '', $url = '', $miniProgram = false) {
        $data = [
            'first' => $this->_getMessageKeyword($title)
        ];
        $message = [
            'touser'      => $openid,
            'template_id' => $templateId,
            'url'         => $url,
            'miniprogram' => $miniProgram,
        ];
        foreach ($keywords as $k => $v) {
            $keywordsKey = 'keyword' . ($k + 1);
            $data[$keywordsKey] = $this->_getMessageKeyword($v);
        }
        $data['remark'] = $this->_getMessageKeyword($remark);
        $message['data'] = $data;
        $apiUrl = $this->apiUrl . 'cgi-bin/message/template/send?access_token=' . $this->_getAccessToken();
        $result = Http::requestPost($apiUrl, json_encode($message));
        $result_obj = json_decode($result);
        if ($result_obj->errcode == 0) {
            return true;
        } else {
            $this->message = $result_obj->errmsg;
            return false;
        }
    }
    
    /**
     * 文本信息模板
     * @param $to ,目标用户ID
     * @param $from ,来源用户ID
     * @param $content ,文本内容
     * @return string ,返回拼装好的xml
     */
    protected function _getTextMessage($to, $from, $content) {
        $msg_template = '<xml>';
        $msg_template .= '<ToUserName><![CDATA[%s]]></ToUserName>';
        $msg_template .= '<FromUserName><![CDATA[%s]]></FromUserName>';
        $msg_template .= '<CreateTime>%s</CreateTime>';
        $msg_template .= '<MsgType><![CDATA[text]]></MsgType>';
        $msg_template .= '<Content><![CDATA[%s]]></Content>';
        $msg_template .= '</xml>';
        return sprintf($msg_template, $to, $from, time(), $content);
    }
    
    /**
     * 解析模板消息关键字
     * @param $keyword -关键字参数
     * @param string $defaultColor - 默认颜色
     * @return array|bool
     */
    protected function _getMessageKeyword($keyword, $defaultColor = '#000000') {
        if (is_string($keyword)) {
            return ['value' => $keyword, 'color' => $defaultColor];
        } else if (is_array($keyword)) {
            return [
                'value' => $this->getSubValue('title', $keyword, ''),
                'color' => $this->getSubValue('color', $keyword, $defaultColor),
            ];
        } else {
            return false;
        }
    }
}