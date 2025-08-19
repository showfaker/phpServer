<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: common.php 2601 2012-01-15 04:59:14Z liu21st $

//公共函数
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}

//过滤函数
function convertSpace($string)
{
	$string = str_replace(array("../","./","/","\\","\\r","\\n","\\r\\n"), "", $string);
    return $string;
}

// 缓存文件
function cmssavecache($name = '', $fields = '') {
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val ) {
		if (empty ( $fields )) {
			$data [$val [$Model->getPk ()]] = $val;
		} else {
			// 获取需要的字段
			if (is_string ( $fields )) {
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1) {
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else {
				foreach ( $fields as $field ) {
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}
function getStatus($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '禁用';
			$showImg = '<span class="badge badge-warning">禁用</span>';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<IMG SRC="__PUBLIC__/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<IMG SRC="__PUBLIC__/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			break;
		case 1 :
		default :
			$showText = '正常';
			$showImg =  '<span class="badge badge-info">正常</span>';

	}
	return ($imageShow === true) ?  $showImg  : $showText;

}
function getStatusoa($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '禁用';
			$showImg = '<span class="badge badge-warning">禁用</span>';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<IMG SRC="__PUBLIC__/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<IMG SRC="__PUBLIC__/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			break;
		case 1 :
		default :
			$showText = '正常';
			$showImg = '<span class="badge badge-info">正常</span>';

	}
	$imageShow=true;
	return ($imageShow === true) ?  $showImg  : $showText;

}


function getStatusoasch($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '<div style="color:green">已完成</div>';
			$showImg = '<IMG style="padding-top:3px;padding-left:3px"  SRC="__PUBLIC__/dwz/images/camera_test-7.png" WIDTH="14" HEIGHT="14" BORDER="0" ALT="已完成">';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<IMG SRC="__PUBLIC__/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<IMG SRC="__PUBLIC__/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">';
			break;
		case 1 :
		default :
			$showText = '<div style="color:red">进行中</div>';
			$showImg = '<IMG style="padding-top:3px;padding-left:3px" SRC="__PUBLIC__/dwz/images/35.png" WIDTH="14" HEIGHT="14" BORDER="0" ALT="进行中">';

	}
	$imageShow=false;
	return ($imageShow === true) ?  $showImg  : $showText;

}

function workStatus($status, $imageShow = true) {
	switch ($status) {
		case undefined :
			$showText = '未知状态';
			$showImg = '<IMG SRC="__PUBLIC__/Images/update.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="未完成">';
			break;
		case 0 :
			$showText = '<span class="badge badge-primary">已完成</span>';
				$showImg = '<IMG SRC="__PUBLIC__/Images/update.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="未完成">';
				break;
		case 2 :
			$showText = '<span class="badge badge-danger">超期限</span>';
			$showImg = '<IMG SRC="__PUBLIC__/Images/update.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="未完成">';
			break;
		case 1 :
		default :
			$showText = '<span class="badge badge-info">正常</span>';
			$showImg = '<IMG SRC="__PUBLIC__/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';

	}
	$imageShow=false;
	return ($imageShow === true) ?  $showImg  : $showText;

}
function getDefaultStyle($style) {
	if (empty ( $style )) {
		return 'blue';
	} else {
		return $style;
	}

}
function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array ();
	if (isset ( $_ip [$ip] )) {
		return $_ip [$ip];
	} else {
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id) {
	if (Session::is_set ( 'nodeNameList' )) {
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}

function get_pawn($pawn) {
	if ($pawn == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}
function get_patent($patent) {
	if ($patent == 0)
		return "<span style='color:green'>没有</span>";
	else
		return "<span style='color:red'>有</span>";
}


function getNodeGroupName($id) {
	if (empty ( $id )) {
		return '未分组';
	}
	if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}

function getCardStatus($status) {
	switch ($status) {
		case 0 :
			$show = '未启用';
			break;
		case 1 :
			$show = '已启用';
			break;
		case 2 :
			$show = '使用中';
			break;
		case 3 :
			$show = '已禁用';
			break;
		case 4 :
			$show = '已作废';
			break;
	}
	return $show;

}

function showStatusoa($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a class="btn btn-primary btn-xs" href="__URL__/resume/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">启用</a>';
			break;
		case 2 :
			$info = '<a style="color:green" href="__URL__/pass/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			$info = '<a  class="btn btn-warning btn-xs" href="__URL__/forbid/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		case - 1 :
			$info = '<a style="color:green" href="__URL__/recycle/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function showStatuspublicity($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a style="color:green" href="__URL__/resume/id/' . $id . '/rel/jbsxBox_publicitys" target="ajaxTodo" callback="'.$callback.'">恢复</a>';
			break;
		case 2 :
			$info = '<a style="color:green" href="__URL__/pass/id/' . $id . '/rel/jbsxBox_publicitys" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			$info = '<a style="color:red" href="__URL__/forbid/id/' . $id . '/rel/jbsxBox_publicitys" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		case - 1 :
			$info = '<a style="color:green" href="__URL__/recycle/id/' . $id . '/rel/jbsxBox_publicitys" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function showStatusoasch($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a style="color:green;" href="__URL__/resume/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">恢复为待办</a>';
			break;
		case 2 :
			$info = '<a style="color:green" href="__URL__/pass/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			$info = '<a style="color:red" href="__URL__/forbid/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">修改为完成</a>';
			break;
		case - 1 :
			$info = '<a style="color:red" href="__URL__/recycle/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function showStatususeroa($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<a style="color:red" href="__URL__/resume/id/' . $id . '/rel/userjbsxBox/curpage/'.$_SESSION[curpage].'" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		case 2 :
			$info = '<a style="color:green" href="__URL__/pass/id/' . $id . '/rel/userjbsxBox/curpage/'.$_SESSION[curpage].'" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		case 1 :
			$info = '<a style="color:green" href="__URL__/forbid/id/' . $id . '/rel/userjbsxBox/curpage/'.$_SESSION[curpage].'" target="ajaxTodo" callback="'.$callback.'">启用</a>';
			break;
		case - 1 :
			$info = '<a style="color:green" href="__URL__/recycle/id/' . $id . '/rel/userjbsxBox/curpage/'.$_SESSION[curpage].'" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function showStatus($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
			break;
		case 2 :
			$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
			break;
		case 1 :
			$info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
			break;
	}
	return $info;
}

function showStatusmyfwork($status, $id, $callback="") {
	if($status=="")
		$info = '<a href="__URL__/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>&nbsp;&nbsp;<a href="__URL__/foreverdelete/id/' . $id . '/rel/jbsxBox_flow" title="你确定要撤销吗？" style="color:red" target="ajaxTodo" callback="'.$callback.'">撤销</a>';
	else
		$info = '<a href="__URL__/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>&nbsp;&nbsp;<b style="color:orange">无法撤销</b>';
	return $info;
}
function showStatusmyfworknotme($status, $id, $callback="") {
	if($status=="")
		$info = '<a href="__URL__/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>';
	else
		$info = '<a href="__URL__/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>&nbsp;&nbsp;<b style="color:orange">无法撤销</b>';
	return $info;
}
function showStatusmyfapprove($status, $id, $moduleid,$callback="") {
	if($status=="<b style='color:green'>审批结束</b>")
		$info = '<a href="__APP__/Myflow/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>&nbsp;&nbsp;<b style="color:orange">无相关操作</b>';
	else
		$info = '<a href="__APP__/Myflow/check/id/' . $id . '" target="dialog" title="查看表单" width="800" height="420" style="color:blue">查看</a>&nbsp;&nbsp;<a href="__URL__/dealapprove/id/' . $id . '/moduleid/'.$moduleid .'" mask="true" height="460" width="900" target="dialog" style="color:blue">审批处理</a>';
	return $info;
}
function show_requist_status($status) {
	switch ($status) {
		case 0 :
			$info = '<font style="color: red;">待审批</font>';
			break;
		case 1 :
			$info = '审批中';
			break;
		case 2 :
			$info = '审批完成';
			break;
		case 3 :
			$info = '<font style="color: red;">待撤销</font>';
			break;
	    case 4 :
		    $info = '已撤销';
			break;
	}
	return $info;
}
 /*
 *status:0,在库
 *status:1,挑拨,待确认
 *status:2,使用中
 *status:3,销毁
 */
function show_store_status($status) {
	switch ($status) {
		case 0 :
			$info = '在库';
			break;
		case 1 :
			$info = '调拨,待确认';
			break;
		case 2 :
			$info = '使用中';
			break;
		case 3 :
			$info = '销毁';
			break;
	}
	return $info;
}
function showWork($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a href="javascript:beworkday(' . $id . ')">更改为工作日</a>';
			break;
		case 1 :
			$info = '<a href="javascript:berestday(' . $id . ')">更改为休息日</a>';
			break;
		case - 1 :
			$info = '<a href="javascript:abnormal(' . $id . ')">异常</a>';
			break;
	}
	return $info;
}


function showWorkInfo($status, $id) {
	switch ($status) {
		case 0 :
			$info = '<a>休息日</a>';
			break;
		case 1 :
			$info = '<a>工作日</a>';
			break;
		case - 1 :
			$info = '<a>异常</a>';
			break;
	}
	return $info;
}
/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1) {
	return rand_string ( $length, $mode );
}


function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}
function sort_by($array, $keyname = null, $sortby = 'asc') {
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree ) {
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree ) {
		$inarray [] = $array [$key];
	}
	return $inarray;
}

/**
	 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
	 +----------------------------------------------------------
 * @return string
	 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}

/* zhanghuihua */
function percent_format($number, $decimals=0) {
	return number_format($number*100, $decimals).'%';
}
/**
 * 动态获取数据库信息
 * @param $tname 表名
 * @param $where 搜索条件
 * @param $order 排序条件 如："id desc";
 * @param $count 取前几条数据 
 */
function findList($tname,$where="", $order, $count){
	$m = M($tname);
	if(!empty($where)){
		$m->where($where);
	}
	if(!empty($order)){
		$m->order($order);
	}
	if($count>0){
		$m->limit($count);
	}
	return $m->select();
}
function findById($name,$id){
	$m = M($name);
	return $m->find($id);
}
function attrById($name, $attr, $id){
	$m = M($name);
	$a = $m->where('id='.$id)->getField($attr);
	return $a;
}

function showAttendanceStatus($status, $id, $callback="")
{
	switch ($status) {
		case 1 :
			$info = '<div style="color:#ff0000">缺勤</div>';
			break;
		case 2 :
			$info = '<div style="color:#ff0000">上班未刷卡</div>';
			break;
		case 3 :
			$info = '<div style="color:#ff0000">上班迟到</div>';
			break;
		case 4 :
			$info = '<div style="color:#ff0000">下班未刷卡</div>';
			break;
		case 5 :
			$info = '<div style="color:#ff0000">下班早退</div>';
			break;
		case 6 :
			$info = '正常考勤';
			break;
		case 7 :
			$info = '补单通过';
			break;
		case 8 :
			$info = '请假时间';
			break;
		case 9 :
			$info = '出差时间';
			break;
		case 10 :
			$info = '非工作日';
			break;
		case 11 :
			$info = '非工作日';
			break;			
		default :
			$info = '未知';
	}
	return $info;
}

function showAbType($status, $id, $callback="") {
	switch ($status) {
		case 1 :
			$info = '<div style="color:#ff0000">缺勤</div>';
			break;
		case 2 :
			$info = '<div style="color:#ff0000">上班未刷卡</div>';
			break;
		case 3 :
			$info = '<div style="color:#ff0000">上班迟到</div>';
			break;
		case 4 :
			$info = '<div style="color:#ff0000">下班未刷卡</div>';
			break;
		case 5 :
			$info = '<div style="color:#ff0000">下班早退</div>';
			break;
		default :
			$info = '未知';
	}
	return $info;
}

function GetCheckAppStatus($status, $id, $callback="") {
	switch ($status) {
		case 0 :
			$info = '<div style="color:#ff0000">待审批</div>';
			break;
		case 1 :
			$info = '<div style="color:blue">审批中</div>';
			break;
		case 2 :
			$info = '通过';
			break;
		case 3 :
			$info = '<div style="color:#ff0000">未通过</div>';
			break;
		case -1 :
			$info = '<div style="color:#ff0000">驳回</div>';
			break;
		default :
			$info = '未知';
	}
	return $info;
}


?>