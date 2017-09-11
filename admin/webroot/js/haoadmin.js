if (typeof jQuery === 'undefined') {
    throw new Error('jQuery no found. ');
}

HaoAdmin = {
     'jcList':{}
    ,show:function(jcKey,options)
    {
        if (this.jcList[jcKey])
        {
            var that = this.jcList[jcKey];
            $(that.$el).appendTo('body');
            return this.jcList[jcKey];
        }
        var _this = this;
        if (!options){options={};}
        options = $.extend({
             title: '&nbsp;'
            ,confirmButton: false
            ,cancelButton: false
            ,closeIcon: true
            ,animation: 'scale'
            ,columnClass: 'col-md-8 col-md-offset-2'
            },options);
        options['content'] = 'url:'+jcKey;
        if (typeof(_hmt)!='undefined'){_hmt.push(['_trackPageview', jcKey]);}

        options['contentLoaded'] = function(){
            var that = this;
            setTimeout(function(){
                that.$b.find('form[data-pjax]').removeAttr('data-pjax');
                haoPageInit(that.$b);
                NProgress.done();
            },1000);
        };
        var oldOpen,oldClose,oldAction;
        if (options['onOpen']){oldOpen = options['onOpen'];}
        options['onOpen'] = function(){
            var that = this;
            $(this.$el).attr('id',jcKey);
            $(this.$b).submit(function(e){
                if (!$.html5Validate.isAllpass($(this).find('form')))
                {
                    return false;
                }
                if (!options['beforeSubmit'] || options['beforeSubmit'].apply(that,arguments)!=false)
                {
                    if (!options['onSubmit'] || options['onSubmit'].apply(that,arguments)!=false)
                    {
                        var params = $(this).find('form').serializeArray();
                        console.log('debug submit',params);
                        /* Because serializeArray() ignores unset checkboxes and radio buttons: */
                        params = params.concat(
                                    $(this).find('form').find('input[type=checkbox]:not(:checked)').map(
                                        function() {
                                            return {"name": this.name, "value": 0}
                                        }).get()
                                    );
                        var ajaxUrl = $(this).find('form').attr('action');
                        if (ajaxUrl==='')
                        {
                            ajaxUrl = jcKey;
                        }
                        console.log(ajaxUrl);
                        if (!ajaxUrl || ajaxUrl==null)
                        {
                            if ($(this).find('[name=url_param]').length>0)
                            {
                                ajaxUrl = '/ajax_haoconnect.php';
                            }
                            else
                            {
                                return true;
                            }
                        }
                        var method = $(this).find('form').attr('method') || 'post';
                        e.preventDefault();
                        $.ajax({
                           type: method,
                           url: ajaxUrl,
                           data: params,
                           dataType: 'text',
                           async:true,//是否使用异步
                           success: function(responseText){
                                try
                                {
                                    result = jQuery.parseJSON( responseText );
                                    if (result['errorCode'] == 0)
                                    {
                                        if (!options['submitSuccess'] || options['submitSuccess'].apply(that,[result])!=false)
                                        {
                                            if ( typeof(result['result']) == 'string' )
                                            {
                                                that.setContent(result['result']);
                                                haoPageInit(that.$b);
                                            }
                                            else
                                            {
                                                that.close();
                                                // $.alert({
                                                //  title: false
                                                //  ,backgroundDismiss:true
                                                //  ,content:'完成'
                                                // });
                                            }
                                        }
                                        if (options['afterSubmitSuccess'])
                                        {
                                            options['afterSubmitSuccess'].apply(that,[result]);
                                        }
                                    }
                                    else
                                    {
                                        if (!options['submitFail'] || options['submitFail'].apply(that,[result])!=false)
                                        {
                                            $.alert({
                                                title: false
                                                ,backgroundDismiss:true
                                                ,content:result['errorStr']
                                            });
                                        }
                                        if (options['afterSubmitFail'])
                                        {
                                            options['afterSubmitFail'].apply(that,[result]);
                                        }
                                    }
                                }
                                catch(e)
                                {
                                    if (!options['submitSuccess'] || options['submitSuccess'].apply(that,responseText)!=false)
                                    {
                                        that.setContent(responseText);
                                        haoPageInit(that.$b);
                                    }
                                    if (options['afterSubmitSuccess'])
                                    {
                                        options['afterSubmitSuccess'].apply(that,[result]);
                                    }
                                }


                           }
                        });
                    }
                }
            });
            if (oldOpen){oldOpen();}
            NProgress.done();
        };

        if (options['onClose']){oldClose = options['onClose'];}
        options['onClose'] = function(){
            if (oldClose){oldClose();}
            haoPageOutIt(this.$b);
            delete _this.jcList[jcKey];
        };

        if (options['onAction']){oldAction = options['onAction'];}
        options['onAction'] = function(ac){
            if (oldAction){oldAction(ac);}
        };
        NProgress.configure({ parent:($('#home_navcontainer').length>0?'#'+$('#home_navcontainer').attrId():'body'),direction:'leftToRightIncreased'});
        NProgress.start();
        var jc = jconfirm(options);
        this.jcList[jcKey] = jc;
        return jc;
    }
    ,findJc:function(_this){
        var jcKey = $(_this).closest('.jconfirm').attr('id');
        if (jcKey)
        {
            return this.jcList[jcKey];
        }
        return null;
    }
    ,closeJc:function(_this)
    {
        var jc = this.findJc(_this);
        if (jc)
        {
            jc.close();
        }
    }
    ,closeAllJc:function()
    {
        for (var jcKey in  this.jcList) {
            var jc = this.jcList[jcKey];
            if (jc)
            {
                jc.close();
            }
        }
    }
    ,user_login:function(e){
        $('#home_container').fadeOut(500);
        var options = {
             title: false
            ,confirmButton: false
            ,cancelButton: false
            ,closeIcon: false
            ,animation: 'scale'
            ,submitFail:function(result){
                // if (result['errorCode'] == 134)
                // {//验证码错误
                    $('[name="captcha_code"]').val('');
                    $('[name="captcha_key"]').siblings('img').trigger('click');
                // }
                return true;
            }
            ,onClose:function(){
                window.location.reload();
            }
        }
        return this.show('/edit/user_login',options);
    }
    ,update:function(_this,_id)
    {
        if (!_id && _this)
        {
            _id = $(_this).find('td').eq(0).html();
        }
        if (!_id || _id=='')
        {
            $(_this).closest('tr,.tr_li').remove();
        }
        var $tbody = $(_this).closest('tbody,.tbody_div');
        if (_id)
        {
            if ($tbody.length==0 && $(_this).siblings('.table-responsive').length>0)
            {
                $tbody = $(_this).siblings('.table-responsive').find('tbody,.tbody_div');
            }
            if ($tbody.length>0)
            {
                var pathname = $tbody.closest('[action]').attr('action');
                if (!pathname)
                {
                    pathname = window.location.pathname;
                }
                pathname += (pathname.indexOf('?')>0?'&':'?') +'is_only_filter_with_request=1&page_max=1&count_total=1&status=1,2,3,0&id='+_id
                $.get(pathname,function(result){
                    $(_this).trigger('haoadmin_detail_beforeReplace');
                    var trNode = $(result).find('tbody tr,.tr_li').addClass('success');
                    haoPageInit(trNode);
                    if (_this && $(_this).closest('tr,.tr_li').length>0)
                    {
                        $(_this).closest('tr,.tr_li').replaceWith(trNode);
                    }
                    else
                    {
                        $tbody.prepend(trNode);

                        $tbody.find('.haoadmin_noresults').remove();

                    }
                });
            }
        }
    }
    ,detail:function(_this,jcKey)
    {
        if (!jcKey || jcKey=='')
        {
            jcKey = $(_this).attr('href');
        }
        var options = {
                         title: '&nbsp;'
                        ,content: 'url:'+jcKey
                        ,confirmButton: false
                        ,cancelButton: false
                        ,closeIcon: true
                        ,animation: 'scale'
                        };
        options['afterSubmitSuccess'] = function(result){
            $(_this).triggerHandler('haoadmin_detail_beforeUpdate',[result,_this]);
            HaoAdmin.update(_this,typeof(result['results'])=='object'?result['results']['id']:null);
            $(this.$b).find('form').eq(0).triggerHandler('haoadmin_detail_beforeUpdate',[result,_this]);
        }
        HaoAdmin.show(jcKey,options);
        return false;
    }
}
