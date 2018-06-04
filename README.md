# wechat-sdk-php

### 主要功能：

+ 微信公众号菜单、消息、用户接口封装

+ 微信公众号支付功能（手机浏览器H5支付、微信浏览器内JS支付、微信扫码支付）

### 扩展安装：

+ composer命令 `composer require itxq/wechat-sdk-php`

### 引用扩展：

+ 当你的项目不支持composer自动加载时，可以使用以下方式来引用该扩展包

`require_once __DIR__ . '/vendor/autoload.php'`

### 使用扩展：

```
// 引入SDK
require_once __DIR__ . '/vendor/autoload.php';

// 微信公众号配置信息
$config = [
    'app_id'     => '替换成你的APPID',
    'app_secret' => '替换成你的APPSECRET',
];

// 微信公众号网页授权获取用户信息调用示例
$callbackUrl = '';  // 回调地址（为空时自动获取当前页面）
$state = '';   // 额外的参数，会原样返回
// 获取微信用户信息
$data = \itxq\wechat\wxsdk\WeChatLogin::ins($config)->login($callbackUrl);
// 转换为数组格式
$data = @json_decode($data, true);
var_dump($data);
```