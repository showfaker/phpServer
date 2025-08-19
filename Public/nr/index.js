(function($){
	jQuery(document).ready(function(){



		initIndex();
		initListData();
		initView();

		//jQuery(window).resize(sizeFix).resize();
	});

	var mesTmpl = '<p class="mes-box"><b class="${className}">${mesContext}</b></p>',
	viewMesTimer;
	
	function initView(){
		//常规视图
		$('#to_nor_view').live('click', function(){
		
			$('.view-list > li').removeClass('config');
			
			$('.view-list').sortable('disable');
			
			$('.view-list > li').each(function(){
			
				var $this = $(this),
				
				text = $this.find('span').html();
				
				$this.attr('title', text);
		
			});
			
			this.id = 'to_adv_view';
			
			this.innerHTML = '配置';
		});
		//配置视图
		$('#to_adv_view').live('click', function(){
		
			$('.view-list > li').addClass('config');
			
			var $viewList = $('.view-list');
			
			if($viewList.data('sortable')){
			
				$viewList.sortable('enable');
				
			} else {
				
				$viewList.sortable({
					distance: 50,
					items: 'li.config',
					revert: true  ,
					cancel: 'i',
					tolerance: 'pointer' ,
					start: function(e,o){
						
					},
					stop: function(e,o){
						var ids = serializeViewId();
						var url = '/general/crm/apps/crm/include/view_order.php';
						$.post(url,{ ids: ids ,name : indexInfo.ENTITY },function(msg){		
							viewMes(msg);
						});				
					}				
				});
			}
			
			$('.view-list > li').attr('title', '拖拽排序');
			
			$('.view-list > li > i').show();
			
			this.id = 'to_nor_view';
			
			this.innerHTML = '返回';
		});
		
		$('.view-list > li[class!=config]').live('click',function(e){
		
			if( !$(this).hasClass('config') ){
		
				$('.view-list > li').removeClass('active');
				
				$(this).addClass('active');
				var v_id = $(this).attr('v_id').toString();
				changeView( v_id );
			}
		
		});

		$('.view-list > li > i').live('click',function(){
		
			var $li = $(this.parentNode),
			id = $li.attr('v_id'),
			cb = creatCallback(reflushView);
			switch(this.className){			
				case 'sys':
					openNewProtal('/general/crm/studio/modules/ViewDefine/?entity_name='+indexInfo.ENTITY+'&view_id='+id+'&callback='+cb+'&back=close', '视图编辑', 'viewEdit_'+id);
				break;
				
				case 'del':
				if(confirm('确认删除视图?')){
					id && $.post('/general/crm/studio/modules/ViewDefine/delete.php',{ view_id: id },function(msg){		
						if(msg == 'success'){
							$li.remove();
						}
						viewMes(msg);
					});				
				}
				break;
			}
		
		});
		
		$('#creat-view').live('click',function(){
			var openername=window.name;
			var cb = creatCallback(reflushView);
			openNewProtal('/general/crm/studio/modules/ViewDefine/?entity_name='+indexInfo.ENTITY+'&view_old_id='+indexInfo.USER_VIEW+'&callback='+cb+'&openername='+openername+'&back=close', '新建视图', 'creatView');
		
		});
	}
	
	function serializeViewId(){
	
		var result = [];
		
		$('.view-list > li').each(function(){
		
			var id = $(this).attr('v_id');
		
			id && result.push(id);
		
		});
	
		return result.join(',');
	}

	function viewMes(msg){
		if(viewMesTimer){
			clearTimeout(viewMesTimer);
			$('#viewMessage').remove();
		}						
		var message = msg === 'success' ? '更新成功.' : '更新失败.',							
		className 	= msg === 'success' ? 'success' : 'error',
		mesbox = $.tmpl(mesTmpl, { className: className, mesContext: message})
					.insertAfter('.view-list')
					.attr('id', 'viewMessage');

		viewMesTimer = setTimeout(function(){
			mesbox.remove();
		}, 4000);
	}
	function reflushView(){
		
	}
})(jQuery);

function initIndex(){
	var sel_ipt_str = "<input type=\"hidden\" name=\"selectIds\" id=\"selectIds\" value=\"\"/>";
	sel_ipt_str +="<input type=\"hidden\" name=\"MselectIds\" id=\"MselectIds\" value=\"\">";
	jQuery(document.body).append(sel_ipt_str);
	
	var line_height = 25;
	if(jQuery.fn.toSuperTable){
		jQuery("#datalist").toSuperTable({ 
	//		width: jQuery(window).width()-30, 
			height:line_height*12+13, 
			margin: 0,
			fixedCols: 0 
		}).parent().css('width','auto');
		jQuery("#datalist_recycle").toSuperTable({ 
	//		width: jQuery(window).width()-30, 
			height:line_height*12+13, 
			margin: 0,
			fixedCols: 0 
		});
	}
	
	jQuery("#selAll").bind("click", function(){
		var check = jQuery(this).attr("checked") == undefined ? false : jQuery(this).attr("checked");
		jQuery("input[name='selRecord']").attr("checked", check);
	});
	
	var sel_all_len = jQuery("input[name='selRecord']").length;
	jQuery("input[name='selRecord']").bind("click", function(){
		var sel_len = jQuery("input[name='selRecord'][checked]").length;
		if(sel_len == sel_all_len){
			jQuery("#selAll").attr("checked", true);
		}else{
			jQuery("#selAll").attr("checked", false);
		}
	});
	
	jQuery("#datalist_box").css("margin-top", "0");
	
		jQuery( "#button" ).click(function() {
		runEffect(jQuery(this), "slide", "effect");
		return false;
	});
	
	jQuery("[datetype=\'mystyle\']").live("mouseover",function() {
		runEffect(jQuery(this), "fade", "Actioneffect");
		jQuery(".x").live("mouseover",function(){ 
			runHiddenEffect("fade", "Actioneffect");
			return false;
		})
		jQuery("#Actioneffect").live("mouseleave",function(){ 
			runHiddenEffect("fade", "Actioneffect");
			return false;
		})
		return false;
	});

	jQuery( "#effect" ).hide();
	jQuery( "#Actioneffect" ).hide();

	jQuery("#datalist_box").css("margin-top", "0");
	jQuery( "#effect" ).after(jQuery("#Actioneffect"));

	jQuery(".attach_div").not('#recent_menu,#view_act_menu').css({"margin-left":"20","margin-top":"-25"});//修正附件功能菜单的定位
	
	jQuery('input.InputField').live('focus', function() {
 		jQuery(this).addClass('InputTextFocus'); 																																//添加focus效果
	}).live('blur',function(){
		jQuery(this).removeClass('InputTextFocus');		
	});																
	
	jQuery.fn.draggable && jQuery('table.CRM_Dialog').draggable({ addClasses: false,iframeFix: true,handle:'.head' });
	
}

function initListData(){
	var dataList = jQuery('#datalist').length ? jQuery('#datalist') : jQuery('#datalist_recycle');
	var h = dataList.height() +18;
	jQuery('div.sData').height(h);
	jQuery('#datalist_box').add('#datalist_recycle_box').height(h);
	dataList.css("table-layout", "fixed");
	var obj = new Object;
	obj.mouseDownX = "";
	obj.pareneTdW = "";
	obj.pareneTableW = "";
	obj.eventBinded = false;
	jQuery("span[class='resizeDivClass']").css("cursor", "e-resize").bind("mousedown", resizeStart);
	jQuery(window).add("span[class='resizeDivClass']").bind("mouseup",resizeEnd );
	typeof initListDataCallback == 'function' && initListDataCallback();
}

function resizeStart(e){

		if(this.setCapture){
			this.setCapture();
		} else if(window.captureEvents){  
   			 window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP|Event.MOUSEDOWN);
		}  

		jQuery(document.body).attr("unselectable", "on").bind("selectstart", function(){
			return false;
		}).css({"-moz-user-select": "none",cursor: 'e-resize'});
		jQuery(this).parent('tr').css('cursor', 'e-resize');
		var pageX = e.originalEvent.x || e.originalEvent.layerX || 0; 
		var pageY = e.originalEvent.y || e.originalEvent.layerY || 0;
		obj.mouseDownX = pageX;
		obj.pareneTdW = jQuery(this).parent().width();
		obj.pareneTableW = jQuery("#clone_table").width();		
		obj.handle = this;
		!obj.eventBinded && jQuery(this).parents('tr:first').add("span[class='resizeDivClass']").bind("mousemove",resizing );
		obj.eventBinded = true;
}

function resizing(e){
		var pageX = e.originalEvent.x || e.originalEvent.layerX || 0; 
		var pageY = e.originalEvent.y || e.originalEvent.layerY || 0;
		
		if(!obj.mouseDownX){
			return false;
		}
		var newWidth = obj.pareneTdW + pageX - obj.mouseDownX;
		
		if(newWidth>60){
			jQuery(obj.handle).parent().css("width", newWidth);
			jQuery("#tableTr > td:eq("+jQuery(obj.handle).parent().index()+")").width( newWidth );
		}
}

function resizeEnd(e){
		if(this.releaseCapture) {
			this.releaseCapture();
		} else if(window.releaseEvents) {
			window.releaseEvents(Event.MOUSEMOVE|Event.MOUSEUP|Event.MOUSEDOWN);
		}

		obj.eventBinded && jQuery(this).parents('tr:first').add("span[class='resizeDivClass']").unbind("mousemove");
		obj.eventBinded = false;
		obj.mouseDownX=0;

		jQuery(document.body).attr("unselectable", "").unbind("selectstart").css({"-moz-user-select": "",cursor: "auto"});
		jQuery(this).parent('tr').css('cursor', 'auto');
}

function runEffect(obj, eve, show_div) {
	var pos = jQuery(obj).offset();
	// get effect type from 
	var selectedEffect = eve;//jQuery( "#effectTypes" ).val();
	
	var left = pos.left + jQuery(obj).width() + 5;
	var top = pos.top;// + jQuery(obj).height();
	
	if(show_div == "Actioneffect" ){
		if( (top + jQuery( "#"+show_div ).height()) > jQuery(document.body).height() ){
			left = pos.left;
			top = top - jQuery( "#"+show_div ).height() - jQuery(obj).height();
		}
	}
	jQuery( "#"+show_div ).css("left", left);
	jQuery( "#"+show_div ).css("top", top);

	// most effect types need no options passed by default
	var options = {};
	// some effects have required parameters
	if ( selectedEffect === "scale" ) {
		options = { percent: 100 };
	} else if ( selectedEffect === "size" ) {
		options = { to: { width: 280, height: 185 } };
	}

	// run the effect
	jQuery( "#"+show_div ).show( selectedEffect, options, 500 );
};

function runHiddenEffect(eve, show_div) {
	// get effect type from 
	var selectedEffect = eve;//jQuery( "#effectTypes" ).val();

	// most effect types need no options passed by default
	var options = {};
	// some effects have required parameters
	if ( selectedEffect === "scale" ) {
		options = { percent: 0 };
	} else if ( selectedEffect === "size" ) {
		options = { to: { width: 200, height: 60 } };
	}

	// run the effect
	jQuery( "#"+show_div ).hide( selectedEffect, options, 500);
	
};
