if (typeof jQuery === 'undefined') {
    throw new Error('jQuery no found. ');
}

/** 支持取得元素的id，如果没有id，就创造一个id。 */
(function($, undefined) {
	$.fn.extend({
	attrId: function() {//axing add
		var id = $(this).attr('id');
		if (!id)
		{
			id = 'rand_'+Math.random().toString(36).substr(2)
			$(this).attr('id',id);
		}
		return id;
	}
	});
})(jQuery);

/** 在当前节点附近寻找对象，第一次找到对象后停止。 */
(function($, undefined) {
	$.fn.extend({
	around: function(selector) {
		var $parent = $(this);
		while($parent.length>0 && $parent.find(selector).length==0)
		{
			$parent = $parent.parent();
		}
		return $parent.find(selector);
	}
	});
})(jQuery);

/** 增强Jquery.attr方法 */
(function(old) {
  $.fn.attr = function() {
    if(arguments.length === 0) {
      if(this.length === 0) {
        return null;
      }

      var obj = {};
      $.each(this[0].attributes, function() {
        if(this.specified) {
          obj[this.name] = this.value;
        }
      });
      return obj;
    }

    return old.apply(this, arguments);
  };
})($.fn.attr);

/** 重新注册对应的组件行为（比如一些特殊的下拉框、地图组件，上传图片的组件之类的。 */
function haoPageInit(target)
{
	console.log('haoPageInit',target);
	if (!target){target='body';}
	var $target = $(target);

    //更新标题
    if ($target.children().eq(0).attr('page_title'))
    {
        var pageObj = $target.children().eq(0);
        $('title').html(pageObj.attr('page_title'));
        $('meta[name=keywords]').attr('content',pageObj.attr('page_keywords'));
        $('meta[name=description]').attr('content',pageObj.attr('page_description'));
        $('link[rel=apple-touch-icon]').attr('href',pageObj.attr('page_icon'));
        //此处hack苹果微信中的bug  :  http://www.jianshu.com/p/217b0e3bd337
        if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent))
        {
            $('<iframe src="/favicon.ico" width=1 height=1 border=0 style="display:none;"></iframe>').on('load',function() {
                var that = this;
                  setTimeout(function() {
                      $(that).off('load').remove();
                  }, 0);
                }).appendTo($('body'));
        }
    }


	// setTimeout(function(){
		// $('title').html($('#main_content .breadcrumb li.active a').html());
	// },1000);
	// document.getElementsByTagName('title')[0].innerHTML = $('#main_content .breadcrumb li.active a').html();

	//三级地区联动
	$target.find('select.select_chosen[name=area_third]').bind('chosen:showing_dropdown',function(e,cObj){
		$(':focus').blur();
		if ($(this).around('select.select_chosen[name=area_second]').val()=='')
		{
			$(this).attr('disabled',true).trigger('chosen:updated');
		}
	}).trigger('chosen:showing_dropdown');
	$target.find('select.select_chosen[name=area_second]').change(function(){
		var $nextObj = $(this).around('select.select_chosen[name=area_third]');
		$nextObj.find('[value=""]').attr('selected', 'selected').siblings().remove()
		$nextObj.removeAttr('disabled').trigger('change').trigger('chosen:updated');
		setTimeout(function(){
			$nextObj.trigger('chosen:open');
		},100);
	}).bind('chosen:showing_dropdown',function(e,cObj){
		$(':focus').blur();
		if ($(this).around('select.select_chosen[name=area_main]').val()=='')
		{
			$(this).attr('disabled',true).trigger('chosen:updated');
		}
	}).trigger('chosen:showing_dropdown');
	$target.find('select.select_chosen[name=area_main]').change(function(){
		var $nextObj = $(this).around('select.select_chosen[name=area_second]');
		$nextObj.find('[value=""]').attr('selected', 'selected').siblings().remove()
		$nextObj.removeAttr('disabled').trigger('change').trigger('chosen:updated');
		setTimeout(function(){
			$nextObj.trigger('chosen:open');
		},100);
	}).bind('chosen:showing_dropdown',function(e,cObj){
		$(':focus').blur();
	});

    // 动态展示侧边栏
    if ($('#main_content').children().eq(0).attr('isHiddenSideBar') == 'true')// && (!window.history || window.history.length>1)
    {
        $('#side_content').hide();
        // $('body').css('padding-bottom','0px');
        // if ($target.find('.side_button').length>0)
        // {
        //     $('body').css('padding-bottom',$target.find('.side_button').height()+'px');
        // }
    }
    else
    {
        $('#side_content').show();
        // $('body').css('padding-bottom','150px');
    }


	//使用HaoAdmin.detail()方法来处理/edit/标签
	$target.find('a[href^="/edit/"][ispjax!="false"]').click(function(e){
		HaoAdmin.detail(this);
		return false;
	});

	//空数据提示
	if($('#main_content tbody').children().length == 0)
	{
		$('#main_content tbody').html('<tr class="haoadmin_noresults"><td colspan='+($('#main_content thead th').length)+' align=center>未查询到任何结果哦。</td></tr>');
	}

	//required表单标识增强
	$target.find('form').attr({'novalidate':'novalidate'});
	// $target.find('input[required]').closest('div').attr({'required':'required'});
	$target.find('div[required]').find('select,input,textarea').attr({'required':'required'});
	if ($target.find('form').hasClass('form-horizontal'))
	{
		$target.find('[required]').each(function(){
			if ($(this).siblings('.form-control-required').length==0)
			{
				$(this).closest('select,input,textarea').after('<span class="form-control-required">*</span>');
			}
		});
	}
	else
	{
		$target.find('[required]').each(function(){
			if ($(this).closest('.form-group').find('.form-control-required').length==0)
			{
				$(this).closest('.form-group').append('<span class="form-control-required">*</span>');
			}
		});
	}
	// 支持ajax的选择组件
	$target
		.find('select[search-type]').each(function(){
			var that = this;
			$LAB
				.script('/third/chosen/chosen.jquery.js')
				.wait(function(){
					$(that)
						.bind({
							'chosen:ready':chosen_ready
							,'chosen:winnow_results':chosen_winnow_results
							})
                        .chosen( $.extend({},$(that).attr(),{allow_single_deselect: true,search_contains:true}) );
				});
		})
	// 七牛上传
    $target.find('input.input_which_upload_to_qiniu').each(function(){
    	var that = this;
		$LAB
			.script('/third/haouploader/js/webuploader/webuploader.min.js')
			.script('/third/haouploader/js/webuploader-haoplus.js')
			.script('/third/haouploader/js/sortable/Sortable.js')
            .script('/third/haouploader/js/lrz/lrz.bundle.js')
			.script('/third/haouploader/js/haouploader.js')
			.wait(function(){
			        HaoUploader.init(that);
			    });
		});
    // 时间组件
    $target.find('input.datetimepicker').each(function(){
    	var that = this;
		$LAB
			.script('/third/datetimepicker/jquery.datetimepicker.full.js')
			.wait(function(){
					$.datetimepicker.setLocale('zh');
				    $(that).datetimepicker({
						 // timepicker:false,
						 format:'Y-m-d H:i:s',
						 step:10,
						 dayOfWeekStart:1,
						 mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
						}).blur();
			    });
		});

	// 地址转地图组件
    $target.find('input.input_which_is_address').each(function(){
    	var that = this;
		$LAB
			.script('http://webapi.amap.com/maps?v=1.3&key='+AMAP_WEBAPI_KEY+'&plugin=AMap.PlaceSearch')
			.wait(function(){
				$LAB
					.script('/third/haoamap.js')
					.wait(function(){
					        HaoAmap.init(that);
					    });
			});
		});

	// checkbox转开关
    $target.find('input[type=checkbox].bootstrap-switch').each(function(){
    	var that = this;
		$LAB
			.script('/third/bootstrap-switch/js/bootstrap-switch.min.js')
			.wait(function(){
				    $(that).bootstrapSwitch();
			    });
		});

    // 富文本编辑器
    $target.find('script[type="text/plain"]').each(function(){
    	var that = this;
		$LAB
			.script('/third/ueditor/ueditor.config.js')
			.script('/third/ueditor/ueditor.all.min.js')
			.wait(function(){
				    var ue = UE.getEditor($(that).attrId(),{
									    	elementPathEnabled:false//是否启用元素路径，默认是显示
									    	,toolbars: [[
									            'emotion','bold', 'italic', 'underline','strikethrough','forecolor', 'backcolor','|', 'insertorderedlist', 'insertunorderedlist','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|','|','insertimage','|','undo', 'redo','|','source'
									        	]]
									    });
			    });
		});


    var ajaxCaptcha = null;
    // 图片验证码
    $target.find('input[name=captcha_key]').each(function(){
    	var img = $(this).siblings('img');
    	if (img.length==0)
    	{
    		img = $('<img/>').insertAfter(this);
	    	img.click(function(){
	    		var that = this;
	    		if (ajaxCaptcha){HaoConnect.abort(ajaxCaptcha);}
	    		ajaxCaptcha = HaoConnect.request('axapi/get_captcha').done(function(hResult){
		    			$(that).attr('src',hResult.find('url'));
		    			$(that).siblings('[name=captcha_key]').val(hResult.find('captchaKey'));
			    		var random = Math.random();
			    		$(that).attr('time-random',random);
			    		setTimeout(function(){
			    			if ($(that).attr('time-random')==random)
			    			{
				    			$(that).trigger('click');
			    			}
			    		},60000);
		    		});
	    	}).trigger('click');
    	}
    });

}

/*注销相关事件*/
function haoPageOutIt(target)
{
	console.log('haoPageOutIt',target);
	var $target = $(target);
// 支持ajax的选择组件
	$target.find('select[search-type]').each(function(){$(this).chosen("destroy");});
// 七牛上传
    $target.find('.input_which_upload_to_qiniu').each(function(){ HaoUploader.destroy(this);});
// 时间组件
    $target.find('input.datetimepicker').each(function(){$(this).datetimepicker('destroy');});
// 地址转地图组件
    $target.find('.input_which_is_address').each(function(){HaoAmap.destroy(this);});
// checkbox转开关
// 富文本编辑器
    $target.find('script[type="text/plain"]').each(function(){UE.getEditor($(this).attrId()).destroy();});
// 图片验证码
    $target.find('[name=captcha_key]').siblings('img').unbind().remove();
}

/*chosen组件ready事件自定义操作*/
function chosen_ready(e,cObj)
{
	var chosen = cObj.chosen;
	// console.log(chosen);
	var chosenType = chosen.form_field_jq.attr('search-type');
	chosen.form_field_jq.attr('data-target',$(chosen.container).attrId());
	// chosen.form_field_jq.
	if (chosenType == 'ajax')
	{
		var ajaxUrl = chosen.form_field_jq.attr('search-ajax-url');
		if (ajaxUrl.match(/(\{)(.*?)(\})/g))
		{
			ajaxUrl = ajaxUrl.replace(/(\{)(.*?)(\})/g,function($0,$1,selector,$3){
				return chosen.form_field_jq.closest('form').find(selector).val();
			});
		}
		var searchNamePath  = chosen.form_field_jq.attr('search-name-path');
		var searchValuePath = chosen.form_field_jq.attr('search-value-path');
		var searchValueKey = searchValuePath.replace(/^.*\>/g,'');
		chosen.form_field_jq.find('option').each(function(){
			var optionObj = $(this);
			if (optionObj.text() == optionObj.val() )
			{
				var xhr = optionObj.data('search-xhr');
				if (xhr) { xhr.abort();}
				xhr = $.getJSON(ajaxUrl+'&search_paths[]='+encodeURIComponent(searchNamePath)+'&search_paths[]='+encodeURIComponent(searchValuePath)+'&'+searchValueKey+'='+optionObj.val(),function(result){
					var hResult         = new HaoResult(result);
					var names           = hResult.search(searchNamePath);
					var values          = hResult.search(searchValuePath);
					if (names.length == values.length)
					{
						var isNewFound = false;
						for (var i in names)
						{
                            if (optionObj.val() == values[i] || optionObj.html()==names[i] )
							{
                                optionObj.html(names[i]);
                                optionObj.val(values[i]);
								chosen.form_field_jq.trigger('chosen:updated');
								break;
							}
						}
					}
				});
				optionObj.data('search-xhr',xhr)
			}
		});
	}
}

/*chosen组件显示结果事件自定义操作*/
function chosen_winnow_results(e,cObj)
{
	var chosen = cObj.chosen;
	var chosenType = chosen.form_field_jq.attr('search-type');
	if (chosenType == 'tags' || chosenType == 'ajax')
	{
		var searchText = chosen.search_field.val();
		var searchWord = encodeURI(searchText);
		if (chosenType == 'ajax')
		{
			var ajaxUrl = chosen.form_field_jq.attr('search-ajax-url');
			if (ajaxUrl.match(/(\{)(.*?)(\})/g))
			{

				ajaxUrl = ajaxUrl.replace(/(\{)(.*?)(\})/g,function($0,$1,selector,$3){
                    var value = chosen.form_field_jq.around(selector).val();
                    return value;
				});
			}
			if (ajaxUrl.substr(-1,1)=='=')
			{
				ajaxUrl = ajaxUrl+encodeURI(searchText);
			}
			searchWord = encodeURI(ajaxUrl);
			if (searchText=='')
			{
				if (chosen.form_field_jq.find("option:selected").text() == chosen.form_field_jq.find("option:selected").val() )
				{
					var searchValuePath = chosen.form_field_jq.attr('search-value-path');
					var searchValueKey = searchValuePath.replace(/^.*\>/g,'');
				}
			}
		}
		if (chosen.form_field_jq.attr('search-word') != searchWord)
		{
			chosen.form_field_jq.attr('search-word',searchWord);
			chosen.form_field_jq.find('[from-search="true"]').not(':selected').remove();
			if (searchText && chosenType == 'tags')
			{
				var value = searchText;
				var name  = searchText;
				if (chosen.form_field_jq.find('[value='+value+']').length == 0)
				{
					chosen.form_field_jq.append('<option from-search="true" value="'+value+'">'+name+'</option>');
					console.log(value,name);
					chosen.form_field_jq.trigger('chosen:updated');
				}
			}
			else if (chosenType == 'ajax')
			{
				var xhr = chosen.form_field_jq.data('search-xhr');
				if (xhr) { xhr.abort();}
				chosen.search_results.find('.no-results').html('searching...');
				var searchNamePath  = chosen.form_field_jq.attr('search-name-path');
				var searchValuePath = chosen.form_field_jq.attr('search-value-path');

				xhr = $.getJSON(ajaxUrl+'&search_paths[]='+encodeURIComponent(searchNamePath)+'&search_paths[]='+encodeURIComponent(searchValuePath),function(result){
					var hResult         = new HaoResult(result);
					var names           = hResult.search(searchNamePath);
					var values          = hResult.search(searchValuePath);
					if (names.length > 0 && names.length == values.length)
					{
						var isNewFound = false;
						for (var i in names)
						{
							var value = values[i];
							var name = names[i];
							var optionObj = chosen.form_field_jq.find('[value='+value+']');
							if (optionObj.length == 0)
							{
								if (name == null){name = ""+value;}
								if (name.indexOf(searchText)<0)
								{
									name = name + ' ('+searchText+')';
								}
								isNewFound = true;
								chosen.form_field_jq.append('<option from-search="true" value="'+value+'">'+name+'</option>');
							}
							else if (optionObj.html() == optionObj.val())
							{
								isNewFound = true;
								optionObj.html(name);
							}
						}
						if (isNewFound)
						{
							chosen.form_field_jq.trigger('chosen:updated');
						}
						else
						{
							chosen.search_results.find('.no-results').html('nothing found...');
						}
					}
				});
				chosen.form_field_jq.data('search-xhr',xhr)
			}
		}
	}
}


function selectAllInputThisRow(_this)
{
	var isChecked = $(_this).prop('checked');
	var _index = $(_this).closest('th,td').index();
	$(_this).closest('table').find('tr').find('td:eq('+_index+')').find('input[type=checkbox]').prop('checked',isChecked);
}


/** ----------------------------------------页面载入完成后，进行初始化与响应处理------------------------------------------------------------------ */

/** 自动载入一张背景图 */
$(function(){
	var imgs = [];
	for (var i = -1; i < 20 ; i++) {
		imgs.push('/background.jpg?idx='+i);
	}
	$("body").ezBgResize({
		img : imgs
		,'delay':60000
	});
});


/** 使用pjax，新页面加载后，别忘了调用haoPageInit */
$(function(){
	$('#side_content .list-group-item').click(function(){
		$('#side_content .list-group-item-warning').removeClass('list-group-item-warning');
		$(this).addClass('list-group-item-warning').blur();
	});
	$.pjax.defaults.timeout = 30000;
    $(document).pjax('a[href^="/"][ispjax!="false"],a[href^="?"][ispjax!="false"]','#main_content');
	$(document).on('submit', 'form[data-pjax]', function(event) {
		event.preventDefault(); // stop default submit behavior
		$.pjax.submit(event, '#main_content');
		return false;
	});
	$(document).on('pjax:click , submit , pjax:beforePopstate', function() {
		haoPageOutIt('#main_content');
	});
    $(document).on('pjax:end', function(event,xhr, textStatus, options) {
        setTimeout(function(){
            haoPageInit('#main_content');
        },100);
    });

	$(document).on('pjax:error', function(xhr, textStatus, error, options) {
	  	console.log(xhr, textStatus, error, options);
	  	if (textStatus['responseText'])
	  	{
	    	$.alert({
	    		title: false
	    		,backgroundDismiss:false
	    		,content:textStatus['responseText']
	    	});
	  	}
	  	return false;
	});
});


/** 设定NProgress相关参数 */
$(function(){
	$(document).on('pjax:start', function(e,xhr, options) {
									$('#side_content .list-group-item-warning').removeClass('list-group-item-warning');
									var requestUrl = options.requestUrl?options.requestUrl:options.url;
									requestUrl = requestUrl.replace(/^http:\/\/.*?(\/.*?)(\/*[\?#].*$|[\?#].*$|\/*$|\.\.+)/g,'$1');
                                    var sideAObj = $('#side_content [href^="'+requestUrl+'"]');
									if (sideAObj.closest('.list-group').css('display')=='none')
									{
										sideAObj.closest('.list-group').show();
									}
									if (sideAObj.length>0)
									{
										sideAObj.addClass('list-group-item-warning').blur();
										NProgress.configure({ parent:'#'+sideAObj.attrId() , direction:(options['direction'] && options['direction']=='back')?'leftToRightReduced':'leftToRightIncreased' });
									}
									else
									{
                                        NProgress.configure({ parent:'body' , direction:(options['direction'] && options['direction']=='back')?'leftToRightReduced':'leftToRightIncreased' });
									}
									NProgress.start();
								});
	$(document).on('pjax:end',   function(e,xhr, options) {
									NProgress.done();
									if ($('body').width()==414)
									{
										$('#side_content .list-group').hide();
									}
								});
});

/** pjax请求的时候，关闭所有弹出窗口 */
$(function(){
    $(document).on('pjax:start', function(e,xhr, options) {
                                    HaoAdmin.closeAllJc();
                                });
});
/** 页面载入后，初始化对应组件。（该方法在ajax后也应该调用哦） */
$(function(){

	$.testRemind.css = {
        padding: "8px 10px",
        borderColor: "#aaa",
        borderRadius: 8,
        boxShadow: "2px 2px 4px rgba(0,0,0,.2)",
        background: "#fff url(/css/chrome-remind.png) no-repeat 10px 12px",
        backgroundSize: "16px",
        backgroundColor: "#fff",
        fontSize: 16,
        zIndex: 10000,
        textIndent: 20
    };

    $.extend(jconfirm.pluginDefaults,{
        confirmButton: '确定',
        cancelButton: '取消',
    });

	haoPageInit('body');

	$('#side_content .panel-heading').bind('click touchstart touchmove',function(e){
		console.log(e);
		if ($('body').width()==414)
		{
			$('#side_content .list-group').hide();
			$(this).siblings('.list-group').show();
		}
		else
		{
			$('#side_content .list-group').show();
		}
	});

	$(window).unload(function(e){
        e.preventDefault();
        console.log('嘿嘿嘿');
    });
});
