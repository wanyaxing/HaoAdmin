if (typeof jQuery === 'undefined') {
    throw new Error('jQuery no found. ');
}

HaoConnect = {
	'ajaxList':{}
	/** 请求接口，并立即得到结果。（不是异步哦） */
	,ajaxAsync: function( urlParam,  params, method){
		if ( !params ) { params = {};     }
		if ( !method ) { method = 'get';  }
		params['url_param'] = urlParam;
		var hResult;
		$.ajax({
               type: method
               ,url: '/ajax_haoconnect.php'
               ,data: params
               ,dataType: 'json'
               ,async:false//使用同步
               ,success: function(result)
		               {
		               		hResult         = new HaoResult(result);
		               }
               ,complete:function(jqXHR,textStatus)
		               {
		               		if (!hResult)
		               		{
		               			hResult = new HaoResult(textStatus,-1,'请求失败，请重试或联系管理员。');
		               		}
		               }
            });
		return hResult;
	}
	/** 异步请求，返回的是ajax异步对象(支持Promise哦.) */
	,request: function( urlParam,  params, method){
		if ( !params ) { params = {};     }
		if ( !method ) { method = 'get';  }
		params['url_param'] = urlParam;
		var ajax = $.ajax({
               type: method,
               url: '/ajax_haoconnect.php',
               data: params,
               dataType: 'json',
               async:true//使用异步
           })
		var deferred = ajax.then(function(data){
           		return new HaoResult(data);
           });
		this.ajaxList[deferred] = ajax;
		return deferred;
	}
	,abort:function(deferred){
		if (this.ajaxList[deferred])
		{
			this.ajaxList[deferred].abort();
			delete this.ajaxList[deferred];
			return true;
		}
		return false;
	}
	/** 请求接口，并直接读取结果中的数据 */
    ,find: function(path,urlParam,  params, method)
    {
        result = HaoConnect.request(urlParam,  params,method);
        return result.find(path);
    }
    /** 请求接口，并直接查询结果中的数据 */
    ,search: function(path,urlParam,  params, method)
    {
        result = HaoConnect.request(urlParam,  params,method);
        return result.search(path);
    }
}
