<?php
/**
 *  ==================================================================
 *        文 件 名: Tools.php
 *        概    要: 工具类
 *        作    者: IT小强
 *        创建时间: 2018/6/6 8:47
 *        修改时间:
 *        copyright (c) 2016 - 2018 mail@xqitw.cn
 *  ==================================================================
 */

namespace itxq\wechat\lib;

/**
 * 工具类
 * Class Tools
 * @package itxq\wechat\lib
 */
class Tools
{
    /**
     * 下划线命名转驼峰命名
     * @param $str - 下划线命名字符串
     * @param $isFirst - 是否为大驼峰（即首字母也大写）
     * @return mixed
     */
    public static function underlineToHump($str, $isFirst = false) {
        $str = preg_replace_callback('/([\-\_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        if ($isFirst) {
            $str = ucfirst($str);
        }
        return $str;
    }
    
    /**
     * 驼峰命名转下划线命名
     * @param $str
     * @return mixed
     */
    public static function humpToUnderline($str) {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        $str = preg_replace('/^\_/', '', $str);
        return $str;
    }
    
    /**
     * 获取数组、对象下标对应值，不存在时返回指定的默认值
     * @param string|integer $name - 下标（键名）
     * @param array|object $data - 原始数组/对象
     * @param mixed $default - 指定默认值
     * @return mixed
     */
    public static function getSubValue($name, $data, $default = '') {
        if (is_object($data)) {
            $value = isset($data->$name) ? $data->$name : $default;
        } else if (is_array($data)) {
            $value = isset($data[$name]) ? $data[$name] : $default;
        } else {
            $value = $default;
        }
        return $value;
    }
    
    /**
     * 判断是否为微信浏览器
     * @return bool
     */
    public static function isMicroMessenger() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 数组序列化url字符串
     * @param $array - 数组（键值对）
     * @return string
     */
    public static function arraySerialize($array) {
        $str = '';
        if (!is_array($array) || count($array) < 1) {
            return $str;
        }
        foreach ($array as $k => $v) {
            $str .= (empty($str)) ? '?' : '&';
            $str .= $k . '=' . $v;
        }
        return $str;
    }
    
    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public static function isMobile() {
        if (self::server('HTTP_VIA') && stristr(self::server('HTTP_VIA'), "wap")) {
            return true;
        } elseif (self::server('HTTP_ACCEPT') && strpos(strtoupper(self::server('HTTP_ACCEPT')), "VND.WAP.WML")) {
            return true;
        } elseif (self::server('HTTP_X_WAP_PROFILE') || self::server('HTTP_PROFILE')) {
            return true;
        } elseif (self::server('HTTP_USER_AGENT') && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', self::server('HTTP_USER_AGENT'))) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 获取server参数
     * @param  string $name 数据名称
     * @param  string|null $default 默认值
     * @return null|string|array
     */
    public static function server($name = '', $default = null) {
        if (empty($name)) {
            return $_SERVER;
        } else {
            $name = strtoupper($name);
        }
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }
    
    /**
     * 生成随机字符串
     * @param int $length - 指定生成字符串的长度
     * @param string $type - 指定生成字符串的类型（all-全部，num-纯数字，letter-纯字母）
     * @return null|string
     */
    public static function cmRound($length = 4, $type = 'all') {
        $str = '';
        $strUp = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $strLow = 'abcdefghijklmnopqrstuvwxyz';
        $number = '0123456789';
        switch ($type) {
            case 'num':
                $strPol = $number;
                break;
            case 'letter':
                $strPol = $strUp . $strLow;
                break;
            default:
                $strPol = $strUp . $number . $strLow;
        }
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)];
        }
        return $str;
    }
    
    /**
     * 获取当前请求的URL
     * @return string
     */
    public static function getUrl() {
        $ssl = isset($_SERVER['HTTPS']) ? strip_tags($_SERVER['HTTPS']) : false;
        $server_name = strip_tags($_SERVER['SERVER_NAME']);
        $request_url = strip_tags($_SERVER['REQUEST_URI']);
        if (!$ssl || $ssl != 'on') {
            $url = 'http://' . $server_name . $request_url;
        } else {
            $url = 'https://' . $server_name . $request_url;
        }
        return $url;
    }
}