var HaoUploader = {
    uploadList : {}
    ,init : function(target){
        targetObj = target.constructor == String ? $('#' + target) : $(target);
        // console.log(targetObj);
        if (targetObj.length>1)
        {
            alert('注意，初始化时，只能接受唯一的dom对象');
            return false;
        }
        if (!targetObj.attr('haouploader-uuid'))
        {
            targetObj.attr('haouploader-uuid',Math.random().toString(36).substr(2));
        }
        var uuid = targetObj.attr('haouploader-uuid');
        var uploader;
        if (HaoUploader['uploadList'][uuid])
        {
            uploader = HaoUploader['uploadList'][uuid];
        }
        else
        {
            uploader = HaoUploader.initUploader(targetObj);
            uploader.uuid = uuid;
            HaoUploader['uploadList'][uuid] = uploader;
        }
        return uploader;
    }
    ,destroy:function(target){
        targetObj = target.constructor == String ? $('#' + target) : $(target);
        if (targetObj.length>1)
        {
            alert('注意，初始化时，只能接受唯一的dom对象');
            return false;
        }
        var uuid = targetObj.attr('haouploader-uuid');
        if (uuid && HaoUploader['uploadList'][uuid])
        {
            var uploader = HaoUploader['uploadList'][uuid];
            uploader.destroy();
            delete HaoUploader['uploadList'][uuid];
            return true;
        }
        return false;
    }
    ,initUploader: function (inputObj)
    {
        var dndObj = $('<span class="dnd_which_upload_to_qiniu"></span>')
                        .css({'display':'inline-block'})
                        .insertAfter(inputObj)
                        .attr('tabindex','0')
                        .attr('multiple',inputObj.attr('multiple'))
                        ;
        inputObj.appendTo(dndObj);
        var targetObj = $('<li class="pick_which_upload_to_qiniu">+</li>').css({'position':'relative'}).insertAfter(inputObj);
        // console.log(dndObj[0],targetObj[0]);

        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: true,

            /**
             * @property {Boolean} [prepareNextFile=false]
             * @namespace options
             * @for Uploader
             * @description 是否允许在文件传输时提前把下一个文件准备好。
             * 对于一个文件的准备工作比较耗时，比如图片压缩，md5序列化。
             * 如果能提前在当前文件传输期处理，可以节省总体耗时。
             */
            prepareNextFile: true,

            // swf文件路径
            swf: 'js/Uploader.swf',

            // 文件接收服务端。
            server: 'http://upload.qiniu.com',

            //允许文件重复
            duplicate: true,
            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: {id:targetObj[0],'multiple':inputObj.attr('multiple')?true:false},

            //验证文件总数量, 超出则不允许加入队列。
            fileNumLimit:inputObj.attr('multiple')?undefined:1,
            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            disableGlobalDnd: true,

            dnd: dndObj[0],
            paste: dndObj[0],

            //不压缩图片
            compress:false,

            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });


        uploader.updateInputValueOfInDND = function()
        {
            $(dndObj).find(inputObj).val($(dndObj).find('[url_preview]').map(function(){return $(this).attr('url_preview');}).get().join(','));
        }

        uploader.updateDNDOfInputValue = function()
        {
            $(dndObj).find('.filePreview').remove();
            $.each($(inputObj).val().split(','),function(index,imgSrc){
                if (imgSrc.indexOf('http')==0)
                {
                    uploader.newPreviewObjOfFile(null,imgSrc);
                }
            });
        }

        uploader.newPreviewObjOfFile = function(file,imgUrl)
        {
            $pick = $(uploader.options.pick.id);

            var $li = $(uploader.options.pick.id);

            if ($(dndObj).attr('multiple'))
            {
                $li = $( '<li class="filePreview">' + '</li>' );
                if (file)
                {
                    $li.attr('title',file.name);
                }
            }

            if ($li.find('.imgWrap').length==0)
            {
                $('<p class="progress"></p>').prependTo($li);
                $('<div class="imgWrap"></div>').prependTo($li);
            }

            var $prgress = $li.find('p.progress').css({'opacity':'0.5'}),

            $wrap = $li.find( 'div.imgWrap' ),

            showError = function( code ) {
                switch( code ) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;

                    default:
                        text = '上传失败，请重试';
                        break;
                }

                $prgress.text( text );
            };

            // 优化retina, 在retina下这个值是2
            var ratio = window.devicePixelRatio || 1;
            // 缩略图大小
            var thumbnailWidth = $pick.width() * ratio;
            var thumbnailHeight = $pick.height() * ratio;
            if (file)
            {
                if ( file.getStatus() === 'invalid' ) {
                    showError( file.statusText );
                } else {
                    // @todo lazyload
                    $wrap.text( '预览中' );


                    uploader.makeThumb( file, function( error, src ) {
                        var img;

                        if ( error ) {
                            $wrap.text( '不能预览' );
                            return;
                        }

                        img = $('<img src="'+src+'">').css({'width':'100%','height':'100%'});
                        $wrap.empty().append( img );
                        if (!$li.attr('url_preview'))
                        {
                            $prgress.text('待上传');
                        }
                    }, thumbnailWidth, thumbnailHeight );
                }
                file.on('statuschange', function( cur, prev ) {
                    if ( prev === 'progress' ) {
                        $prgress.hide().text('');
                    } else if ( prev === 'queued' ) {
                        $li.off( 'click' );
                    }

                    // 成功
                    if ( cur === 'error' || cur === 'invalid' ) {
                        console.log( file.statusText );
                        showError( file.statusText );
                    } else if ( cur === 'interrupt' ) {
                        showError( 'interrupt' );
                    } else if ( cur === 'queued' ) {
                        $prgress.css('display', 'block');
                    } else if ( cur === 'progress' ) {
                        $prgress.css('display', 'block');
                    } else if ( cur === 'complete' ) {
                        $prgress.hide().text('');
                    }

                    $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
                });
                file.previewObj = $li;
            }
            else if (imgUrl)
            {
                img = $('<img src="'+imgUrl+'?imageView2/1/w/'+thumbnailWidth+'/h/'+thumbnailHeight+'/">').css({'width':'100%','height':'100%'});
                $wrap.empty().append( img );
                $li.attr('url_preview',imgUrl);
                $prgress.css({'opacity':'0'});
            }


            if ($(dndObj).attr('multiple'))
            {

                $li.on( 'click', function() {
                    if (confirm('是否删除？'))
                    {
                        if (file)
                        {
                            uploader.removeFile( file , true);
                        }
                        uploader.updateInputValueOfInDND();
                        $(this).remove();
                    }
                });
                $li.insertBefore( uploader.options.pick.id );
                Sortable.create(dndObj[0],{
                                                    draggable:'.filePreview',
                                                    animation: 150,
                                                    onSort: function (evt) {
                                                            uploader.updateInputValueOfInDND();
                                                        }
                                                }
                                            );
            }
        }


        uploader.on('beforeFileQueued', function( file ){
            console.log('beforeFileQueued');
            if (!file.previewObj)
            {
                uploader.newPreviewObjOfFile(file);
            }
            return true;
        });

        //此处需要自定义，根据json.urlPreview来决定更新对应结果。 by axing
        uploader.on('uploadSuccess', function( file , ret ){
            console.log('uploadSuccess');
            var $li = $(file.previewObj);
            var json = (file.ret || ret );
            console.log(file , ret,json);
            if (json && json.urlPreview) {
                $li.attr('url_preview',json.urlPreview);
                $li.find('.progress').css({'opacity':'0'});
            } else {
                alert('上传失败，请检查');
            }
            uploader.updateInputValueOfInDND();
            uploader.removeFile(file);
        });


        uploader.on('uploadProgress', function(file,percentage){
            console.log('uploadProgress');
            console.log(file,percentage);
            var $li = $(file.previewObj);
            $li.find('.progress').text( parseInt(percentage * 100) + '%' ).css('opacity',1-percentage);
        });

        uploader.on('error', function(type){
            console.log(type);
        });

        uploader.updateDNDOfInputValue();

        return uploader;
    }

}

$(function(){

    $('.input_which_upload_to_qiniu').each(function(){
        HaoUploader.init(this);
    });


    // HaoUploader.init($('.input_which_upload_to_qiniu')[0]);
    // setTimeout(function(){
    //     HaoUploader.init($('.input_which_upload_to_qiniu')[1]);
    // },100);

});
