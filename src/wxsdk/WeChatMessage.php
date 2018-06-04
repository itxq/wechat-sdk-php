<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatMessage.php
 *        概    要: 消息处理类
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:40
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 消息处理类
 * Class WeChatMessage
 * @package itxq\wechat\wxsdk
 */
class WeChatMessage extends WeChat
{
    /**
     * 对微信公众平台的请求信息做出响应
     */
    public function response() {
        /* 获取请求时POST：XML字符串 该数据不是key/value型 因此不能使用$_POST获取*/
        // $xml_str = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml_str = file_get_contents('php://input');
        /* 如果没有post数据，则响应空字符串表示结束 */
        if (empty($xml_str)) {
            die ('');
        }
        /* 解析该xml字符串，利用simpleXML */
        /* 禁止xml实体解析，防止xml注入 */
        libxml_disable_entity_loader(true);
        /* 从字符串获取simpleXML对象 */
        $request_xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        /* 判断该消息的类型通过元素：MsgType */
        switch ($request_xml->MsgType) {
            /* 事件 */
            case 'event':
                /* 判断具体的事件类型（关注，取消，点击） */
                $event = $request_xml->Event;
                if ('subscribe' == $event) {
                    /* 关注事件 */
                    die($this->_responseSubscribe($request_xml));
                } else if ('CLICK' == $event) {
                    file_put_contents('./c.txt', 'dasd');
                    /* 菜单点击事件 */
                    die($this->_responseClick($request_xml));
                } else if ('VIEW' == $event) {
                    /* 链接跳转事件 */
                    die($this->_responseView($request_xml));
                } else {
                    die('');
                }
                break;
            /* 文本消息 */
            case 'text':
                $this->_responseText($request_xml);
                break;
            case 'image': // 图片消息
                $this->_responseImage($request_xml);
                break;
            case 'voice': // 语音消息
                $this->_responseVoice($request_xml);
                break;
            case 'video': // 视频消息
                $this->_responseVideo($request_xml);
                break;
            case 'shortvideo': // 短视频消息
                $this->_responseShortVideo($request_xml);
                break;
            case 'location': // 位置消息
                $this->_responseLocation($request_xml);
                break;
            case 'link': // 连接消息
                $this->_responseLink($request_xml);
                break;
            default:
                die ('');
                break;
        }
    }
    
    /**
     * 发送模板消息
     * @param $templateId - 发送模板消息
     * @param $openid - 微信OPENID
     * @param $title - 模板标题
     * @param array $keywords - 模板内容字段
     * @param string $remark - 备注信息
     * @param string $url - 详情跳转链接
     * @param bool $miniprogram - 小程序信息
     * @return bool
     */
    public function sendTemplateMessage($templateId, $openid, $title, $keywords = [], $remark = '', $url = '', $miniprogram = false) {
        $data = [
            'first' => $this->_getMessageKeyword($title)
        ];
        $message = [
            'touser'      => $openid,
            'template_id' => $templateId,
            'url'         => $url,
            'miniprogram' => $miniprogram,
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
     * 响应关注事件
     * @param $request_xml
     */
    private function _responseSubscribe($request_xml) {
        $to = $request_xml->FromUserName;
        $from = $request_xml->ToUserName;
        $content = '欢迎订阅本公众号';
        $msg = $this->_getTextMessage($to, $from, $content);
        die($msg);
    }
    
    /**
     * 响应菜单点击事件
     * @param $request_xml
     */
    private function _responseClick($request_xml) {
        $to = $request_xml->FromUserName;
        $from = $request_xml->ToUserName;
        $content = '当前菜单KEY为：' . $request_xml->EventKey;
        $msg = $this->_getTextMessage($to, $from, $content);
        die($msg);
    }
    
    /**
     * 响应链接跳转事件
     * @param $request_xml
     * @return string
     */
    private function _responseView($request_xml) {
        $to = $request_xml->FromUserName;
        $from = $request_xml->ToUserName;
        $content = '欢迎订阅本公众号';
        $msg = $this->_getTextMessage($to, $from, $content);
        return $msg;
    }
    
    /**
     * 响应文本消息
     * @param $request_xml
     */
    private function _responseText($request_xml) {
        /* 获取文本内容 */
        $content = $request_xml->Content;
        $to = $request_xml->FromUserName;
        $from = $request_xml->ToUserName;
        if ($content == '帮助') {
            $response_content = '输入对应序号或名称，获取相应资源' . "\n" . '[1]PHP' . "\n" . '[2]Java' . "\n" . '[3]C++';
        } else {
            $response_content = '未匹配到任何内容';
        }
        die($this->_getTextMessage($to, $from, $response_content));
    }
    
    /**
     * 响应图片消息
     * @param $request_xml
     */
    private function _responseImage($request_xml) {
        // TODO
    }
    
    /**
     * 响应语音消息
     * @param $request_xml
     */
    private function _responseVoice($request_xml) {
        // TODO
    }
    
    /**
     * 响应视频消息
     * @param $request_xml
     */
    private function _responseVideo($request_xml) {
        // TODO
    }
    
    /**
     * 响应短视频消息
     * @param $request_xml
     */
    private function _responseShortVideo($request_xml) {
        // TODO
    }
    
    /**
     * 响应位置消息
     * @param $request_xml
     */
    private function _responseLocation($request_xml) {
        // TODO
    }
    
    /**
     * 响应连接消息
     * @param $request_xml
     */
    private function _responseLink($request_xml) {
        // TODO
    }
    
    /**
     * 文本信息模板
     * @param $to ,目标用户ID
     * @param $from ,来源用户ID
     * @param $content ,文本内容
     * @return string ,返回拼装好的xml
     */
    private function _getTextMessage($to, $from, $content) {
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
    private function _getMessageKeyword($keyword, $defaultColor = '#000000') {
        if (is_string($keyword)) {
            return ['value' => $keyword, 'color' => $defaultColor];
        } else if (is_array($keyword)) {
            return [
                'value' => $this->getArrayData('title', $keyword, ''),
                'color' => $this->getArrayData('color', $keyword, $defaultColor),
            ];
        } else {
            return false;
        }
    }
}