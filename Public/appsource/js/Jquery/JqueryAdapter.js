//g函数返回的必须是可以var = g(id); var.style.top引用属性的对象才行,所以不能用jq自己的$函数
function g(id)
{
	return document.getElementById(id);
}

//F函数将表单序列化作为AJAX的数据提交
function F(formId)
{
	return $('#'+formId).serialize();
}

//在特定的元素上绑定事件
function addEvent(elementId,event,fn)
{
	$('#'+elementId).bind(event,fn);
}

//在window对象上绑定事件
function windowAddEvent(event,fn)
{
	window.bind(event,fn);
}

//target元素淡入
function fadeIn(target)
{
	$('#'+target).fadeTo("normal",1);
}

//target元素淡出,注意一下要淡出完成了再把它变为不可见才好看,不然没有淡出效果了
function fadeOut(target)
{
	$('#'+target).fadeOut(1000);
	window.setTimeout(function(){$('#'+target).hide()},1000);
}

//取得页面上方的坐标
function getPageScroll(){
	var yScroll;
	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (parent.document.documentElement && parent.document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = parent.document.documentElement.scrollTop;
	} else if (parent.document.body) {// all other Explorers
		yScroll = parent.document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}