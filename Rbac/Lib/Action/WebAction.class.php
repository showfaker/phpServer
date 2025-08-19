<?php
class WebAction extends CommonAction {
	function _initialize() {
		
    }
	// 经济测算
	function cesuan() {
		$this->display();
    }
	
	//充电建设测算
	function calc3() {
		$this->display();
	}
	//换电建设电量统计
	function calc1() {
		$this->display();
	}
	function calc1_2() {
		$this->display();
	}
	function calc1_3() {
		$this->display();
	}
	function calc1_4() {
		$this->display();
	}
	//换电建设里程统计
	function calc2() {
		$this->display();
	}
	function calc2_2() {
		$this->display();
	}
	function calc2_3() {
		$this->display();
	}
	//项目列表
	function programlist()
	{
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("webid",$_REQUEST["webid"]);
		$this->assign("city",$_REQUEST["city"]);
		$this->assign("projecttype",$_REQUEST["projecttype"]);
		$this->assign("user",$_REQUEST["user"]);
		$this->display();
	}
	//施工日志项目列表
	function programlistdaily() {
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("webid",$_REQUEST["webid"]);
		$this->assign("city",$_REQUEST["city"]);
		$this->assign("projecttype",$_REQUEST["projecttype"]);
		$this->assign("user",$_REQUEST["user"]);
		
		$this->display();
	}
	//施工日志列表
	function dailylist() {
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("plmid",$_REQUEST["plmid"]);
		$this->display();
	}
	function dailyadd() {
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("plmid",$_REQUEST["plmid"]);
		$this->display();
	}
	function dailydetail() {
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("id",$_REQUEST["id"]);
		$this->display();
	}
	//用户信息
	function userinfo() {
		$this->assign("account",$_REQUEST['account']);
		$this->assign("name",$_REQUEST['name']);
		$this->assign("role",$_REQUEST['role']);
		$this->assign("part",$_REQUEST['part']);
		$this->assign("tel",$_REQUEST['tel']);
		$this->display();
	}
	//修改密码
	function modpassword(){
		$this->assign("account",$_REQUEST['account']);
		$this->display();
	}
	
	function plmprogress() {
		$this->assign("account",$_REQUEST["account"]);
		$this->assign("plmid",$_REQUEST["plmid"]);
		
		$plminfo=M("Project")->where("id=".$_REQUEST["plmid"])->find();
		$this->assign("plminfo",$plminfo);
		
		$mapforPlmschedule_2[plmid] = $_REQUEST[plmid];
		$mapforPlmschedule_2[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule_2)->order("sort asc")->select();
		foreach($schedules as $key => $val)
		{
			if(empty($val["percent"]))
			{
				$schedules[$key]["percent"]="0%";
			}
		}
		$this->assign("schedules",$schedules);
		$this->display();
	}
}
?>