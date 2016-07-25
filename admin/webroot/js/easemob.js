if (typeof jQuery === 'undefined') {
    throw new Error('jQuery no found. ');
}

function noticeOfEasemob(t,v,_this)
{
    console.log('点击了',t,v,_this);
}


$(function(){
    $LAB
        .script('/web-im/static/sdk/strophe.js')
        .script('/web-im/static/sdk/easemob.im-1.1.1.js')
        .script('/web-im/static/sdk/easemob.im.shim.js')
        .wait(function(){
            HaoConnect.request('easemob/get_my_auth_info').done(function(hResult){
                if (hResult.isResultsOK())
                {
                    if (Notification )
                    {
                        if (Notification.permission !== 'granted')
                        {
                            $.alert({
                                    title: false,
                                    content: '为了更好的为您提供服务，请在浏览器设置中，允许我们给您发送通知。',
                                    closeIcon: true,
                                    confirm: function(){
                                        Notification.requestPermission();
                                    }
                                });
                        }

                    }
                    $('#home_navcontainer').append('<a id="btn_easemob" class="navbar-brand" href="javascript:;">●</a>');
                    var easemob_user   = hResult.find('username');
                    var easemob_pwd    = hResult.find('password');
                    var easemob_appKey = hResult.find('appKey');
                    var conn   = new Easemob.im.Connection({multiResources:true});
                    conn.listen({
                            //连接成功时
                            onOpened : function() {
                                console.log('online');
                                $('#btn_easemob').addClass('easemob_online');
                                //启动心跳
                                if (conn.isOpened()) {
                                    conn.heartBeat(conn);
                                }
                                conn.setPresence();//设置用户上线状态，必须调用
                            }
                            //关闭时
                            ,onClosed : function() {
                                console.log('closed');
                                // conn.clear();
                                $('#btn_easemob').removeClass('easemob_online');
                            }
                            ,onTextMessage : function(message) {
                                /**处理文本消息，消息格式为：
                                    {   type :'chat',//群聊为“groupchat”
                                        from : from,
                                        to : too,
                                        data : { "type":"txt",
                                            "msg":"hello from test2"
                                        }
                                    }
                                */
                                $('#div_alert_notice').slideUp(function(){
                                    $(this).html('<div '+(message.ext.t?'onclick="noticeOfEasemob('+message.ext.t+','+message.ext.v+',this)"':'')+' class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong></strong>'+message.data+'</div>').slideDown();
                                });
                                if (Notification && Notification.permission === 'granted') {
                                    var notic = new Notification(
                                                    '您有一条信息'
                                                    , {
                                                         tag: message.id
                                                        ,body: message.data
                                                    }
                                                );
                                    notic.onclick = function() {
                                        console.log('点击事件：',message);
                                        if (message.ext.t)
                                        {
                                            noticeOfEasemob(message.ext.t,message.ext.v);
                                        }
                                        notic.close();
                                    };
                                }
                                console.log(message,message.ext.t,message.ext.v);
                            }
                            //离线时的回调方法
                            ,onOffline: function () {
                                if (conn.isOpened())
                                {
                                    conn.stopHeartBeat();
                                    conn.close();
                                }
                            }
                            //异常时的回调方法
                            ,onError: function(message) {
                                if (message['data'].indexOf('invalid_grant')>0)
                                {
                                    HaoConnect.post('easemob/reset_my_auth_info').done(function(){
                                        $('#btn_easemob').trigger('click');
                                    });
                                }
                                console.log(message);
                            }
                    });
                    $('#btn_easemob').click(function(){
                        if ($(this).hasClass('easemob_online'))
                        {
                            $.confirm({
                                title: false,
                                content: '您确定要暂时关闭通知功能吗？',
                                confirm: function(){
                                    conn.stopHeartBeat();
                                    conn.close();
                                }
                            });
                        }
                        else
                        {
                            conn.open({
                                    user : easemob_user,
                                    pwd : easemob_pwd,
                                    appKey : easemob_appKey
                            });
                        }
                    });
                    $('#btn_easemob').trigger('click');
                }
                });
        });
});
