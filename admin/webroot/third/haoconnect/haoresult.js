if (typeof jQuery === 'undefined') {
    throw new Error('jQuery no found. ');
}

HaoResult = (function()
{
    /** 将数据初始化成对象 */
	function HaoResult(results,errorCode,errorStr,extraInfo)
	{
		var modelType = 'HaoResult';
		if (!errorCode && !errorStr && !extraInfo && typeof(results['errorCode'])!='undefined')
		{
			errorCode = results['errorCode'];
			errorStr  = results['errorStr'];
			extraInfo = results['extraInfo'];
			modelType = results['modelType'];
			results   = results['results'];
		}
        this.errorCode   = errorCode;
        this.errorStr    = errorStr;
        this.extraInfo   = extraInfo;
        this.results     = results;
        this.modelType   = modelType;

        this.pathCache   = {};
        this.searchIndexString   = null;
	}

    /** 根据路径取数据，默认是results中取，也可以指定extraInfo>路径下取数据。 */
	HaoResult.prototype.find = function(path,defaultValue)
	{
        path = $.trim(path);
        if (this.pathCache[path])
        {
            return this.pathCache[path];
        }
        if (!path)
        {
        	console.log('warning: unvalid path.');
        	return null;
        }

        if ( path.indexOf('results>') !== 0 && path.indexOf('extraInfo>') !== 0 )
        {
            path = 'results>' + path;
        }

        paths = path.split('>');

        var changeValue = null;

        for (var index in paths)
        {
        	var keyItem = paths[index];
        	if (index==0)
            {
                if (keyItem=='extraInfo')
                {
                    changeValue = this.extraInfo;
                }
                else
                {
                    changeValue = this.results;
                }
            }
            else if (keyItem!='')
            {
                if (changeValue && changeValue[keyItem])
                {
                    changeValue = changeValue[keyItem];
                    continue;
                }
                changeValue = defaultValue;
                break;
            }
        }

        value = this.value(changeValue);
        this.pathCache[path] = value;
        return value;
    }

    /** 传入值如果是model，则以当前Result为框架构建新Result，否则直接返回。 */
    HaoResult.prototype.value = function(value)
    {
        if ($.isArray(value))
        {
            var array = [];
            for (var key in value)
            {
            	var tmpValue = value[key];
                array.push(this.value(tmpValue));
            }
            return array;
        }
        else if (typeof(value) == 'object')
        {
            if (value.modelType)
            {
                return new HaoResult(value, this.errorCode, this.errorStr, this.extraInfo);
            }
            else
            {
                return value;
            }
        }
        return value;
    }

    HaoResult.prototype.getKeyIndexArray = function(target)
    {
        var keyList = [];
        if (typeof(target) == 'object')
        {
            for (var key in target) {
                keyList.push(key + '');
                var objc = target[key];
                if (typeof(objc) == 'object') {
                    var keyListTemp = this.getKeyIndexArray(objc);
                    for (var j in keyListTemp)
                    {
                    	keyList.push(key + ">" + keyListTemp[j]);
                    }
                }
            }
        }
        return keyList;
    }


    /**
     * 根据path取值，如果不是数组，就转成数组
     * @param  string $path
     * @return array
     */
    HaoResult.prototype.findAsList = function(path)
    {
        value = this.find(path);

        if ( typeof(value)!=='object' || value instanceof HaoResult)
        {
            value = [value];
        }

        return value;
    }

    /**
     * 根据path取值，如果不是字符串，就转成字符串
     * @param  string path
     * @return string
     */
    HaoResult.prototype.findAsString = function(path)
    {
        value = this.find(path);

        if (typeof(value) != 'string')
        {
            value = value+"";
        }

        return value;
    }


    /**
     * 根据path取值，如果不是数字，就转成数字
     * @param  string path
     * @return int
     */
    HaoResult.prototype.findAsInt = function(path)
    {
        value = this.find(path);

        if (typeof(value)!='number')
        {
            value = parseInt(value);
        }

        return value;
    }

    /**
     * 根据path取值，如果不是HaoResult类型，就转成HaoResult类型
     * @param  string path
     * @return HaoResult
     */
    HaoResult.prototype.findAsResult = function(path)
    {
        value = this.find(path);

        if (!(value instanceof HaoResult))
        {
            value = new HaoResult(value, this.errorCode, this.errorStr, this.extraInfo);
        }

        return value;
    }


    /** 在结果中进行搜索，返回结果是数组（至少也是空数组） */
    HaoResult.prototype.search = function(path)
    {
        if (this.searchIndexString == null)
        {
        	var resultObjc = {};
        	resultObjc['results']       = this.results;
        	resultObjc['extraInfo']     = this.extraInfo;
            var searchIndex             = this.getKeyIndexArray( resultObjc );
            this.searchIndexString      = searchIndex.join("\n");
        }

        path = $.trim(path);

        if ( path.indexOf('results>') !== 0 && path.indexOf('extraInfo>') !== 0 )
        {
            path = 'results>' + path;
        }

        var result = [];
        var reg = new RegExp('(^|\\s)(' + path + ')(|\\s+)','g');
        var _this = this;
        this.searchIndexString.replace(reg,function($0,$1,$2,$3){
        	result.push(_this.find($2));
        });
        return result;
    }

    /** 判断当前实例是否目标model */
    HaoResult.prototype.isModelType = function(modelType)
    {
        return modelType.toLowerCase() == this.modelType.toLowerCase();
    }

    /**
     * 判断是否等于目标ErroCode
     * @param  array  errorCode  目标errorCode
     * @return boolean            是否一致
     */
    HaoResult.prototype.isErrorCode = function(errorCode)
    {
        return this.errorCode === errorCode;
    }

    /**
     * 判断是否正确获得结果
     * @return boolean            是否正确获得
     */
    HaoResult.prototype.isResultsOK = function()
    {
        return this.isErrorCode(0) ;
    }

    /** 返回字典类型数据（重新包装成字典） */
    HaoResult.prototype.properties = function()
    {
        return {
                    'errorCode'  : this.errorCode,
                    'errorStr'   : this.errorStr,
                    'extraInfo'  : this.extraInfo,
                    'results'    : this.results
		        };
    }

	return HaoResult;
})();
