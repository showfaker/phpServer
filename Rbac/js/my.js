function validateCallback(form)
{
	var $form=$(form);
	if(!$form.valid())
	{
		return false;
	}
	var _submitFn=function(){
		$.ajax({
			type:form.method||'POST',
			url:$form.attr("action"),
			data:$form.serializeArray(),
			dataType:"json",
			cache:false,
			success:function(data)
			{
				console.log(data);
				if(data.status==1)
				{
					parent.toastr_success(data.message);
					location.href=location.href;
				}
				else
				{
					parent.toastr_error(data.message);
				}
			},
			error:function(data)
			{
			
			}
		});
	}
	_submitFn();
	return false;
}
function toastr_success(message)
{
	toastr.options={
	closeButton: true,//显示关闭按钮
	debug: false,
	progressBar: true,//显示进度条
	positionClass: "toast-top-center",//位置
	onclick: null,//点击消息框自定义事件
	showDuration: "300",//显示动作时间
	hideDuration: "1000",//隐藏动作时间
	timeOut: "2000",//显示时间,0为永久显示
	extendedTimeOut: "1000",//鼠标移动过后显示显示时间
	showEasing: "swing",
	hideEasing: "linear",
	showMethod: "fadeIn",//显示方式
	hideMethod: "fadeOut"//隐藏方式
	};
	toastr.success(message);
}
function toastr_error(message)
{
	toastr.options={
	closeButton: true,//显示关闭按钮
	debug: false,
	progressBar: true,//显示进度条
	positionClass: "toast-top-center",//位置
	onclick: null,//点击消息框自定义事件
	showDuration: "300",//显示动作时间
	hideDuration: "1000",//隐藏动作时间
	timeOut: "2000",//显示时间,0为永久显示
	extendedTimeOut: "1000",//鼠标移动过后显示显示时间
	showEasing: "swing",
	hideEasing: "linear",
	showMethod: "fadeIn",//显示方式
	hideMethod: "fadeOut"//隐藏方式
	};
	toastr.error(message);
}