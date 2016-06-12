if (!WebUploader)
{
    alert('please insert this script file after webuploader.js ');
}


//axing add
WebUploader.Uploader.register({
    'init':function(opts){
        console.log('init',this);
        var uploader = this.owner;
        uploader.on('fileQueued', function (file) {
            file.setStatus( 'queued' );
        });
        uploader.on('uploadBeforeSend', function (block, data, header) {
            data['token'] = block.file.token;
        });
        uploader.on('uploadSuccess', function (file, ret) {
            if (ret && ret.urlPreview)
            {
                ret._raw = utils.json2str({'state':'SUCCESS','url':ret['urlPreview'],'title':ret['urlPreview'].replace(/.*\//g,''),'original':ret['name'],'type':ret['ext'],'size':ret['size']});
            }
        });
    }
    ,'before-send':function(block){
        var uploader = this.owner;
    }
    ,'before-send-file': function(file){
        console.log('before-send-file init',this);
        var uploader = this.owner;
        uploader.option('server', 'http://upload.qiniu.com');
        uploader.option('fileVal', 'file');
        // var file = fileObj.source;
        var deferred = WebUploader.Deferred();
        // 返回的是 promise 对象
        uploader.md5File(file.source)
            // 可以用来监听进度
            .progress(function(percentage) {
                console.log('Percentage:', percentage);
            })
            // 处理完成后触发
            .then(function(ret) {
                file.md5 = ret;
                $.ajax({
                   type: 'POST',
                   url: editor.getActionUrl('getuploadtokenforqiniu'),
                   data: {
                             'md5':file.md5
                            ,'filesize':file.size
                            ,'filetype':file.ext
                         },
                   dataType: 'json',
                   async:true,//是否使用异步
                   success: function(result){
                        if (result['errorCode'] == 0)
                        {
                            file.token = result['results']['uploadToken'];
                            if (result['results']['isFileExistInQiniu'])
                            {
                                file.ret = utils.json2str({'state':'SUCCESS','url':result['results']['urlPreview'],'title':result['results']['urlPreview'].replace(/.*\//g,''),'original':result['results']['name'],'type':result['results']['ext'],'size':result['results']['size']});
                                console.log('秒传：',result['results']['urlPreview']);
                            }
                        }
                        else
                        {
                            alert(result['errorStr']);
                        }
                        // 结束此promise, webuploader接着往下走。
                        deferred.resolve();
                   }
                });
            });
        return deferred.promise();
    }
    ,'before-send':function(block){
        var deferred = WebUploader.Deferred();
        if (block.file.ret)
        {
            deferred.reject();
        }
        else
        {
            deferred.resolve();
        }
        return deferred.promise();
    }
});
