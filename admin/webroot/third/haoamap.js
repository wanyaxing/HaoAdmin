var HaoAmap = {
    amapList : {}
    ,init : function(target){
        inputObj = target.constructor == String ? $('#' + target) : $(target);
        if (inputObj.length>1)
        {
            alert('注意，初始化时，只能接受唯一的dom对象');
            return false;
        }
        if (!inputObj.attr('haoamap-uuid'))
        {
            inputObj.attr('haoamap-uuid',Math.random().toString(36).substr(2));
            if (inputObj.parent().width()<200)
            {
                inputObj.parent().width(200)
            }
            if (inputObj.parent().height()<inputObj.parent().width()*0.8)
            {
                inputObj.parent().height(inputObj.parent().width() * 0.8);
            }
        }
        var uuid = inputObj.attr('haoamap-uuid');
        var map;
        if (HaoAmap['amapList'][uuid])
        {
            map = HaoAmap['amapList'][uuid];
        }
        else
        {
            map = HaoAmap.initAmap(inputObj);
            map.uuid = uuid;
            HaoAmap['amapList'][uuid] = map;
        }
        return map;
    }
    ,destroy:function(target){
        targetObj = target.constructor == String ? $('#' + target) : $(target);
        if (targetObj.length>1)
        {
            alert('注意，初始化时，只能接受唯一的dom对象');
            return false;
        }
        var uuid = targetObj.attr('haoamap-uuid');
        if (uuid && HaoAmap['amapList'][uuid])
        {
            var map = HaoAmap['amapList'][uuid];
            map.destroy();
            delete HaoAmap['amapList'][uuid];
            return true;
        }
        return false;
    }
    ,initAmap: function (inputObj)
    {
        var isDisabled = inputObj.is(':disabled');
        var isMarkerInit = false;
        var dndObj = $('<div class="div_which_amap_init" style="width: 100%;height: 100%;position: relative;"></div>')
                        .insertAfter(inputObj)
                        ;
        var container = $('<div  class="haoamap_container" style="position: absolute;top: 0;left: 0;right: 0;bottom: 0;width: 100%;height: 100%;"></div>')
                        .attr('id','haocontainer_'+(inputObj.attr('haoamap-uuid'))).appendTo(dndObj);
        var panel     = $('<divclass="haoamap_panel" style="position: absolute;background-color: white;max-height: 90%;overflow-y: auto;top: 10px;right: 10px;max-width: 30%;"></div>')
                         .attr('id','haopanel_'+(inputObj.attr('haoamap-uuid'))).appendTo(dndObj);


        var placeSearch = new AMap.PlaceSearch({ //构造地点查询类
            pageSize: 5,
            pageIndex: 1,
            // city: "010", //城市
            // map: map,
            panel: panel.attr('id')
        });

        AMap.event.addListener(placeSearch, "complete", function(result){
            setTimeout(function(){
                panel.find('.poibox').bind({click:function(){
                            var location = result.poiList.pois[$(this).index()].location;
                            map.setZoomAndCenter(16,location);
                        }});
            },300);
        });

        var map = new AMap.Map(container.attr('id'), {
            resizeEnable: true
        });


        map.on('complete', function(e) {

        });

        map.on('mapmove', function(e) {
            if (!isMarkerInit || !isDisabled)
            {
                infoWindow.open(map, map.getCenter());
                marker.setPosition(map.getCenter());
            }
        });

        map.on('moveend', function(e) {
            if (!isMarkerInit || !isDisabled)
            {
                var location = map.getCenter();
                console.log("地图移动完毕！当前地图中心点为：" + location);
                marker.setPosition(location);
                infoWindow.open(map, location);
                isMarkerInit = true;
                if (!isDisabled)
                {
                    inputObj.attr({'lng':location.getLng(),'lat':location.getLat()});
                    inputObj.siblings('input[name=lng]').val(location.getLng());
                    inputObj.siblings('input[name=lat]').val(location.getLat());
                }
            }
        });

        // map.on('click', function(e) {
        //     marker.setPosition(e.lnglat);
        // });

        var marker = new AMap.Marker({ //添加自定义点标记
                map: map,
                position: map.getCenter(), //基点位置
                draggable: true,  //是否可拖动
                cursor: 'move',
                raiseOnDrag: true
            });

        marker.on('dragend',function(e){
            map.setCenter(e.lnglat);
        });

        var infoWindow = new AMap.InfoWindow({
                autoMove: true,
                offset: {x: 0, y: -30}
            });
        var infoObj = $('<div></div>');
        var addressObj = $('<input type="text" class="amap_address form-control" value="" '+(isDisabled?'disabled':'')+'/>').appendTo(infoObj);
        addressObj.keyup(function(e){
                var stroke, _ref;
                stroke = (_ref = e.which) != null ? _ref : e.keyCode;
                switch (stroke) {
                    case 8:
                    case 13:
                    case 27:
                    case 9:
                    case 38:
                    case 40:
                    case 16:
                    case 91:
                    case 17:
                    case 18:
                        return true;
                        break;
                }
                inputObj.val($(this).val());
                var searchText = $(this).val();
                if (searchText!='')
                {
                    var _this = this;
                    setTimeout(function(){
                        if (searchText==$(_this).val())
                        {
                            var isFirstResult = true;
                            placeSearch.setPageIndex(1);
                            placeSearch.search(searchText,function (status,result){
                                // if (isFirstResult && status=='complete' && result.poiList.pois.length>0)
                                // {
                                //     map.setZoomAndCenter(16,result.poiList.pois[0].location);
                                //     isFirstResult = false;
                                // }
                            });
                        }
                    },300);
                }
            });
        // var searchObj = $('<a href="javascript:;">查找周边</a>').appendTo(infoObj);
        // searchObj.click(function(){
        //     placeSearch.searchNearBy('',map.getCenter(),200);
        // });
        infoWindow.setContent(infoObj[0]);
        infoWindow.open(map, marker.getPosition());
        addressObj.val(inputObj.val());
        inputObj.change(function(){addressObj.val($(this).val()).trigger('keyup');});

        if (inputObj.attr('lng'))
        {
            map.setZoomAndCenter(16,[inputObj.attr('lng'),inputObj.attr('lat')]);
        }
        else if (inputObj.siblings('input[name=lng]').val())
        {
            map.setZoomAndCenter(16,[inputObj.siblings('input[name=lng]').val(),inputObj.siblings('input[name=lat]').val()]);
        }
        else
        {
            addressObj.trigger('keyup');
        }

        return map;
    }

}

$(function(){
    $('.input_which_is_address').each(function(){
        HaoAmap.init(this);
    });

});
