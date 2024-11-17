<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Demo</title>
  <meta name="_token" content="{{ csrf_token() }}"/>
  <link href="./static/layui/css/layui.css" rel="stylesheet">
</head>
<style type="text/css">

  .logout{
    position: absolute;
    right: 100px;
  }
</style>
<body>

welcome to laravel framework!!

<button type="button" class="layui-btn layui-bg-red logout" lay-active="logout">logout</button>

<script src="./static/layui/layui.js"></script> 
<script>
layui.use(['util', 'table','laypage'], function(){

    var util = layui.util;
    var $    = layui.$;

    util.event('lay-active', {
        logout: function(){
            logout();
        }
    });

    function logout(){

        $.ajax({
            url:"{{ url('admin/logout')  }}",
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success:function(data){
                if(data.redirect){
                   window.location.href = data.redirect;  
                }
            },
            error:function(data){
                layer.msg(data.responseJSON.message,{icon:2});
            }
        })
    }


});
</script>

</body>
</html>
