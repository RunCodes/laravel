<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录</title>
    <link href="./static/layui/css/layui.css" rel="stylesheet">
</head>
<style>
    * {
        padding: 0;
        margin: 0;
    }

    html, body {
        width: 100%;
        height: 100%;
    }

    .main {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .contain {
        width: 350px;
    }

    .title {
        text-align: center;
        font-size: 1.4rem;
    }

    .demo-login-other
    .layui-icon {
        position: relative;
        display: inline-block;
        margin: 0 2px;
        top: 2px;
        font-size: 26px;
    }

    .demo-login-container {
        border: 1px solid #ebebeb;
        padding: 20px;
    }

</style>
<body>
<div class="main">
    <div class="contain">
        <div class="title">login</div>
        <form class="layui-form">
            <div class="demo-login-container">
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-username"></i>
                        </div>
                        <input type="text" name="name" value="" lay-verify="required" placeholder="name"
                               lay-reqtext="Please fill in the username" autocomplete="off" class="layui-input"
                               lay-affix="clear">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-password"></i>
                        </div>
                        <input type="password" name="password" value="" lay-verify="required" placeholder="password"
                               lay-reqtext="Please fill in the password" autocomplete="off" class="layui-input"
                               lay-affix="eye">
                    </div>
                </div>
                <div class="layui-form-item">
                    <input type="checkbox" name="remember" lay-skin="primary" title="remember">
                    <a href="/admin/register" style="float: right; margin-top: 7px;">register</a>
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="demo-login">login</button>
                </div>

            </div>
        </form>
    </div>
</div>
</body>
<script src="./static/layui/layui.js"></script>
<script>

    layui.use(function () {
        var $ = layui.$;
        var form = layui.form;
        var layer = layui.layer;
        // 提交事件
        form.on('submit(demo-login)', function (data) {

            var field = data.field; // 获取表单字段值

            $.ajax({
                url: "{{ url('admin/login')  }}",
                data: field,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (data) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                },
                error: function (data) {
                    layer.msg(data.responseJSON.message, {icon: 2});
                }
            })
            // …
            return false; // 阻止默认 form 跳转
        });
    });
</script>
</html>
