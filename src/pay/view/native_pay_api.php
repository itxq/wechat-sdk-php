<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微信扫码支付</title>
    <style type="text/css">
        * {margin: 0;padding: 0;}
        
        body {background-color: #F7F7F7; }
        
        .title {height: 60px;line-height: 60px;text-align: center;border-bottom: 1px solid #DDD;background: #FFF;font-size: 100%;}
        
        .title .text {font-size: 20px;color: #333;font-weight: 400;vertical-align: middle;}
        
        .title .icon {display: inline-block;width: 41px;height: 36px; vertical-align: middle;margin: 0 7px 0 0;
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAkCAYAAAD7PHgWAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAIwSURBVFhHxZdBasMwEEV9hFwip8g5coUcIevcIRfIJXKGdNFFN4VAoYvSRUMXXTRQKCkKT1RGlmdkybbsDz+kii19zXyNppXpicvvxZy+T+bweTC7953ZvG7M6nlluX5Zm+3b1uw/9ub4dTTnn/P/W/nIEshCLIqI6qHK4vJpaUWzqRwkCWRSoiIt3IeIJfIpiAokjWMKC4lQLBCDKpAdSpOWIP7VIArkBWmiksTXZCxES2DJlHZx8bhoHaKGwDkiFzJMdy1wSs+FJHIIk+qlFUjupRdLk1NMXQ29R5rd6bYCp/YdB0IqL9e/qy3m7jn+rgir/3JJEgjtJiGSpNp/nrGq9MFgUaKi3cdEUrs6sUBlP4QfHd3Rz7WB5i8HBCfNKQ56RKCDDbnwjE/NXw63263hs06KgwGZ0IGdS1GP+cuBUtaVsRbFQYGcKB/0gEQr5i8HhGs+66Q4KJD05mKUbkgcFIgX8U8Kwno2iOKgwpQmk0hn+yzGsDjGyMIaYvVsEHMnDUtIcj3ry9wuxo/iaD5TaDfOQjlphogc1WcKKU9W4Jy9oEasB+qGtYjBB9Dd4bXAuZpWiX45qwUCm3PhhSnJFeqjIRDMKTIUB1oCgdaxlKR2S4kCAffuFCI5nLFuSBUISt4QbD6lQ4pGMLeAp5BNpzQdDqpA7lxpcsYh1xzpidmADfIM/5gRra7GVoIq0E8vi4RNgg8W5vQ78XxnLLV/jEEVSGSIQJ9OejwYcwfsM/fSMVk5ngAAAABJRU5ErkJggg==') center center no-repeat;
        }
        
        .content {margin: 20px auto;width: 90%;min-width: 500px;text-align: center;color: #333;border: 1px solid #E5E5E5;border-top: none;
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAHCAMAAAAoNw3DAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3NpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDE0IDc5LjE1MTQ4MSwgMjAxMy8wMy8xMy0xMjowOToxNSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDplZjI4OTIxMy1lYzE5LTRiOGUtOTVhMC1kODgyMjgyYjI2ZWQiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QjY1RUZEQjFFMzVDMTFFNEI2NzBEMEI1NjZBOENFMTMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QjY1RUZEQjBFMzVDMTFFNEI2NzBEMEI1NjZBOENFMTMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6ZjM0NDNlZmQtMDkwNy00NDc1LWJlOTYtNzRmOWRhZTg5MWVlIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOmVmMjg5MjEzLWVjMTktNGI4ZS05NWEwLWQ4ODIyODJiMjZlZCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqOuhZMAAAAtUExURff39////+7u7vX19fb29vr6+vHx8fT09PPz8+/v7+3t7erq6vDw8Pj4+Ozs7EoyMs4AAABBSURBVHjadMdLDsAgCAVAHqD4v/9xTWtMG6KzG6KXlEQ/rswU67VNAYR6qRkegY+VhkXjoTljS0N9i+DT2XUKMAClGgHHUVvOJwAAAABJRU5ErkJggg==') top center repeat-x #FFF;
        }
        
        .content .time {font-size: 20px;padding: 30px 10px 0 10px;}
        
        .content .time .red {color: #F00;font-weight: 700;}
        
        .content .time .wx_pay_time {color: #00B0F0;}
        
        .content .fee {font-size: 48px;margin-top: 20px;}
        
        .content .qr-code {margin: 30px 0 0 0;padding: 0;}
        
        .content .qr-code img {width: 260px;height: 260px;}
        
        .content .main {margin: 60px 80px 0 80px;padding: 25px 0 0 0;border-top: 1px solid #E5E5E5;}
        
        .content .main dl {margin: 0;padding: 0;font-size: 14px;text-align: right;line-height: 28px;}
        
        .content .main dl dt {float: left;margin: 0;padding: 0;}
        
        .content .bottom {margin: 40px 80px 0 80px;border-top: 1px dashed #E5E5E5;padding: 30px 0;position: relative;}
        
        .content .bottom .semi-circle {display: inline-block;width: 50px;height: 50px;position: absolute;top: -23px;background: #F7F7F7;border-radius: 50%;}
        
        .content .bottom .semi-circle.left {left: -105px;}
        
        .content .bottom .semi-circle.right {right: -105px;}
        
        .content .bottom .text {display: inline-block;vertical-align: middle;text-align: left;margin: 0 0 0 23px;padding: 0;font-size: 16px;line-height: 28px;}
        
        .content .bottom .icon {display: inline-block;width: 56px;height: 55px; vertical-align: middle;background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADgAAAA2CAYAAACSjFpuAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAEgSURBVGhD7ZqxDYNADEVvBJZgCvZiERZgG1KkSJMFUgWlDFWkyNE/gWSQJaC44jt+0i9iRM5P54LCSWam7yTds5Pm3kh1rSRd0qGUxjrTCnpG73CAy0LucHgPUt9q88W9lMY6cy9wgVN+f/yMp25sm9JYZx4JnOCW2ke7KvavPj9gAz2jd31Z2U2PJuaXHTgsPtlt+YEw3tyWPJbKaSXoBe0UgoxopxBkRDuFICPaKQQZ0U7+BeeaW0KQnRBkJwTZCUF2/AuuPms8xix6iln0lHlU3RKC7IQgOyHITgiy419Qf9Z4QTuFICPaKQQZ0U4hyIh2CkFGtNN/CbrcVXO/bWjti+qVYBbQs7kviivVxbMpjXXmkcApjyv+xO/OtsgPm6oGXKwqrB4AAAAASUVORK5CYII=') no-repeat;}
    </style>
</head>
<body>
<h1 class="title">
    <span class="icon"></span>
    <span class="text">微信支付</span>
</h1>
<div class="content">
    <div class="time" id="wx_pay_time_content">
        您正在使用微信扫码支付进行交易 二维码将在
        <span class="wx_pay_time" id="wx_pay_time">
            <?php echo \itxq\wechat\lib\Tools::getSubValue('timeout_express', $var, '0分钟0秒'); ?>
        </span>
        后失效，请及时付款！
        <span class="red">请勿重复刷新本页！</span>
    </div>
    <div class="fee">￥<?php echo \itxq\wechat\lib\Tools::getSubValue('fee', $var, 0); ?></div>
    <div class="qr-code">
        <img src="<?php echo \itxq\wechat\lib\Tools::getSubValue('native_pay_api_url', $var, 0); ?>" alt="微信扫码支付">
    </div>
    <div class="main">
        <dl>
            <dt>订单名称:</dt>
            <dd><?php echo \itxq\wechat\lib\Tools::getSubValue('body', $var, '微信支付'); ?></dd>
            <dt>订单价格:</dt>
            <dd>RMB&nbsp;<?php echo \itxq\wechat\lib\Tools::getSubValue('fee', $var, '0'); ?>元</dd>
            <dt>交易单号:</dt>
            <dd><?php echo \itxq\wechat\lib\Tools::getSubValue('out_trade_no', $var, ''); ?></dd>
            <dt>创建时间:</dt>
            <dd><?php echo date('Y-m-d H:i:s', \itxq\wechat\lib\Tools::getSubValue('ctime', $var, 0)); ?></dd>
        </dl>
    </div>
    <div class="bottom">
        <span class="semi-circle left"></span>
        <span class="semi-circle right"></span>
        <div class="icon"></div>
        <div class="text">
            <p>请使用微信扫一扫</p>
            <p>扫描二维码完成支付</p>
        </div>
    </div>
</div>
<script type="text/javascript">
    var wx_pay_timer = setInterval(function () {
        getWeChatPayTime();
    }, 1000);
    
    function getWeChatPayTime() {
        const _time = document.getElementById('wx_pay_time').innerText;
        const _timeArr = _time.split('分钟');
        const _i = parseInt(_timeArr[0]);
        const _s = parseInt(_timeArr[1]);
        const _newTime = parseInt((_i * 60 + _s) - 1);
        if (_newTime < 0) {
            document.getElementById('wx_pay_time_content').innerHTML = '<span class="red">支付二维码已失效，请重新获取！</span>';
            clearInterval(wx_pay_timer);
        } else {
            const _newI = parseInt(_newTime / 60);
            const _newS = parseInt(_newTime % 60);
            document.getElementById('wx_pay_time').innerText = _newI + '分钟' + _newS + '秒';
        }
    }
</script>
</body>
</html>