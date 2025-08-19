<?php
class AindexAction extends CommonAction {
	function _initialize() {
		
		
    }
	
	
	function setworktypeperiod() 
	{
		$worktypes=M("Worktype")->select();
		foreach($worktypes as $key => $val)
		{
			if(!empty($val["period1"]))
			{
				$mapforWorktype["id"]=$val["pid"];
				$data["worktype"]=M("Worktype")->where($mapforWorktype)->getField("title");
				$data["subworktype"]=$val["title"];
				$data["begin"]=0;
				$data["end"]=10000;
				$data["pid"]=$val["id"];
				$data["period"]=$val["period1"];
				M("Worktypeperiod")->add($data);
			}
			
		}
		
	}
	
	function deal() 
	{

		$plmid=62278;
		$classify="主项节点库";
		
		$mapforSchedule["plmid"]=$plmid;
		$mapforSchedule["status"]=1;
		$mapforSchedule["classify"]=$classify;
		$schedules=M("Plmschedule")->where($mapforSchedule)->order("id asc")->select();
		
		$plminfo=M("Project")->where("id=$plmid")->find();
		
		
		foreach($schedules as $key => $val)
		{
			$mapforWorktype["title"]=$val["worktype"];
			$mapforWorktype["projecttype"]=$plminfo["projecttype"];
			$mapforWorktype["classify"]=$classify;
			$mapforWorktype["type"]="1";
			$worktypes=M("Worktype")->where($mapforWorktype)->select();
			$worktypeids="";
			foreach($worktypes as $key1 => $val1)
			{
				$worktypeids.=$val1["id"].",";
			}
			
			$mapworktype["classify"]=$val["classify"];
			$mapworktype["title"]=$val["subworktype"];
			$mapworktype["pid"]=array("in",$worktypeids);
			$worktypedetail=M("Worktype")->where($mapworktype)->find();
			
			$dataplmworktype["content"]=time();
			$dataplmworktype["plmid"]=$plmid;
			$dataplmworktype["title"]=$val["subworktype"];
			$dataplmworktype["classify"]=$val["classify"];
			$dataplmworktype["attribute"]=$val["attribute"];
			$dataplmworktype["sort"]=$worktypedetail["sort"];
			$dataplmworktype["qualityunit"]=$worktypedetail["qualityunit"];
			$dataplmworktype["pid"]=$worktypedetail["pid"];
			$dataplmworktype["type"]=$worktypedetail["type"];
			$dataplmworktype["parallel"]=$worktypedetail["parallel"];
			
			$mapforUser["nickname"]=$val["user"];
			$userinfo=M("User")->where($mapforUser)->find();
		
			$dataplmworktype["user_id"]=$userinfo["number"];
			$dataplmworktype["create_time"]=time();
			$dataplmworktype["pworktype"]=$val["worktype"];
			dump($dataplmworktype);
			M("Plmworktype")->add($dataplmworktype);
			
		}
	}
	function getFile() {
		$projects=M("Project")->select();
		foreach($projects as $key => $val)
		{
			$programme2=explode(",",$val["programme2"]);
			$programmefilename2=explode(",",$val["programmefilename2"]);
			foreach($programme2 as $key1 => $val1)
			{
				if(!file_exists("../Public/Uploads/".$val1))
				{
					echo ("<div>项目名：".$val["title"]."</div><div>源文件：".$val1."</div><div>文件名：".$programmefilename2[$key1]."</div></br>");
				}
			}
			
		}
		
    }
	
	
	// 框架首页
	public function index() {
		
		//渠道编码	project-manage-system
		//密钥	2SA6ew3543iLA1Jmq6r5qixcfzT2zb55
		//http://demo.hbasesoft.com:8888/oauth/token?client_id=*************&grant_type=password&scope=read%20write&username=xmgl_admin&password=***************
		//http://demo.hbasesoft.com:8888/oauth/token?client_id=project-manage-system&grant_type=password&scope=read%20write&username=xmgl_admin&password=2SA6ew3543iLA1Jmq6r5qixcfzT2zb55
		
		
		$scheme = $_SERVER['REQUEST_SCHEME']; //协议
		$domain = $_SERVER['HTTP_HOST']; //域名/主机
		$requestUri = $_SERVER['REQUEST_URI']; //请求参数
		//将得到的各项拼接起来
		$currentUrl = $scheme . "://" . $domain . $requestUri;


		$datatest[title]=($currentUrl);
		$datatest[content]=($GLOBALS['HTTP_RAW_POST_DATA']);
		$datatest[create_time]=date("Y-m-d H:i:s");
		M("Formtest")->add($datatest);
		
		
		$dataarray=json_decode(filter_var($datatest[content], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),true);
		
		$_SESSION["userToken"]=$dataarray["userToken"];
		$_SESSION["userInfo"]=$dataarray["userInfo"];
		$_SESSION["userName"]=$dataarray["userInfo"]["userName"];
		
		$datatest[title]=$dataarray["userInfo"]["userName"];
		$datatest[content]=($GLOBALS['HTTP_RAW_POST_DATA']);
		$datatest[create_time]=date("Y-m-d H:i:s");
		M("Formtest")->add($datatest);
		
		$mapforUser["nickname"]=$_SESSION["userName"];
		$userinfo=M("User")->where($mapforUser)->find();
		$mapforUser["id"]=$userinfo["id"];
		M("User")->where($mapforUser)->setField("token",$_SESSION["userToken"]["token"]["access_token"]);
		M("User")->where($mapforUser)->setField("token_time",time());
		
		
		$map            =   array();
		$map['account']	= "xmgl_admin";
        $map["status"]	=	array('gt',0);
		import ( '@.ORG.Util.RBAC' );
        $authInfo = RBAC::authenticate($map);
		$time=time();
		$ip=get_client_ip();
		$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
		$_SESSION['email']	=	$authInfo['email'];
		$_SESSION['account']	=	$authInfo['account'];
		$_SESSION['loginUserName']		=	$authInfo['nickname'];
		$_SESSION['name']		=	$authInfo['nickname'];
		$_SESSION['nickname']		=	$authInfo['nickname'];
		$_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
		$_SESSION['login_count']	=	$authInfo['login_count'];
		$_SESSION['id']	=	$authInfo['id'];
		$_SESSION['name']	=	$authInfo['nickname'];
		$_SESSION['sex']	=	$authInfo['sex'];
		$_SESSION['tel']	=	$authInfo['tel'];
		$_SESSION['graduated']	=	$authInfo['graduated'];
		$_SESSION['graduationtime']	=	$authInfo['graduationtime'];
		$_SESSION['education']	=	$authInfo['education'];
		$_SESSION['company']	=	$authInfo['company'];
		$_SESSION['comaddress']	=	$authInfo['comaddress'];
		$_SESSION['department']	=	$authInfo['department'];
		$_SESSION['position']	=	$authInfo['position'];
		$_SESSION['number']	=	$authInfo['number'];
		$_SESSION['usernamefortalk']	=	$authInfo['usernamefortalk'];
		$_SESSION['skin']	=	$authInfo['skin'];
		$_SESSION['plmNumber']	=	$authInfo['bind_account'];
		$_SESSION[namenumber] = $authInfo['nickname'].$authInfo['number'];
		if(($authInfo['account']=='admin')) {
			$_SESSION['administrator']		=	true;
		}
		
		$_SESSION['role']=M("Role")->where("id=".$_SESSION['position'])->getField("name");
		$_SESSION['datapower']=M("Role")->where("id=".$_SESSION['position'])->getField("datapower");
		$_SESSION['dept']=M("Dept")->where("id=".$_SESSION['department'])->getField("name");
		$_SESSION['city']=str_replace("电动","市",$_SESSION['dept']);
		$_SESSION['citynull']=str_replace("电动","",$_SESSION['dept']);
		if($_SESSION['dept']=="省公司")
		{
			$_SESSION['city']="南京市";
			$_SESSION['citynull']="南京";
		}
		$_SESSION['projecttype']	=	$authInfo['projecttype'];
		$_SESSION['projecttype1']	=	$authInfo['projecttype1'];
		if(false!==strstr($_SESSION['role'],"公司专责"))
		{
			if($_SESSION['dept']=="省公司")
			{
				$_SESSION["role1"]="省公司专责";
			}
			else
			{
				$_SESSION["role1"]="地市公司专责";
			}
		}
		
		//保存登录信息
		$User	=	M('User');
		$ip		=	get_client_ip();
		$time	=	time();
		$data = array();
		$data['id']	=	$authInfo['id'];
		$data['last_login_time']	=	$time;
		$data['login_count']	=	array('exp','login_count+1');
		$data['last_login_ip']	=	$ip;
		$User->save($data);
		
		$logdata = array();
		$logdata['account']	=	$authInfo['account'];
		$logdata['number']	=	$authInfo['number'];
		$logdata['time']	=	$time;
		$logdata['ip']	=	$ip;
		$logdata['name']	=	$authInfo['nickname'];
		M("Log")->add($logdata);
		
		RBAC::saveAccessList();
		$company = "Cominfo";
		$commodel = D($company);
		$first=$commodel->getField("ifweather");
		$_SESSION['first']	=	$first;
		$mapcominfo[id]=1;
		$commodel->where($mapcominfo)->setField("ifweather",1);
		
		echo "回调成功";
	}
	
	
	public function test() {
		
		//$data=M("Formtest")->order("id desc")->find();
		//dump (get_object_vars(json_decode(json_decode($data["content"]))));
		dump($_SESSION);
	}
	
}
?>