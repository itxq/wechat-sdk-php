<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatQRCode.php
 *        概    要: 微信二维码获取接口类
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:37
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 微信二维码获取接口类
 * Class WeChatQRCode
 * @package itxq\wechat\wxsdk
 */
class WeChatQRCode extends WeChat
{
    /**
     * @var string - 微信二维码获取接口
     */
    protected $mpUrl = 'https://mp.weixin.qq.com/';
    
    /* 表示QRCode的类型 */
    const QRCODE_TYPE_TEMP = 1;
    const QRCODE_TYPE_LIMIT = 2;
    const QRCODE_TYPE_LIMIT_STR = 3;
    
    /**
     * 获取二维码
     * @param $content ,QRCode内容标识
     * @param null $path ,存储为文件的路径，如果为NULL表示直接输出
     * @param int $type ,二维码类型
     * @param int $expire ,如果类型选择为临时，表示其有效期
     * @return bool|string,写入文件成功返回文件路径,失败返回false
     * @throws \Exception
     */
    public function getQRCode($content, $path = NULL, $type = self::QRCODE_TYPE_LIMIT, $expire = 604800) {
        /* 获取 QRCodeTicket */
        $ticket = $this->_getQRCodeTicket($content, $type, $expire);
        $url = $this->mpUrl . 'cgi-bin/showqrcode?ticket=';
        $url .= $ticket;
        /* 此时 result 就是 QRCode 图像内容 */
        $result = Http::requestGet($url);
        if ($path != NULL) {
            $path = preg_match('/.*?\/$/', $path) ? $path : $path . '/';
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    throw new \Exception('创建目录失败');
                }
            }
            $filePath = $path . md5($content) . '.jpg';
            $file = true;
            if (!is_file($filePath)) {
                $file = file_put_contents($filePath, $result);
            }
            return $file === false ? false : $filePath;
        }
        header('Content-Type: image/jpeg');
        echo $result;
        exit;
    }
    
    /**
     * 获取 QRCodeTicket
     * @param $content ,QRCode内容标识
     * @param int $type ,二维码类型
     * @param int $expire ,如果是临时，表示其有效期
     * @return bool|string 成功返回 QRCodeTicket ,失败返回 false
     */
    private function _getQRCodeTicket($content, $type = self::QRCODE_TYPE_LIMIT, $expire = 604800) {
        $url = $this->apiUrl . 'cgi-bin/qrcode/create?access_token=';
        $url .= $this->_getAccessToken();
        $type_list = [
            self::QRCODE_TYPE_TEMP      => 'QR_SCENE',
            self::QRCODE_TYPE_LIMIT     => 'QR_LIMIT_SCENE',
            self::QRCODE_TYPE_LIMIT_STR => 'QR_LIMIT_STR_SCENE',
        ];
        $action_name = $type_list[$type];
        if ($type == self::QRCODE_TYPE_TEMP) {
            $data_arr['expire_seconds'] = $expire;
            $data_arr['action_name'] = $action_name;
            $data_arr['action_info']['scene']['scene_id'] = $content;
        } else if ($type == self::QRCODE_TYPE_LIMIT_STR) {
            $data_arr['action_name'] = $action_name;
            $data_arr['action_info']['scene']['scene_id'] = $content;
        } else {
            $data_arr['action_name'] = $action_name;
            $data_arr['action_info']['scene']['scene_id'] = $content;
        }
        $data = json_encode($data_arr);
        $result = Http::requestPost($url, $data);
        if (!$result) {
            $this->message = '获取 QRCodeTicket 失败';
            return false;
        } else {
            /* 处理响应数据 */
            $result_obj = json_decode($result);
            return $result_obj->ticket;
        }
    }
}