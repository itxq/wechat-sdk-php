<?php
/**
 *  ==================================================================
 *        文 件 名: WeChatResource.php
 *        概    要: 公众号素材管理
 *        作    者: IT小强
 *        创建时间: 2018/6/4 9:42
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\wxsdk;

use itxq\wechat\lib\Http;

/**
 * 公众号素材管理
 * Class WeChatResource
 * @package itxq\wechat\wxsdk
 */
class WeChatResource extends WeChat
{
    //图片
    const MEDIA_IMAGE = 'image';
    // 音频文件
    const MEDIA_VOICE = 'voice';
    // 视频文件
    const MEDIA_VIDEO = 'video';
    // 缩略图
    const MEDIA_THUMB = 'thumb';
    // 图文素材
    const MEDIA_NEWS = 'news';
    
    /**
     * @var array - 允许上传的素材类型
     */
    protected $allowMediaType = [self::MEDIA_IMAGE, self::MEDIA_VOICE, self::MEDIA_VIDEO, self::MEDIA_THUMB, self::MEDIA_NEWS];
    
    /**
     * 获取永久素材总数
     * @param $type
     * @return bool|mixed
     */
    public function getMaterialCount($type = true) {
        $url = $this->apiUrl . 'cgi-bin/material/get_materialcount?access_token=' . $this->_getAccessToken();
        $result = Http::requestGet($url);
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        }
        if ($type === true) {
            return $result;
        }
        if (!isset($result[$type . '_count'])) {
            $this->message = '获取类型错误';
            return false;
        }
        return $result[$type . '_count'];
    }
    
    /**
     * 图片上传到微信服务器（图文消息专用，只能获取到URL）
     * 本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下。
     * @param $media - 本地服务器素材绝对路径
     * @return bool|string
     */
    public function uploadImage($media) {
        $media = realpath($media);
        if (!$media) {
            $this->message = '文件不存在';
            return false;
        }
        if (class_exists('\CURLFile')) {
            $media = new \CURLFile($media);
        } else {
            $media = '@' . $media;
        }
        $url = $this->apiUrl . 'cgi-bin/media/uploadimg?access_token=' . $this->_getAccessToken();
        $data = ['media' => $media];
        $result = Http::requestPost($url, $data);
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        }
        if (!isset($result['url'])) {
            $this->message = '图片转换失败';
            return false;
        }
        return strval($result['url']);
    }
    
    /**
     * 获取永久素材列表
     * @param string $type - 素材的类型
     * @param int $offset - 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * @param int $count - 返回素材的数量，取值在1到20之间
     * @return bool|mixed
     */
    public function getMaterialList($type = self::MEDIA_IMAGE, $offset = 0, $count = 10) {
        $url = $this->apiUrl . 'cgi-bin/material/batchget_material?access_token=' . $this->_getAccessToken();
        $data = json_encode(['type' => $type, 'offset' => $offset, 'count' => $count]);
        $result = Http::requestPost($url, $data);
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        }
        return $result;
    }
    
    /**
     * 新增永久素材
     * @param $media - 本地服务器素材绝对路径
     * @param string $type - 类型
     * @param string $title - 视频素材的标题(类型为video时必须)
     * @param string $introduction - 视频素材的描述(类型为video时必须)
     * @return bool|array
     * @throws \Exception
     */
    public function materialUpload($media, $type = self::MEDIA_IMAGE, $title = '', $introduction = '') {
        $media = realpath($media);
        if (!$media) {
            throw new \Exception('文件不存在');
        }
        if (class_exists('\CURLFile')) {
            $media = new \CURLFile($media);
        } else {
            $media = '@' . $media;
        }
        $url = $this->apiUrl . 'cgi-bin/material/add_material?access_token=' . $this->_getAccessToken() . '&type=' . $type;
        $data = ['media' => $media];
        if ($type == 'video') {
            $data['description'] = json_encode(['title' => $title, 'introduction' => $introduction]);
        }
        $result = Http::requestPost($url, $data);
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        }
        return $result;
    }
    
    /**
     * 删除永久素材
     * @param $mediaId - 素材ID
     * @return bool
     */
    public function materialDelete($mediaId) {
        $url = $this->apiUrl . 'cgi-bin/material/del_material?access_token=' . $this->_getAccessToken();
        $result = Http::requestPost($url, json_encode(['media_id' => $mediaId]));
        $result = json_decode($result, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        }
        return true;
    }
    
    /**
     * 新增临时素材
     * @param $media - 本地服务器素材绝对路径
     * @param string $type - 类型
     * @return bool|string
     * @throws \Exception
     */
    public function mediaUpload($media, $type = self::MEDIA_IMAGE) {
        $media = realpath($media);
        if (!$media) {
            throw new \Exception('文件不存在');
        }
        if (class_exists('\CURLFile')) {
            $media = new \CURLFile($media);
        } else {
            $media = '@' . $media;
        }
        $url = $this->apiUrl . 'cgi-bin/media/upload?access_token=' . $this->_getAccessToken() . '&type=' . $type;
        $data = ['media' => $media];
        $result = Http::requestPost($url, $data);
        $result = json_decode($result, true);
        if (isset($result['media_id'])) {
            return $result['media_id'];
        }
        $this->errCode = $result['errcode'];
        $this->message = $result['errmsg'];
        return false;
    }
    
    /**
     * 获取临时素材
     * @param $mediaId
     * @return bool|mixed
     */
    public function mediaGet($mediaId) {
        $url = $this->apiUrl . 'cgi-bin/media/get?access_token=' . $this->_getAccessToken() . '&media_id=' . $mediaId;
        $result = Http::requestGet($url);
        $result = json_decode($result, true);
        if (isset($result['errcode'])) {
            $this->errCode = $result['errcode'];
            $this->message = $result['errmsg'];
            return false;
        } else if (isset($result['video_url'])) {
            return $result['video_url'];
        } else {
            return $result;
        }
    }
}