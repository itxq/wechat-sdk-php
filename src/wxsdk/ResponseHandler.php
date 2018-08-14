<?php
/**
 *  ==================================================================
 *        文 件 名: ResponseHandler.php
 *        概    要: 微信消息处理基类
 *        作    者: IT小强
 *        创建时间: 2018/8/14 13:32
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

/**
 * 微信消息处理基类
 * Class ResponseHandler
 * @package itxq\wechat\wxsdk
 */
abstract class ResponseHandler
{
    /**
     * @var array - 配置信息
     */
    protected $config = [];
    
    /**
     * 初始化
     * WeChatEventHandler 构造函数.
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
    }
    
    /**
     * 文本信息模板
     * @param $to ,目标用户ID
     * @param $from ,来源用户ID
     * @param $content ,文本内容
     * @return string ,返回拼装好的xml
     */
    protected function responseTextMessage($to, $from, $content) {
        $msg_template = '<xml>';
        $msg_template .= '<ToUserName><![CDATA[%s]]></ToUserName>';
        $msg_template .= '<FromUserName><![CDATA[%s]]></FromUserName>';
        $msg_template .= '<CreateTime>%s</CreateTime>';
        $msg_template .= '<MsgType><![CDATA[text]]></MsgType>';
        $msg_template .= '<Content><![CDATA[%s]]></Content>';
        $msg_template .= '</xml>';
        return sprintf($msg_template, $to, $from, time(), $content);
    }
}