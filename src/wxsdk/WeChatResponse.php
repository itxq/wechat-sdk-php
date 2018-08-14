<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatResponse.phpp
 *        概    要: 消息处理类
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:40
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

/**
 * 消息处理类
 * Class WeChatResponse
 * @package itxq\wechat\wxsdk
 */
class WeChatResponse extends WeChat
{
    /**
     * @var array - 消息类型
     */
    protected $msgType = ['event', 'text', 'image', 'voice', 'video', 'shortvideo', 'location', 'link'];
    
    /**
     * @var null|string 消息处理类
     */
    protected $msgHandler = null;
    
    /**
     * @var array - 推送事件类型
     */
    protected $event = ['subscribe', 'unsubscribe', 'SCAN', 'LOCATION', 'CLICK', 'VIEW'];
    
    /**
     * @var null|string 事件处理类
     */
    protected $eventHandler = null;
    
    /**
     * 初始化
     * @param array $config - 配置信息
     */
    protected function initialize($config) {
        $this->msgHandler = $this->getSubValue('msg_handler', $config, null);
        $this->eventHandler = $this->getSubValue('event_handler', $config, null);
    }
    
    /**
     * 对微信公众平台的请求信息做出响应
     */
    public function response() {
        // 获取请求时POST：XML字符串 该数据不是key/value型 因此不能使用$_POST获取
        // $xmlStr = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xmlStr = file_get_contents('php://input');
        // 如果没有post数据，则响应空字符串表示结束
        if (empty($xmlStr)) {
            die ('');
        }
        // 解析该xml字符串，利用simpleXML 禁止xml实体解析，防止xml注入
        libxml_disable_entity_loader(true);
        // 从字符串获取simpleXML对象
        $requestXml = simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $requestXml = json_encode($requestXml);
        $requestXml = json_decode($requestXml, true);
        // 获取消息类型
        $msgType = $this->getSubValue('MsgType', $requestXml, '');
        if (empty($msgType) || !in_array($msgType, $this->msgType)) {
            die('');
        }
        if ($msgType === 'event') { //　事件处理
            $event = $this->getSubValue('Event', $requestXml, '');
            if (empty($event) || !in_array($event, $this->event)) {
                die('');
            }
            $this->eventHandler($event, $requestXml, $this->eventHandler);
        } else { // 其他消息处理
            $this->msgHandler($msgType, $requestXml, $this->msgHandler);
        }
    }
    
    /**
     * 消息处理
     * @param $msgType - 信息类型
     * @param $data - 推送数据
     * @param $handler - 回调类
     * @return bool
     */
    protected function msgHandler($msgType, $data, $handler) {
        if (is_null($handler)) {
            return false;
        }
        $msg = 'msg' . ucfirst(strtolower($msgType));
        try {
            $handler = new $handler($this->config);
            $handler->$msg($data);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            return false;
        }
        return true;
    }
    
    /**
     * 事件处理
     * @param $event - 事件名称
     * @param $data - 推送数据
     * @param $handler - 回调类
     * @return bool
     */
    protected function eventHandler($event, $data, $handler) {
        if (is_null($handler)) {
            return false;
        }
        $event = 'event' . ucfirst(strtolower($event));
        try {
            $handler = new $handler($this->config);
            $handler->$event($data);
        } catch (\Exception $exception) {
            $this->message = $exception->getMessage();
            return false;
        }
        return true;
    }
}