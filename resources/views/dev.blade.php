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
    .about {
        text-align: center;
    }

    .logout {
        position: absolute;
        right: 100px;
    }

    .main {
        margin: 1rem 0 0 1rem;
    }

    .layui-input-wrap {
        width: 500px;
    }

    .layui-btn-container {
        margin-top: 10px;
    }

    #downloadReduce {
        margin-left: 10px;
    }

    #downloadAdd,
    #downloadReduce {
        white-space: nowrap;
        word-wrap: break-word;
        word-spacing: normal;
    }
</style>
<body>

<p class="about">After leaving the page, you need to download it again</P>
<button type="button" class="layui-btn layui-bg-red logout" lay-active="logout">logout</button>


<div class="main">
    <div class="layui-form">
        <div class="layui-input-wrap">
            <input type="text" lay-affix="clear" placeholder="please input" class="layui-input" lay-verify="required">
        </div>
    </div>
    <div class="layui-btn-container">
        <button type="button" class="layui-btn" lay-active="execute">Execute</button>
        <button type="button" class="layui-btn layui-bg-blue" lay-active="excel">Export Excel</button>
        <button type="button" class="layui-btn layui-bg-orange" lay-active="json">Export Json</button>
        <p id="downloadAdd"></p>
        <p id="downloadReduce"></p>
    </div>
</div>

<div id="error"></div>

<table class="ui-usertable" id="test" lay-filter="demo"></table>
<div id="laypage"></div>

<script src="./static/layui/layui.js"></script>
<script>

    layui.use(['util', 'table', 'laypage'], function () {

        var util = layui.util;
        var $ = layui.$;
        var table = layui.table;
        var laypage = layui.laypage;
        var downloadCount = 0;

        util.event('lay-active', {
            execute: function () {
                var sql = $('input').val();
                requestData(sql);
            },
            excel: function () {
                var sql = $('input').val();
                requestData(sql, 'excel');
            },
            json: function () {
                var sql = $('input').val();
                requestData(sql, 'json');
            },
            logout: function () {
                logout();
            }
        });

        function logout() {

            $.ajax({
                url: "{{ url('admin/logout')  }}",
                type: 'get',
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
        }

        function periodicFileDownload(url, fid, interval) {
            let isRequesting = false; // 标记请求状态

            const timer = setInterval(() => {
                if (isRequesting) return; // 防止重复请求
                isRequesting = true;

                layui.jquery.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response, status, xhr) {
                        isRequesting = false;
                        if (xhr.status === 200) {
                            downloadCount--;
                            $('#downloadReduce').html(`There is ${downloadCount} file left to download`);
                            clearInterval(timer);
                            window.location.href = "{{ url('admin/download') }}?fileName=" + fid
                        }

                    },
                    error: function (error) {
                        isRequesting = false;
                        console.error('download failed:', error);
                    }
                });
            }, interval);
        }

        function requestData(sql, exportType = null) {

            $('#error').html('');
            // 获取用户输入的 SQL 查询
            var page = 1;//页码全局变量
            var limit = 10;//分页大小全局变量
            table.render({
                elem: '#test'//容器ID
                , url: "{{ url('admin/execute') }}"//数据获取接口地址
                , method: 'post'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
                , where: {
                    page: page,
                    sql: sql,
                    limit: limit,
                    exportType: exportType
                }
                , height: 'full-85'
                , limit: limit //每页默认显示的数量
                , cols: []
                , id: 'testReload'
                , done: function (res, curr, count) {

                    if (res.fid) {
                        downloadCount++;
                        $('#downloadAdd').html(`current ${downloadCount} files are ready to download`);
                        periodicFileDownload("{{ url('admin/consult') }}?fileName=" + res.fid, res.fid, 5000);
                    }

                    var columns = []; // 动态生成表头
                    var tableData = res.data; // 获取数据
                    var totalCount = res.total; // 总条数
                    if (tableData.length > 0) {
                        var firstRow = tableData[0];
                        for (var key in firstRow) {
                            if (firstRow.hasOwnProperty(key)) {
                                columns.push({
                                    field: key,
                                    title: key
                                });
                            }
                        }
                    }
                    table.init('demo', {//转换成静态表格
                        cols: [columns]//将处理后的mycars传入cols
                        , data: tableData//设置静态表格数据
                        , limit: limit//设置静态表格页面数据条数（分页大小）
                        , count: res.total//设置静态表格总数据条数
                    });
                    laypage.render({
                        elem: 'laypage'//分页容器ID
                        , count: res.total//设置分页数据总数
                        , curr: page//当前页码
                        , limit: limit//分页大小
                        , layout: ['prev', 'page', 'next', 'skip', 'count', 'limit']
                        , jump: function (obj, first) {//跳转方法
                            if (!first) {//若不为第一页
                                page = obj.curr;//设置全局变量page 为当前选择页码
                                limit = obj.limit;//设置全局变量limit 为当前选择分页大小
                                table.reload('testReload', {//重新加载表格
                                    where: { //接口参数，page为分页参数
                                        page: page,
                                        limit: limit,
                                        sql: sql,
                                        check: 1
                                    }
                                });
                            }
                        }
                    })
                }, error: function (data) {

                    $('#error').html(data.responseJSON.message);
                    // layer.msg(data.responseJSON.message,{icon:2});
                }
            });

        }


    });
</script>

</body>
</html>
