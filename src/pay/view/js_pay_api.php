<?php
$var = isset($var) ? $var : [];
$jsApiParameters = isset($var['jsApiParameters']) ? $var['jsApiParameters'] : [];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>正在请求支付...</title>
    <style type="text/css">
        * {margin: 0;padding: 0;}
        
        body, html {height: 100%;-webkit-tap-highlight-color: transparent;}
        
        body {background-color: #F8F8F8;font-family: -apple-system-font, Helvetica Neue, Helvetica, sans-serif; line-height: 1.6;}
        
        a {text-decoration: none;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);}
        
        .weui-form-preview__btn {
            position: relative;
            display: block;
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            flex: 1;
            color: #09BB07;
            text-align: center;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        
        .weui-form-preview__btn:after {
            content: " ";
            position: absolute;
            left: 0;
            top: 0;
            width: 1px;
            bottom: 0;
            border-left: 1px solid #D5D5D6;
            color: #D5D5D6;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            -webkit-transform: scaleX(0.5);
            transform: scaleX(0.5);
        }
        
        .weui-form-preview__ft:before {
            content: " ";
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            height: 1px;
            border-top: 1px solid #D5D5D6;
            color: #D5D5D6;
            -webkit-transform-origin: 0 0;
            transform-origin: 0 0;
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }
        
        .weui-form-preview:after {
            content: " ";
            position: absolute;
            left: 0;
            bottom: 0;
            right: 0;
            height: 1px;
            border-bottom: 1px solid #E5E5E5;
            color: #E5E5E5;
            -webkit-transform-origin: 0 100%;
            transform-origin: 0 100%;
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }
        
        .page__hd {padding: 20px;}
        
        .page__desc {margin-top: 5px;color: #888;text-align: center;font-size: 14px;}
        
        .page__title {text-align: center;font-size: 20px;font-weight: 400;}
        
        .weui-form-preview {position: relative;background-color: #FFF;}
        
        .weui-form-preview__hd {position: relative;padding: 10px 15px;text-align: right;line-height: 2.5em;}
        
        .weui-form-preview__bd {padding: 10px 15px;font-size: .9em;text-align: right;color: #808080;line-height: 2;}
        
        .weui-form-preview__ft {position: relative;line-height: 50px;display: -webkit-box;display: -webkit-flex;display: flex;}
        
        .weui-form-preview__item {overflow: hidden;}
        
        .weui-form-preview__label {float: left;margin-right: 1em;min-width: 4em;color: #808080;text-align: justify;text-align-last: justify;}
        
        .weui-form-preview__hd .weui-form-preview__value {font-style: normal;font-size: 1.6em;}
        
        .weui-form-preview__value {display: block;overflow: hidden;word-break: normal;word-wrap: break-word;}
        
        .weui-form-preview__btn_primary {color: #09BB07;}
        
        .weui-form-preview__btn_default {color: #808080;}
    </style>
</head>
<body>
<div class="page__hd">
    <h1 class="page__title" id="h1">正在请求支付...</h1>
    <p class="page__desc" id="p">正在请求支付，请勿关闭当前页面！</p>
</div>
<div class="weui-form-preview">
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">订单名称：</label>
            <span class="weui-form-preview__value">
                <?php echo \itxq\wechat\lib\Tools::getSubValue('body', $var, ''); ?>
            </span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">订单价格：</label>
            <span class="weui-form-preview__value">
                ¥ <?php echo \itxq\wechat\lib\Tools::getSubValue('fee', $var, 0); ?>
            </span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">交易单号：</label>
            <span class="weui-form-preview__value">
                <?php echo \itxq\wechat\lib\Tools::getSubValue('out_trade_no', $var, ''); ?>
            </span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">创建时间：</label>
            <span class="weui-form-preview__value">
                <?php echo date('Y-m-d H:i:s', \itxq\wechat\lib\Tools::getSubValue('ctime', $var, '')); ?>
            </span>
        </div>
    </div>
    <div class="weui-form-preview__ft">
        <a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:window.history.go(-1);">支付失败</a>
        <a class="weui-form-preview__btn weui-form-preview__btn_primary" target="_blank"
           href="<?php echo \itxq\wechat\lib\Tools::getSubValue('return_url', $var, 'javascript:window.history.go(-1);'); ?>">
            支付成功
        </a>
    </div>
</div>
<script type="text/javascript">
    wxPay();
    
    function onBridgeReady() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', {
                /* 公众号名称，由商户传入 */
                "appId": "<?php echo \itxq\wechat\lib\Tools::getSubValue('appId', $jsApiParameters, ''); ?>",
                /* 时间戳，自1970年以来的秒数 */
                "timeStamp": "<?php echo \itxq\wechat\lib\Tools::getSubValue('timeStamp', $jsApiParameters, ''); ?>",
                /* 随机串 */
                "nonceStr": "<?php echo \itxq\wechat\lib\Tools::getSubValue('nonceStr', $jsApiParameters, ''); ?>",
                "package": "<?php echo \itxq\wechat\lib\Tools::getSubValue('package', $jsApiParameters, ''); ?>",
                /* 微信签名方式 */
                "signType": "<?php echo \itxq\wechat\lib\Tools::getSubValue('signType', $jsApiParameters, ''); ?>",
                /* 微信签名 */
                "paySign": "<?php echo \itxq\wechat\lib\Tools::getSubValue('paySign', $jsApiParameters, ''); ?>"
            },
            function (res) {
                var return_url = "<?php echo \itxq\wechat\lib\Tools::getSubValue('return_url', $var, 'window.history.go(-1);'); ?>";
                if (res.err_msg === "get_brand_wcpay_request:ok") {
                    document.title = '支付成功';
                    document.getElementById('h1').innerText = '支付成功';
                    document.getElementById('p').innerText = '订单已成功完成支付,页面正在跳转中...';
                } else if (res.err_msg === "get_brand_wcpay_request:cancel") {
                    document.title = '取消支付';
                    document.getElementById('h1').innerText = '取消支付';
                    document.getElementById('p').innerText = '您已主动取消支付,页面正在跳转中...';
                } else if (res.err_msg === "get_brand_wcpay_request:fail") {
                    document.title = '支付失败';
                    document.getElementById('h1').innerText = '支付失败';
                    document.getElementById('p').innerText = '订单支付失败,页面正在跳转中...';
                } else {
                    document.title = '支付失败';
                    document.title = '支付失败';
                    document.getElementById('h1').innerText = '支付失败';
                    document.getElementById('p').innerText = '订单支付失败,页面正在跳转中...';
                }
                if (return_url && return_url !== '' && return_url !== null) {
                    window.location.href = return_url;
                } else {
                    window.history.go(-1);
                }
            }
        );
    }
    
    function wxPay() {
        if (typeof WeixinJSBridge === "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        } else {
            onBridgeReady();
        }
    }
</script>
</body>
</html>