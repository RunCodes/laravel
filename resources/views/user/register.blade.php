<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="_token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>注册</title>
    <link href="./static/layui/css/layui.css" rel="stylesheet">
</head>
<body>
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

    .demo-reg-container {
        border: 1px solid #ebebeb;
        padding: 20px;
    }

</style>

<body>
<div class="main">
    <div class="contain">
        <div class="title">register</div>
        <form class="layui-form">
            <div class="demo-reg-container">
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-username"></i>
                        </div>
                        <input type="text" name="name" value="" lay-verify="required" placeholder="name"
                               autocomplete="off" class="layui-input" lay-affix="clear">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-wrap">
                        <div class="layui-input-prefix">
                            <i class="layui-icon layui-icon-password"></i>
                        </div>
                        <input type="password" name="password" value="" lay-verify="required" placeholder="password"
                               autocomplete="off" class="layui-input" id="reg-password" lay-affix="eye">
                    </div>
                </div>
                <!--  <div class="layui-form-item">
                   <div class="layui-input-wrap">
                     <div class="layui-input-prefix">
                       <i class="layui-icon layui-icon-password"></i>
                     </div>
                     <input type="password" name="confirmPassword" value="" lay-verify="required|confirmPassword" placeholder="确认密码" autocomplete="off" class="layui-input" lay-affix="eye">
                   </div>
                 </div> -->
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="demo-reg">register</button>
                </div>
                <div class="layui-form-item demo-reg-other">
                    <a href="/admin/login" style="float: right; margin-top: 7px;">Log in to Existing account</a>
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
        var util = layui.util;

        // // 自定义验证规则
        // form.verify({
        //   // 确认密码
        //   confirmPassword: function(value, item){
        //     var passwordValue = $('#reg-password').val();
        //     if(value !== passwordValue){
        //       return '两次密码输入不一致';
        //     }
        //   }
        // });
        // 提交事件
        form.on('submit(demo-reg)', function (data) {
            var field = data.field; // 获取表单字段值

            $.ajax({
                url: "{{ url('admin/register')  }}",
                data: field,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (data) {

                    if (data.redirect)
                        window.location.href = data.redirect;
                }
                , error: function (data) {
                    layer.msg(data.responseJSON.message, {icon: 2});
                }
            })
            return false;
        });
    });
</script>

</body>
</html>
