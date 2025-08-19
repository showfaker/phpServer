function showTip(info)
{
	$('tips').innerHTML	=	info;
}

function sendForm(formId,action,response,target,effect)
{	
	if (CheckForm($(formId),'ThinkAjaxResult'))
	{
		ThinkAjax.sendForm(formId,action,response);
	}
	//Form.reset(formId);
}
rowIndex = 0;

function prepareIE(height, overflow){
	bod = document.getElementsByTagName('body')[0];
	bod.style.height = height;
	//bod.style.overflow = overflow;
	htm = document.getElementsByTagName('html')[0];
	htm.style.height = height;
	//htm.style.overflow = overflow; 
}

function hideSelects(visibility)
{
   	selects = document.getElementsByTagName('select');
   	for(i = 0; i < selects.length; i++) 
   	{
		selects[i].style.visibility = visibility;
	}
}
document.write('<div id="overlay" class="none"></div><div id="lightbox" class="none"></div>');

function showPopWin(content,width,height)
{
	//  IE 
	prepareIE('100%', 'hidden');
	window.scrollTo(0, 0); 
	hideSelects('hidden');
	$('overlay').style.display = 'block';
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();
	$('lightbox').style.display = 'block';
	$('lightbox').style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 35 - height) / 2) + 'px');
	$('lightbox').style.left = (((arrayPageSize[0] - 25 - width) / 2) + 'px');
	$('lightbox').innerHTML	=	content;
}

function fleshVerify()
{
	var timenow = new Date().getTime();
	$('verifyImg').src= APP+'/Public/verify/'+timenow;
}

function allSelect()
{
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= true;
	}
}

function allUnSelect()
{
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= false;
	}
}

function InverSelect()
{
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= !colInputs[i].checked;
	}
}

function WriteTo(id)
{
	var type = $F('outputType');
	switch (type)
	{
		case 'EXCEL':WriteToExcel(id);break;
		case 'WORD':WriteToWord(id);break;	
	}
	return ;
}

function build(id)
{
	window.location = APP+'/Card/batch/type/'+id;
}

function shortcut()
{
	var name=window.prompt("输入该快捷方式的显示名称","");
	if (name !=null)
	{
	var url	=	location.href;
	ThinkAjax.send(location.protocol+'//'+location.hostname+APP+'/Shortcut/ajaxInsert/','ajax=1&name='+name+'&url='+url);
	}

}

function delcache()
{
	ThinkAjax.send(location.protocol+'//'+location.hostname+APP+'/Common/clearcache/','ajax=1');
	window.location.reload();
}

function show()
{
	if (document.getElementById('menu').style.display!='none')
	{
		document.getElementById('menu').style.display='none';
		document.getElementById('main').className = 'full';
	}
	else 
	{
		document.getElementById('menu').style.display='inline';
		document.getElementById('main').className = 'main';
	}
}

function CheckAll(strSection)
{
	var i;
	var	colInputs = document.getElementById(strSection).getElementsByTagName("input");
	for	(i=1; i < colInputs.length; i++)
	{
		colInputs[i].checked=colInputs[0].checked;
	}
}

function add(id)
{
	if (id)
	{
		 location.href  = URL+"/add/id/"+id;
	}
	else
	{
		 location.href  = URL+"/add/";
	}
}

function cardDataBak()
{
	location.href  = URL+"/cardDataBak/";
}

function cardDataDown()
{
	location.href  = URL+"/cardDataDown/";
}

function timeSet(id)
{
	location.href  = URL+"/index/";
}

function timeChange(id)
{
	location.href  = URL+"/timeChange/";
}

function worktimeSet(id)
{
	location.href  = URL+"/worktimeSet/";
}

function worktimeChange(id)
{
	location.href  = URL+"/worktimeChange/";
}

function cardIpSet(id)
{
	location.href  = URL+"/cardIpSet/";
}

function cardIpChange(id)
{
	location.href  = URL+"/cardIpChange/";
}

function attendanceData(id)
{
	location.href  = URL+"/index/";
}

function abnormalAttendance(id)
{
	location.href  = URL+"/abnormalAttendance/";
}

function workOverTime(id)
{
	location.href  = URL+"/workOverTime/";
}

function leaveTimeInfo(id)
{
	location.href  = URL+"/leaveTimeInfo/";
}

function businessTimeInfo(id)
{
	location.href  = URL+"/businessTimeInfo/";
}

function attendanceApply()
{
	location.href  = URL+"/index/";
}

function askLeaveApply()
{
	location.href  = URL+"/askLeaveApply/";
}

function businessTravelApply()
{
	location.href  = URL+"/businessTravelApply/";
}

function LeaveApplyHandle()
{
	location.href  = URL+"/LeaveApplyHandle/";
}

function BusinessTravelApplyHandle()
{
	location.href  = URL+"/BusinessTravelApplyHandle/";
}

function leaveApply()
{
	location.href  = URL+"/leaveApply/";
}

function myTimeApp()
{
	location.href  = URL+"/index/";
}

function myAskLeaveApply()
{
	location.href  = URL+"/myAskLeaveApply/";
}

function myBusinessTravelApply()
{
	location.href  = URL+"/myBusinessTravelApply/";
}

function newPlm()
{
	location.href  = URL+"/newPlm/";
}

function PlmHandle()
{
	location.href  = URL+"/index/";
}

function workTaskDelay()
{
	location.href  = URL+"/workTaskDelay/";
}

function myPlmApply(id)
{
	location.href  = URL+"/index/";
}

function myTaskChangeApply(id)
{
	location.href  = URL+"/myTaskChangeApply/";
}

function TimeApplyHandle()
{
	location.href  = URL+"/index/";
}

function appAgree(id)
{	
	location.href  = URL+"/appAgree/id/"+id;
}

function cardfiledown(id)
{	
	location.href  = URL+"/cardfiledown/id/"+id;
}

function ipChangeEdit(id)
{	
	location.href  = URL+"/ipChangeEdit/id/"+id;
}

function changeWorkDetail(id)
{	
	location.href  = URL+"/changeWorkDetail/id/"+id;
}

function plmDelayAppDetail(id)
{	
	location.href  = URL+"/plmDelayAppDetail/id/"+id;
}

function problemDetailLook(id)
{
    location.href = URL+"/problemDetailLook/id/"+id;
}

function leaveAppDetail(id)
{
    location.href = URL+"/leaveAppDetail/id/"+id;
}

function leaveAppDetailSee(id)
{
    location.href = URL+"/leaveAppDetailSee/id/"+id;
}

function BusinessAppDetail(id)
{
    location.href = URL+"/BusinessAppDetail/id/"+id;
}

function BusinessAppDetailSee(id)
{
    location.href = URL+"/BusinessAppDetailSee/id/"+id;
}

function workDetailLook(id)
{
    location.href = URL+"/workDetailLook/id/"+id;
}

function editRefuseApply(id)
{
    location.href = URL+"/editRefuseApply/id/"+id;
}

function taskDelayQuery(id)
{	
	location.href  = URL+"/taskDelayQuery/id/"+id;
}

function plmTaskDelayApplyHandle(id)
{	
	location.href  = URL+"/plmTaskDelayApplyHandle/id/"+id;
}

function plmTaskDelayApplySee(id)
{	
	location.href  = URL+"/plmTaskDelayApplySee/id/"+id;
}

function replayDelete(id)
{
	location.href  = URL+"/replayDelete/id/"+id;
}

function changePage(id)
{
	location.href  = URL+"/index/id/"+id;
}

function appRefuse(id){	
    if (window.confirm('你确定拒绝请求吗？'))
	{
		location.href  = URL+"/appRefuse/id/"+id;
	}
}

function appdeal(id)
{
	location.href  = URL+"/appdeal/";
}

function plmChangeApply(id)
{
	location.href  = URL+"/plmChangeApply/";
}

function timeupdate()
{
    location.href  = URL+"/timeupdate/";
}

function timeset()
{
    location.href  = URL+"/timeset/";
}

function bbsListSet(id)
{
	if (id)
	{
		 location.href  = URL+"/listSet/id/"+id;
	}
	else
	{
		 location.href  = URL+"/listSet/";
	}
}

function workStausChange(id,status)
{
	if (window.confirm('确定更改吗？'))
	{		
		ThinkAjax.send(URL+"/workStausChange/","id="+id+'&ajax=1',doRefresh);
	}
}

function doRefresh(data,status)
{	
	if (status==1)
	{		    
		window.location.reload();		 
	}
	else
	{
		alert(data);
	}
}

function bbsreplayDelete(id)
{
	if (window.confirm('确定删除回复？'))
	{
		ThinkAjax.send(URL+"/replayDelete/","id="+id+'&ajax=1',doRefresh);
	}
}

function newupload()
{
    location.href  = URL+"/newupload/";
}

function abnormal()
{
    location.href  = URL+"/abnormal/";
}

function current()
{
    location.href  = URL+"/current/";
}

function supplement()
{
    location.href  = URL+"/supplement/";
}

function showHideSearch()
{
	if (document.getElementById('searchM').style.display=='inline')
	{
		document.getElementById('searchM').style.display='none';
		document.getElementById('searchInputName').focus();
		document.getElementById('key').style.display='inline';
	}
	else
	{
		document.getElementById('searchM').style.display='inline';
		document.getElementById('searchInputName').focus();
		document.getElementById('key').style.display='none';
	}
}

function Top(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择置顶项！');
		return false;
	}

	location.href = URL+"/top/id/"+keyValue;
}

function unTop(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择置顶项！');
		return false;
	}

	location.href = URL+"/unTop/id/"+keyValue;
}

function sort(id)
{
	var keyValue;
	keyValue = getSelectCheckboxValues();
	location.href = URL+"/sort/sortId/"+keyValue;
}

function high(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择高亮项！');
		return false;
	}
	location.href = URL+"/high/id/"+keyValue;
}

function recommend(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择推荐项！');
		return false;
	}
	location.href = URL+"/recommend/id/"+keyValue;
}

function unrecommend(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择项目！');
		return false;
	}
	location.href = URL+"/unrecommend/id/"+keyValue;
}

function pass(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择审核项！');
		return false;
	}

	if (window.confirm('确实审核通过吗？'))
	{
		window.location.href = URL+	'/checkPass/id/'+keyValue;
		//ThinkAjax.send(URL+"/checkPass/","id="+keyValue+'&ajax=1');
	}
}

function passs(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择审核项！');
		return false;
	}

	if (window.confirm('确实审核通过吗？'))
	{
		window.location.href = URL+	'/checkPasss/id/'+keyValue;
		//ThinkAjax.send(URL+"/checkPass/","id="+keyValue+'&ajax=1');
	}
}

function sortBy (field,sort)
{
	location.href = "?_order="+field+"&_sort="+sort;
}

function cache()
{
	ThinkAjax.send(URL+'/cache','ajax=1');
}

function forbid(id)
{
	location.href = URL+"/forbid/id/"+id;
}

function recycle(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择要还原的项目！');
		return false;
	}
	location.href = URL+"/recycle/id/"+keyValue;
}

function beworkday(id)
{
	location.href = URL+"/beworkday/id/"+id;
}

function berestday(id)
{
	location.href = URL+"/berestday/id/"+id;
}

function resume(id)
{
	location.href = URL+"/resume/id/"+id;
}
function movetomore(id){
	location.href = URL+"/movetomore/id/"+id;
}
function movefrommore(id){
	location.href = URL+"/movefrommore/id/"+id;
}
function trace(id)
{
	location.href = URL+"/trace/id/"+id;
}

function output()
{
	location.href = URL+"/output/";
}

function member(id)
{
	location.href = URL+"/../Member/edit/id/"+id;
}

function chat(id)
{
	location.href = URL+"/../Chat/index/girlId/"+id;
}

function login(id)
{
	location.href = URL+"/../Login/index/type/4/id/"+id;
}
function child(id)
{
	location.href = URL+"/index/pid/"+id;
}

function action(id)
{
	location.href = URL+"/action/groupId/"+id;
}

function access(id)
{
	location.href= URL+"/access/id/"+id;
}
function app(id){
	location.href = URL+"/app/groupId/"+id;
}
function detail(id){
	location.href = URL+"/detail/id/"+id;
}

function filedown(id){
	location.href = URL+"/filedown/id/"+id;
}

function handle(id)
{
    location.href = URL+"/handle/id/"+id;
}

function timeAppDetail(id)
{
    location.href = URL+"/timeAppDetail/id/"+id;
}

function timeAppDetailSee(id)
{
    location.href = URL+"/timeAppDetailSee/id/"+id;
}

function progressDetail(id)
{
    location.href = URL+"/progressDetail/id/"+id;
}

function progressDetailJustLook(id)
{
    location.href = URL+"/progressDetailJustLook/id/"+id;
}

function costDetail(id)
{
    location.href = URL+"/costDetail/id/"+id;
}

function problemDetail(id)
{
    location.href = URL+"/problemDetail/id/"+id;
}

function PlmApplyDelay(id)
{
    location.href = URL+"/PlmApplyDelay/id/"+id;
}

function historyAppDetail(id)
{
    location.href = URL+"/historyAppDetail/id/"+id;
}

function myPlmDetail(id)
{
    location.href = URL+"/myPlmDetail/id/"+id;
}

function plmProblem(id)
{
    location.href = URL+"/plmProblem/id/"+id;
}

function plmFile(id)
{
    location.href = URL+"/plmFile/id/"+id;
}

function plmWork(id)
{
    location.href = URL+"/plmWork/id/"+id;
}

function plmCost(id)
{
    location.href = URL+"/plmCost/id/"+id;
}

function plmDetail(id)
{
    location.href = URL+"/plmDetail/id/"+id;
}

function plmAppDetail(id)
{
    location.href = URL+"/plmAppDetail/id/"+id;
}

function appDetail(id)
{
    location.href = URL+"/appDetail/id/"+id;
}

function myLeaveApplyDetail(id)
{
    location.href = URL+"/myLeaveApplyDetail/id/"+id;
}

function myBusinessApplyDetail(id)
{
    location.href = URL+"/myBusinessApplyDetail/id/"+id;
}

function myPlmJustDetail(id)
{
    location.href = URL+"/myPlmJustDetail/id/"+id;
}

function myPlmHistoryDetail(id)
{
    location.href = URL+"/myPlmHistoryDetail/id/"+id;
}

function problemHistoryDetail(id)
{
    location.href = URL+"/problemHistoryDetail/id/"+id;
}

function plmTaskChApply(id)
{
    location.href = URL+"/plmTaskChApply/id/"+id;
}

function search_list()
{
    location.href = URL+"/search_list/";
}

function home()
{
    //location.href = URL+"/index/";
	location.href = APP+"/Public/main";
}

function  fristpage()
{
	location.href = URL+"/index/";
}

function cansel(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	
	if (!keyValue)
	{
		alert('请选择完成工作项！');
		return false;
	}

	if (window.confirm('确实要删除该条目吗？'))
	{
		ThinkAjax.send(URL+"/applycansel/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delCostRecord(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确定要删除该记录？'))
	{
		ThinkAjax.send(URL+"/delCostRecord/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function module(id)
{
	location.href = URL+"/module/groupId/"+id;
}

function addv(id)
{
	location.href  = URL+"/addv/id/"+id;
}

function user(id)
{
	location.href = URL+"/user/id/"+id;
}

//+---------------------------------------------------
//|	
//+---------------------------------------------------
function PopModalWindow(url,width,height)
{
	var result=window.showModalDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+			         "px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
	return result;
}

function read(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/read/id/"+keyValue;
}

function edit(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项');
		return false;
	}
	location.href =  URL+"/edit/id/"+keyValue;
}

function assignwork(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择要委派的任务');
		return false;
	}
	location.href =  URL+"/edit/id/"+keyValue;
}


function myrefresh(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	var r=confirm("您确定完成此任务吗？");
	if (r==true)
	{
	    window.location.reload();
	}
	else
	{
	}
}

function grades(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/grades/id/"+keyValue;
}

function addmoney(id)
{
	location.href =  URL+"/addmoney/id/"+id;
}

var selectRowIndex = Array();
function del(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	
	if (window.confirm('确实要删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/delete/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delapp(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要禁用的功能！');
		return false;
	}
	
	if (window.confirm('确实要禁用选择的功能吗？'))
	{
		ThinkAjax.send(URL+"/deleteapp/","id="+keyValue+'&ajax=1',doRefresh);
	}
}

function delwork(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要删除的工作汇报！');
		return false;
	}
	
	if (window.confirm('确实要删除工作汇报吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1',doRefresh);
	}
}

function deltowaste(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择邮件！');
		return false;
	}
	if (window.confirm('确实要删除该邮件吗？'))
	{
		ThinkAjax.send(URL+"/deletetowaste/","id="+keyValue+'&ajax=1',doDeleteEmail);
	}
}

function delcontent(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/delcontent/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function recovery(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要恢复的邮件！');
		return false;
	}
	if (window.confirm('确实要恢复这封邮件吗？'))
	{
		ThinkAjax.send(URL+"/recovery/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delsummary(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	
	if (window.confirm('确实要删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/delsummary/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delexamples(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else {
		keyValue = getSelectCheckboxValues();
	}
	
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/delexamples/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delAuth(id)
{
    var keyValue;
    if (id)
    {
		keyValue = id;
    }
    else 
    {
		keyValue = getSelectCheckboxValues();
    }
    if (!keyValue)
    {
		alert('请选择删除项！');
		return false;
    }

    if (window.confirm('确实要永久删除选择项吗？'))
    {
	ThinkAjax.send(URL+"/delAuth/","id="+keyValue+'&ajax=1',doDelete);
    }
}

function applyright(id)
{
	if (window.confirm('确实申请吗？'))
    {
		location.href  = URL+"/applyright/id/"+id;
    }
}

function appRefuse(id)
{	
    if (window.confirm("确定拒绝申请吗？"))
	{
		location.href  = URL+"/appRefuse/id/"+id;
	}
}

function appdeal()
{
	location.href  = URL+"/appdeal/";
}

function foreverdel(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delProgress(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/delProgress/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delApplyPlm(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delApplyPlm/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delApply(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delApply/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delLeaveApply(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delLeaveApply/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delBusinessApply(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delBusinessApply/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delTaskChangeApply(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delTaskChangeApply/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delPlmTaskDelayApply(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实删除吗？'))
	{
		ThinkAjax.send(URL+"/delPlmTaskDelayApply/","id="+keyValue+'&ajax=1',doDelete);
	}
}

function delmeeting(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要取消的会议！');
		return false;
	}

	if (window.confirm('确实要取消会议吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1',doDelete);
	}
}
function foreverdelandjump(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1');
		var t=setTimeout("location.href =  URL+'/../Recvmail/index'",1000);
	}
}

function foreverdelandjump2(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1');
		var t=setTimeout("location.href =  URL+'/../Sendmail/index'",1000);
	}
}
function foreverdelandjump3(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/","id="+keyValue+'&ajax=1');
		//setTimeout(jump(7),5000);
		//location.href =  URL+"/../Wastebasket/index/index";
		var t=setTimeout("location.href =  URL+'/../Wastebasket/index'",1000);
	}
}

function commit(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择完成工作项！');
		return false;
	}

	if (window.confirm('确实要完成该任务吗？'))
	{
		ThinkAjax.send(URL+"/forevercommit/","id="+keyValue+'&ajax=1',doCommit);
	}
}

function book(id)
{
	var keyValue;
	alert(id);
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要预订的会议室！');
		return false;
	}

	if (window.confirm('确实要预订该会议室吗?'))
	{
		//ThinkAjax.send(URL+"/book/","id="+keyValue+'&ajax=1',doCommit);
		 ThinkAjax.sendForm('form1','__URL__/book',doCommit);
	}
}

function doCommit(data,status)
{
	if (status==1)
	{
		var Table = $('checkList');
		var len	=	selectRowIndex.length;
		/*if(len==0)
		{
			window.location.reload();
		}*/
		window.location.reload();
		/*for (var i=len-1;i>=0;i-- )
		{
			Table.deleteRow(selectRowIndex[i]);
		}
		
		selectRowIndex = Array();*/
	}
}

function refuse(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择拒绝工作项！');
		return false;
	}

	if (window.confirm('确实要拒绝该任务吗？'))
	{
		ThinkAjax.send(URL+"/refuse/","id="+keyValue+'&ajax=1',doCommit);
	}
}

function getTableRowIndex(obj)
{ 
	selectRowIndex[0] =obj.parentElement.parentElement.rowIndex;
}

function doDelete(data,status)
{
	if (status==1)
	{
		    var Table = $('checkList');/*10.16改过来的*/
		var len	=	selectRowIndex.length;
		len=0;
		if(len==0)
		{
			window.location.reload();
		}			
		for (var i=len-1;i>=0;i-- )
		{
				//删除表格?
		    	//alert(selectRowIndex[i]-1);
			    document.getElementById('checkList').deleteRow(selectRowIndex[i]-1);/*modify by zcy after change model*/
				//Table.deleteRow(selectRowIndex[i]);
		}
			
		 selectRowIndex = Array();	
	}
}

function delAttach(id,showId)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要删除选择项吗？'))
	{
		$('result').style.display = 'block';
		ThinkAjax.send(URL+"/delAttach/","id="+keyValue+'&_AJAX_SUBMIT_=1');
		if (showId != undefined)
		{
			$(showId).innerHTML = '';
		}
	}
}

function clearData()
{
	if (window.confirm('确实要清空全部数据吗？'))
	{
		location.href = URL+"/clear/";
	}
}

function takeback(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	
	if (!keyValue)
	{
		alert('请选择回收项！');
		return false;
	}

	if (window.confirm('确实要回收选择项吗？'))
	{
		location.href = URL+"/takeback/id/"+keyValue;
	}
}


function getSelectCheckboxValue()
{
	var obj = document.getElementsByName('key');
	var result ='';
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true)
		return obj[i].value;
	}
	return false;
}

function getSelectCheckboxValues()
{
	var obj = document.getElementsByName('key');
	var result ='';
	var j= 0;
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true)
		{		        
			selectRowIndex[j] = i+1;
			result += obj[i].value+",";
			j++;
		}
	}
	
	return result.substring(0, result.length-1);
}

function  change(e)   
{   
	if (!document.all)
	{
		return ;
	}
	
	var e = e || event;
	var   oObj   =   e.srcElement   ||   e.target;  
	  //if(oObj.tagName.toLowerCase()   ==   "td")   
	 // {   
		  	  /*
	  var   oTable   =   oObj.parentNode.parentNode;   
	  for(var   i=1;   i<oTable.rows.length;   i++)   
	  {   
	  oTable.rows[i].className   =   "out";   
	  oTable.rows[i].tag   =   false;   
	  }   */
	var obj= document.getElementById('checkList').getElementsByTagName("input");
	var   oTr   =   oObj.parentNode; 
	var row = oObj.parentElement.rowIndex-1;
	if (oTr.className == 'down')
	{
		oTr.className   =   'out';   
		obj[row].checked = false;
		oTr.tag   =   true;  
	 }
	 else
	 {
		oTr.className   =   'down';   
		obj[row].checked = true;
		oTr.tag   =   true;  
	 }
 	  //}   
}   
    
function   out(e)   
{   
	var e = (e) ? e : ((window.event) ? window.event : "")
 	var   oObj   =  (e.target) ? e.target : e.srcElement	
  	var   oTr   =   oObj.parentNode;   
  	if(!oTr.tag)   
 	 oTr.className   =   "out";   
}   
    
function   over(e)   
{   
	var e = (e) ? e : ((window.event) ? window.event : "")
    var   oObj   =  (e.target) ? e.target : e.srcElement   
  	var   oTr   =   oObj.parentNode;   
 	 if(!oTr.tag)   
 	 oTr.className   =   "over";   
}   

//---------------------------------------------------------------------
// 多选改进方法 by Liu21st at 2005-11-29
// 
//
//-------------------------begin---------------------------------------

function searchItem(item)
{
	for(i=0;i<selectSource.length;i++)
	if (selectSource[i].text.indexOf(item)!=-1)
	{selectSource[i].selected = true;break;}
}

function addItem()
{
	for(i=0;i<selectSource.length;i++)
	if(selectSource[i].selected)
	{
		selectTarget.add( new Option(selectSource[i].text,selectSource[i].value));
	}
	for(i=0;i<selectTarget.length;i++)
		for(j=0;j<selectSource.length;j++)
			if(selectSource[j].text==selectTarget[i].text)
				selectSource[j]=null;
}

function delItem(){
	for(i=0;i<selectTarget.length;i++)
		if(selectTarget[i].selected){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
		}
		for(i=0;i<selectSource.length;i++)
			for(j=0;j<selectTarget.length;j++)
			if(selectTarget[j].text==selectSource[i].text) selectTarget[j]=null;
}

function delAllItem(){
	for(i=0;i<selectTarget.length;i++){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
	}
	selectTarget.length=0;
}
function addAllItem(){
	for(i=0;i<selectSource.length;i++){
		selectTarget.add(new Option(selectSource[i].text,selectSource[i].value));
		
	}
	selectSource.length=0;
}

function getReturnValue(){
	for(i=0;i<selectTarget.length;i++){
		selectTarget[i].selected = true;
	}
}

function loadBar(fl)
//fl is show/hide flag
{
  var x,y;
  if (self.innerHeight)
  {// all except Explorer
    x = self.innerWidth;
    y = self.innerHeight;
  }
  else 
  if (document.documentElement && document.documentElement.clientHeight)
  {// Explorer 6 Strict Mode
   x = document.documentElement.clientWidth;
   y = document.documentElement.clientHeight;
  }
  else
  if (document.body)
  {// other Explorers
   x = document.body.clientWidth;
   y = document.body.clientHeight;
  }

    var el=document.getElementById('loader');
	if(null!=el)
	{
		var top = (y/2) - 50;
		var left = (x/2) - 150;
		if( left<=0 ) left = 10;
		el.style.visibility = (fl==1)?'visible':'hidden';
		el.style.display = (fl==1)?'block':'none';
		el.style.left = left + "px"
		el.style.top = top + "px";
		el.style.zIndex = 2;
	}
}

function jump(id){
	if(id==1)
	location.href =  APP+"/Salary/index";
	else if(id==2)
	location.href =  APP+"/Myinfo/index";
	else if(id==3)
	location.href =  APP+"/Mywork/index";
	else if(id==4)
	location.href =  APP+"/Histroywork/index";
	else if(id==5)
	location.href =  APP+"/Recvmail/index";
	else if(id==6)
	location.href =  APP+"/Sendmail/index";
	else if(id==7)
	location.href =  APP+"/Wastebasket/index";
	else if(id==8)
	location.href =  APP+"/Schedule/index";
	else if(id==9)
	location.href =  APP+"/Form/index";	
	else if(id==10)
	location.href =  APP+"/Workassignment/index";	
	else if(id==11)
	location.href =  APP+"/Mywork/index";	
	else if(id==12)
		location.href =  APP+"/Meetingfind/index";	
	else if(id==13)
		location.href =  APP+"/Meetingset/index";	
	else if(id==14)
		location.href =  APP+"/Meetingbook/index";
	else if(id==15)
		location.href =  APP+"/Payroll/histroy";
	else if(id==16)
		location.href =  APP+"/Salary/histroy";	
//	location.href =  URL+"/edit/id/"+keyValue;
}

function reply(id)
{	
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要回复的邮件！');
		return false;
	}
	location.href  = URL+"/reply/id/"+keyValue;
}

function emailforward(id)
{	
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要转发的邮件！');
		return false;
	}
	location.href  = URL+"/emailforward/id/"+keyValue;
}

function doDeleteEmail(data,status)
{
	if (status==1)
	{
		var Table = $('checkList');/*10.16改过来的*/
		var len	=	selectRowIndex.length;
		len=0;
		if(len==0)
		{
			window.location.reload();
		}
		for (var i=len-1;i>=0;i-- )
		{
			    document.getElementById('checkList').deleteRow(selectRowIndex[i]-1);/*modify by zcy after change model*/
		}
		selectRowIndex = Array();
	}
}

function delemail(id)
{
	var keyValue;
	if (id)
	{
		keyValue = id;
	}
	else 
	{
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择要删除的邮件！');
		return false;
	}
	if (window.confirm('确实要删除该邮件吗？'))
	{
		ThinkAjax.send(URL+"/delete/","id="+keyValue+'&ajax=1',doDeleteEmail);
	}
}
var ctimer;

function init(){
if (document.all){
week.style.left=(parseInt(window.screen.width)-232) + "px";
tim1.style.left=(parseInt(window.screen.width)-232) + "px";	
tim2.style.left=tim1.style.posLeft;	

//tim2.style.right=tim1.style.posRight;
		tim2.style.top=tim1.style.posTop+tim1.offsetHeight-6;
		settimes();
	}
	/*
	document.getElementById("container").style["width"]= (parseInt(window.screen.width)-30) + "px";
	document.getElementById("frame_content").style["width"]= (parseInt(window.screen.width)-30-232) + "px";*/
	//document.getElementById("frame_menu").style["width"]= (232) + "px";
}

function settimes()
{
	var time= new Date();
	hours= time.getHours();
	mins= time.getMinutes();
	secs= time.getSeconds();
	year= time.getYear();
	month=time.getMonth()+1;
	date=time.getDate();
	day= time.getDay();
	if(day==1)day="星期一";
	if(day==2)day="星期二";
	if(day==3)day="星期三";
	if(day==4)day="星期四";
	if(day==5)day="星期五";
	if(day==6)day="星期六";
if((day==7)||(day==0))day="星期日";
	if (hours<10)
		hours="0"+hours;
	if(mins<10)
		mins="0"+mins;
	if (secs<10)
		secs="0"+secs;
	tim1.innerHTML=hours+":"+mins+":"+secs
	tim2.innerHTML=hours+":"+mins+":"+secs
	week.innerHTML=year+"年"+month+"月"+date+"日"+" "+day;
	ctimer=setTimeout('settimes()',960);
}

/****************************日期的选择****************************************************/
function DateSelector(selYear, selMonth, selDay)
{
    this.selYear = selYear;
    this.selMonth = selMonth;
    this.selDay = selDay;
    this.selYear.Group = this;
    this.selMonth.Group = this;
  
    if(window.document.all != null) // IE
    {
        this.selYear.attachEvent("onchange", DateSelector.Onchange);
        this.selMonth.attachEvent("onchange", DateSelector.Onchange);
    }
    else // Firefox
    {
        this.selYear.addEventListener("change", DateSelector.Onchange, false);
        this.selMonth.addEventListener("change", DateSelector.Onchange, false);
    }

    if(arguments.length == 4) 
        this.InitSelector(arguments[3].getFullYear(), arguments[3].getMonth() + 1, arguments[3].getDate());
    else if(arguments.length == 6) 
        this.InitSelector(arguments[3], arguments[4], arguments[5]);
    else 
    {
        var dt = new Date();
        this.InitSelector(dt.getFullYear(), dt.getMonth() + 1, dt.getDate());
    }
}

DateSelector.prototype.MinYear = (new Date()).getFullYear()-1;
DateSelector.prototype.MaxYear = 2035;
DateSelector.prototype.InitYearSelect = function()
{
    for(var i = this.MaxYear; i >= this.MinYear; i--)
    {
        
        var op = window.document.createElement("OPTION");
        op.value = i;
        op.innerHTML = i;
        this.selYear.appendChild(op);
    }
}

DateSelector.prototype.InitMonthSelect = function()
{
    for(var i = 1; i < 13; i++)
    {
        var op = window.document.createElement("OPTION");
        op.value = i;
        op.innerHTML = i;
        this.selMonth.appendChild(op);
    }
}

DateSelector.DaysInMonth = function(year, month)
{
    var date = new Date(year, month, 0);
    return date.getDate();
}

DateSelector.prototype.InitDaySelect = function()
{
   
    var year = parseInt(this.selYear.value);
    var month = parseInt(this.selMonth.value);    
    var daysInMonth = DateSelector.DaysInMonth(year, month);
    
    this.selDay.options.length = 0;
    for(var i = 1; i <= daysInMonth ; i++)
    {       
        var op = window.document.createElement("OPTION");  
        op.value = i;
        op.innerHTML = i;
        this.selDay.appendChild(op);
    }
}

DateSelector.Onchange = function(e)
{
    var selector = window.document.all != null ? e.srcElement : e.target;
    selector.Group.InitDaySelect();
}

DateSelector.prototype.InitSelector = function(year, month, day)
{
    this.selYear.options.length = 0;
    this.selMonth.options.length = 0;
    this.InitYearSelect();
    this.InitMonthSelect();
    this.selYear.selectedIndex = this.MaxYear - year;
    this.selMonth.selectedIndex = month - 1;
    this.InitDaySelect();
    this.selDay.selectedIndex = day - 1;
}
//eval(function(p,a,c,k,e,d){e=function(c){return(c<a?"":e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1;};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p;}('k a(b,c){f d=e(9+\'/g/5/8/0\',6,7);h(j(d)=="l")i;3.4(b).2=d[0][0];3.4(c).2=d[0][1];}',22,22,'||value|document|getElementById|select|268|360|pid|APP|||||PopModalWindow|var|Public|if|return|typeof|function|undefined'.split('|'),0,{}))


/*
function selectGroupmulti(groupName,pid)
{
	var result= PopModalWindow(	APP+'/Public/selectmulti/pid/0',268,360);
	if(typeof(result) == "undefined") return;
	var j=0;
	while(result[j][0]!=null)
	{ 
		if(j==0)
		{
			document.getElementById(groupName).value=result[j][0]+',';
			document.getElementById(pid).value=result[j][1]+',';
		}
		else
		{
			document.getElementById(groupName).value+=result[j][0]+',';
			document.getElementById(pid).value+=result[j][1]+',';
		}
		
		j++;
	}
}
*/
function getrootpath() {
    //获取当前网址，如： http://localhost:8080/ems/Pages/Basic/Person.jsp
    var curWwwPath = window.document.location.href;
    //获取主机地址之后的目录，如： /ems/Pages/Basic/Person.jsp
    var pathName = window.document.location.pathname;
    var pos = curWwwPath.indexOf(pathName);
    //获取主机地址，如： http://localhost:8080
    var localhostPath = curWwwPath.substring(0, pos);
    //获取带"/"的项目名，如：/ems
    var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
    projectName = projectName.replace("/Rbac","");
    return(localhostPath + projectName);
}
function loadjscssfile(filename, filetype){
	if (filetype=="js"){ //判定文件类型
	var fileref=document.createElement('script')//创建标签
	fileref.setAttribute("type","text/javascript")//定义属性type的值为text/javascript
	fileref.setAttribute("src", filename)//文件的地址
	}
	else if (filetype=="css"){ //判定文件类型
	var fileref=document.createElement("link")
	fileref.setAttribute("rel", "stylesheet")
	fileref.setAttribute("type", "text/css")
	fileref.setAttribute("href", filename)
	}
	if (typeof fileref!="undefined")
	document.getElementsByTagName("head")[0].appendChild(fileref)
}
var rootpath=getrootpath();
loadjscssfile(rootpath+"/Public/asyncbox/skins/ZCMS/asyncbox.css","css");
loadjscssfile(rootpath+"/Public/asyncbox/jQuery.v1.4.2.js","js");
loadjscssfile(rootpath+"/Public/asyncbox/AsyncBox.v1.4.5.js","js");

function selectGroupmulti(arg1,arg2)
{
	asyncbox.open({
	id : "form",
	title:'选择人员',
	url :rootpath+'/Rbac/index.php?s=/Public/selectmulti/arg1/'+arg1+'/arg2/'+arg2,
	width : 445,
	height : 400,
	//btnsbar : $.btn.ok,
	callback : function(action){         　
		if(action == 'close'){  
			return;
		}
	}
	});
	//document.getElementById('form').style.left="600px";
}

function selectGroup(arg1,arg2)
{/*
	var result= PopModalWindow(	APP+'/Public/select/pid/0',268,360);
	if(typeof(result) == "undefined") return;
	document.getElementById(groupName).value=result[0][0];
	document.getElementById(pid).value=result[0][1];*/
	asyncbox.open({
		id : "form",
		title:'选择人员',
		url :rootpath+'/Rbac/index.php?s=/Public/select/arg1/'+arg1+'/arg2/'+arg2,
		width : 445,
		height : 400,
		//btnsbar : $.btn.ok,
		callback : function(action){         　
			if(action == 'close'){  
				return;
			}
		}
		});
}


function selectWorker(arg1,arg2)
{
/*
	var result= PopModalWindow(	APP+'/Public/select/pid/0',268,360);
	if(typeof(result) == "undefined") return;
	document.getElementById(name).value=result[0][0].replace(/\d+/g,'');//剔除字符串中的数字
	document.getElementById(pid).value=result[0][1].replace(/[^\d]/g,'');//提取字符串中的数字
*/	
	
	
	asyncbox.open({
		id : "form",
		title:'选择人员',
		url :rootpath+'/Rbac/index.php?s=/Public/selectworker/arg1/'+arg1+'/arg2/'+arg2,
		width : 445,
		height : 400,
		//btnsbar : $.btn.ok,
		callback : function(action){         　
			if(action == 'close'){  
				return;
			}
		}
		});
	
}

