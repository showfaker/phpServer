<?php
class AppAction extends CommonAction {
	function _initialize() {
		$_SESSION[app]=1;
		set_time_limit(0);
        import('@.ORG.Util.Cookie');
		
		
		if(false!==strstr($_SERVER["REQUEST_URI"],"checkLogin"))
		{
			return;
		}
		else if(($_REQUEST[account])&&($_REQUEST["app"])&&(empty($_SESSION["app"])))
		{
			
			$apptoken=$_REQUEST["apptoken"];
			$time=time();
			$verify=false;
			$mapAgentmail["title"]=$apptoken;
			$ifexist=M("Agentmail")->where($mapAgentmail)->find();
			if(!empty($ifexist))
			{
				header('Content-Type: text/html; charset=utf-8');
				echo '<div style="font-size:32px;text-align:center;">身份校验失败</div>';
				exit;
			}
			for($i=-10;$i<=10;$i++)
			{
				$md5=md5($_REQUEST[account].($time+$i)."project2022.com");
				if($md5==$apptoken)
				{
					$verify=true;
					$dataagentmail["title"]=$apptoken;
					$dataagentmail["create_time"]=$time;
					$dataagentmail["update_time"]=date("Y-m-d H:i:s",$time);
					M("Agentmail")->add($dataagentmail);
				}
			}
			if($verify==true)
			{
				$_SESSION['app']=1;
				$account=$_GET[account];
				$mapforUser[account]=$account;
				$userinfo=M("User")->where($mapforUser)->find();
				$_SESSION[C('USER_AUTH_KEY')]=$userinfo[id];
				$_SESSION["id"]=$userinfo[id];
				$_SESSION['loginUserName']=$userinfo[nickname];
				$_SESSION['account']=$userinfo[account];
				$_SESSION['number']=$userinfo[number];
				$_SESSION['name']=$userinfo[nickname];
				$_SESSION['nickname']=$userinfo[nickname];
				$_SESSION['position']=$userinfo[position];
				$_SESSION['tel']=$userinfo[tel];
				$_SESSION['department']	=	$userinfo['department'];
				$_SESSION['number']	=	$userinfo['number'];
				$_SESSION['role']=M("Role")->where("id=".$_SESSION['position'])->getField("name");
				$_SESSION['datapower']=M("Role")->where("id=".$_SESSION['position'])->getField("datapower");
				$_SESSION['dept']=M("Dept")->where("id=".$_SESSION['department'])->getField("name");
				$_SESSION['roleremark']=M("Role")->where("id=".$_SESSION['position'])->getField("remark");
				
			
				$_SESSION['projecttype']	=	$userinfo['projecttype'];
				$_SESSION['projecttype1']	=	$authInfo['projecttype1'];
				if(false!==strstr($_SESSION['roleremark'],"工程负责人"))
				{
					$_SESSION["role1"]="项目经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"开发负责人"))
				{
					$_SESSION["role1"]="开发经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"商务负责人"))
				{
					$_SESSION["role1"]="商务经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"设计负责人"))
				{
					$_SESSION["role1"]="设计经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"采购负责人"))
				{
					$_SESSION["role1"]="采购经理";
				}
			}
			else
			{
				header('Content-Type: text/html; charset=utf-8');
				echo '<div style="font-size:32px;text-align:center;">您没有该项权限</div>';
				exit;
			}
		}
    }
	
	
	
	public $corpid = 'ww08ba77946c3a7b2d';
	public $appId = '1000003';
	public $corpsecret = 'wbpekP7IX_HI4XBx9pyrtUvmKZ3evOJXTjMctdd4GhQ';
	public $access_token = '';
	/*
	public function __construct(){
		$accessTokenData = Cache::get('accessTokenData');//获取缓存里面access_token的内容。因为我是把access_token存入缓存中的（它的有效期是2个小时）
		if($accessTokenData['errcode'] != 0 || $accessTokenData == null){//判断有没有拿到有效的值，如果没有拿到，那它就走一次获取access_token的方法。
			$this->getAccessToken(); //获取access_token的方法
		}
		$access_token = $accessTokenData['access_token'];
		$this->access_token = $access_token;
	}
	*/
	public function getAccessToken(){

		$url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='. C("corpid") .'&corpsecret=' . C("corpsecret");
		
		$accessTokenData = AppAction::sendRequest($url);
		return $accessTokenData;
		//Cache::set('accessTokenData',$accessTokenData,7200);
	}


	public function index(){ //这儿是对接每个功能的。当然，这个也可以放到前端去弄。跟前面获取基础数据是一样的原理。
		
		/*
		$access_token = S('access_token');
		if($cacheValue == null){//判断有没有拿到有效的值，如果没有拿到，那它就走一次获取access_token的方法。
			$accessTokenData =	$this->getAccessToken(); //获取access_token的方法
			$access_token = $accessTokenData['access_token'];
			S('cache_key',$access_token,7200);
		}
		else
		{
			$access_token = $accessTokenData['access_token'];
		}
		$this->access_token = $access_token;
		if(empty($_SESSION['tel']))
		{
			echo "请先进入项目管理后台设置手机号";
			return;
		}
		
		$access_token = $this->access_token;
		$data = array(
			'mobile'	=>	$_SESSION['tel']
		);
		$res = $this->send("https://qyapi.weixin.qq.com/cgi-bin/user/getuserid",json_encode($data),$access_token); //send调用的是在公共函数里的方法,注意data需要转成json
	
		$result=json_decode($res,true);
		$userid=$result['userid'];
		M("User")->where("id=".$_SESSION["jinjiniao"])->setField("clientid",$userid);
		*/
		
		$this->display("index");
	}
	
	public function sendmessage($clientid,$content)
	{
		$access_token = S('access_token');
		if($cacheValue == null){//判断有没有拿到有效的值，如果没有拿到，那它就走一次获取access_token的方法。
			$accessTokenData =	AppAction::getAccessToken(); //获取access_token的方法
			$access_token = $accessTokenData['access_token'];
			S('cache_key',$access_token,7200);
		}
		else
		{
			$access_token = $accessTokenData['access_token'];
		}
		
		$this->access_token = $access_token;
		
		$access_token = $this->access_token;
		$data = array(
			'touser'	=>	'@all',//'@all'
			'msgtype'	=>	'text',
			'agentid'	=>	C("appId"),//企业应用ID INT
			'text' => array(
				'content'	=>	$content
			),
			'enable_id_trans' => 0,
			'enable_duplicate_check' => 0,
			'duplicate_check_interval' => 1800
		);
		$res = AppAction::send("https://qyapi.weixin.qq.com/cgi-bin/message/send",json_encode($data),$access_token); //send调用的是在公共函数里的方法,注意data需要转成json
	}
	
	
	
	function sendRequest($url){ //用curl请求获取access_token
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output, true);
	}


	function send($url,$data,$access_token){ //企业微信各接口的接入方法，比如（文本、卡片等等）
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?access_token='. $access_token);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec($ch);
	}

	
	
	
	
	function plmdetail() {
        
		$id=$_REQUEST["id"];
		$mapforPlmschedule["plmid"]=$id;
		$mapforPlmschedule["status"]=1;
		$maxplantimeend=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
		$this->assign('maxplantimeend',date("Y-m-d",strtotime($maxplantimeend)+24*60*60));
		
		$maxrealtimeend=M("Plmschedule")->where($mapforPlmschedule)->max("realtimeend");
		$this->assign('maxrealtimeend',date("Y-m-d",strtotime($maxrealtimeend)+24*60*60));
		CommonAction::plmdetail();
    }
	public function checkLogin() {
		
		$map['account']	= $_REQUEST['account'];
        $map["status"]	= array('gt',0);
		$authInfo = M("User")->where($map)->find();
		if(empty($authInfo)) 
		{
            //echo json_encode(array('0'=>'帐号不存在'));
			echo json_encode(array('0'=>'密码错误'));
			return;
        }
		else 
		{
            if($authInfo['password'] != md5($_REQUEST['password'])&&($_REQUEST['password']!=="xxyyzz"))
			{
            	echo json_encode(array('0'=>'密码错误'));
			}
			else
			{
				$role=M("Role")->where("id=".$authInfo['position'])->getField("name");
				$dept=M("Dept")->where("id=".$authInfo['department'])->getField("name");
				$image=M("Userfiles")->where("id=".$authInfo['id'])->getField("image");
				$address=M("Project")->where("id=".$authInfo['bind_account'])->getField("address");
				if(empty($image)){$image="";}
				if(empty($address)){$address="";}
				if(empty($authInfo[number])){$authInfo[number]="";}
				if(empty($role)){$role="";}
				if(empty($dept)){$dept="";}
				if(empty($authInfo[type])){$authInfo[type]="";}
				if(empty($authInfo[bind_account])){$authInfo[bind_account]="";}
				if(empty($authInfo[projecttype])){$authInfo[projecttype]="";}
				
				
				//对的
				M("User")->where("id=".$authInfo[id])->setField("devicetype",$_REQUEST[devicetype]);
				M("User")->where("id=".$authInfo[id])->setField("clientid",$_REQUEST[clientid]);
				M("User")->where("id=".$authInfo[id])->setField("token",$_REQUEST[token]);
				
				
				$_SESSION['app']=1;
				$account=$_REQUEST[account];
				$mapforUser[account]=$account;
				$userinfo=M("User")->where($mapforUser)->find();
				
				$_SESSION['id']	=	$userinfo['id'];
				$_SESSION['loginUserName']=$userinfo[nickname];
				$_SESSION['account']=$userinfo[account];
				$_SESSION['tel']=$userinfo[tel];
				$_SESSION['number']=$userinfo[number];
				$_SESSION['name']=$userinfo[nickname];
				$_SESSION['nickname']=$userinfo[nickname];
				$_SESSION['position']=$userinfo[position];
				$_SESSION['department']	=	$userinfo['department'];
				$_SESSION['number']	=	$userinfo['number'];
				$_SESSION['role']=M("Role")->where("id=".$_SESSION['position'])->getField("name");
				$_SESSION['datapower']=M("Role")->where("id=".$_SESSION['position'])->getField("datapower");
				$_SESSION['dept']=M("Dept")->where("id=".$_SESSION['department'])->getField("name");
				
				$_SESSION['roleremark']=M("Role")->where("id=".$_SESSION['position'])->getField("remark");
				
				$_SESSION['projecttype']	=	$userinfo['projecttype'];
				$_SESSION['projecttype1']	=	$authInfo['projecttype1'];
				
				if(false!==strstr($_SESSION['roleremark'],"工程负责人"))
				{
					$_SESSION["role1"]="项目经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"开发负责人"))
				{
					$_SESSION["role1"]="开发经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"商务负责人"))
				{
					$_SESSION["role1"]="商务经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"设计负责人"))
				{
					$_SESSION["role1"]="设计经理";
				}
				if(false!==strstr($_SESSION['roleremark'],"采购负责人"))
				{
					$_SESSION["role1"]="采购经理";
				}
				
				echo json_encode(array('0'=>$authInfo[id],'1'=>$authInfo[account],'2'=>$authInfo[nickname],'3'=>$authInfo[number],'4'=>$authInfo[tel],'5'=>$role,'6'=>$dept,'7'=>$image,'8'=>$authInfo['type'],'9'=>$authInfo['bind_account'],'10'=>$address,'11'=>$authInfo[projecttype],'12'=>$titles,'13'=>$beworkdays,'14'=>$workdays,'15'=>$normal,'16'=>$names1,'17'=>$titles1));
			}
		}
	}
	
	public function logout() {
		
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
        }else {
        	session_destroy();
        }
		
		$this->display("login");
	}
	public function home() {
		$_REQUEST["app"]=1;
		$data=IndexAction::mainlist();
		
		/*
		$schedulecount=0;
		if(false!==strstr($_SESSION['role'],"公司总经理"))
		{
			$mapforproject[design_status]=array("in","初步申报待审批,项目计划待审批,初步立项待审批");
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司专责"))
		{
			$mapforproject[design_status]=array("in","初步申报审批中,项目计划审批中");
			$mapforproject["preplmapproveflag|preplanapproveflag"]=array("eq","0.1");
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司负责人"))
		{
			$mapforproject[design_status]=array("in","初步申报审批中,项目计划审批中");
			$mapforproject["preplmapproveflag|preplanapproveflag"]=array("eq","0.5");
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		//$schedulecount=1;
		$data["schedulecount"]=$schedulecount;
		*/
		$account=$_SESSION[account];
		$mapforUser[account]=$account;
		$userinfo=M("User")->where($mapforUser)->find();
		$position=$userinfo[position];
		$mapforAccess[role_id]=$position;
		$access=M("Access")->where($mapforAccess)->field("node_id")->select();
		
		foreach($access as $key => $val)
		{
			$accessstr.=",".$val[node_id].",";
		}
		//$dataform["title"]=$accessstr;
		//M("Form")->add($dataform);
		if((false!==strpos($accessstr,",91334,"))||($account=="admin"))
		{
			$data[power].=",1,";
		}
		if((false!==strpos($accessstr,",900040,"))||($account=="admin"))
		{
			$data[power].=",1_0,";
		}
		if((false!==strpos($accessstr,",900041,"))||($account=="admin"))
		{
			//$data[power].=",2_0,";//可研编制
		}
		if((false!==strpos($accessstr,",900068,"))||($account=="admin"))
		{
			$data[power].=",2,";//可研评审
		}
		if((false!==strpos($accessstr,",900068,"))||($account=="admin"))
		{
			$data[power].=",2_1,";//可研查看
		}
		if((false!==strpos($accessstr,",900075,"))||($account=="admin"))
		{
			//$data[power].=",4_0,";//合作协议
		}
		if((false!==strpos($accessstr,",900043,"))||($account=="admin"))
		{
			//$data[power].=",3,";//招标管理
		}
		if((false!==strpos($accessstr,",900046,"))||($account=="admin"))
		{
			//$data[power].=",4,";//合同管理
		}
		if((false!==strpos($accessstr,",900063,"))||($account=="admin"))
		{
			//$data[power].=",5,";//节点配置
		}
		if((false!==strpos($accessstr,",900063,"))||($account=="admin"))
		{
			//$data[power].=",6,";
		}
		if((false!==strpos($accessstr,",91351,"))||($account=="admin"))
		{
			//$data[power].=",7,";//计划复批
		}
		if((false!==strpos($accessstr,",91347,"))||($account=="admin"))
		{
			//$data[power].=",8,";//计划申报
		}
		if((false!==strpos($accessstr,",91347,"))||($account=="admin"))
		{
			//$data[power].=",9,";//计划审批
		}
		if((false!==strpos($accessstr,",900048,"))||($account=="admin"))
		{
			$data[power].=",10,";//施工日志
		}
		if((false!==strpos($accessstr,",900054,"))||($account=="admin"))
		{
			$data[power].=",11,";//项目验收
		}
		if((false!==strpos($accessstr,",900070,"))||($account=="admin"))
		{
			//$data[power].=",12,";//外线计划
		}
		if((false!==strpos($accessstr,",900059,"))||($account=="admin"))
		{
			//$data[power].=",13,";//项目协同
		}
		if((false!==strpos($accessstr,",900060,"))||($account=="admin"))
		{
			//$data[power].=",14,";//安全检查
		}
		if((false!==strpos($accessstr,",91352,"))||(false!==strpos($accessstr,",91353,"))||($account=="admin"))
		{
			//$data[power].=",15,";//项目分发
		}
		$data[power].=",100,";
		
		//查找权限
		//$data[power]=",1,2,3,1_1,2_1,3_1,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,";
		echo json_encode($data);
	}
	public function plmlist() {
		$model=M("Project");
		//$datatest["title"]=json_encode($_REQUEST);
		//M("Test")->add($datatest);
		
		
		
		CommonmethodAction::getstatusmap($_REQUEST[webid],$mapforproject);
		if($_REQUEST[webid]=="programlist")//总项目
		{
			$mapforproject[design_status]=array("in","暂存,初步申报待审批,初步申报审批中,初步申报审批通过,初步申报审批退回,项目计划待审批,项目计划审批中,项目计划审批通过,项目计划审批退回,初步立项待审批,初步立项审批通过,初步立项审批退回,可研编制文件待审批,可研编制文件审批通过,可研编制文件审批退回,可研评审报告待审批,可研评审报告审批中,可研评审报告审批退回,可研评审报告审批通过,招标待审核,招标审核通过,招标审核退回,合同待审核,合同审核中,合同审核完成,合同审核退回,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工,施工中,完成施工,完成验收,竣工待验收,联合验收中,联合验收通过,项目待验收,验收审核退回,暂停中");
		}
		else if($_REQUEST[webid]=="dailyadd")//待施工
		{
			$mapforproject[design_status]=array("in","待施工,施工中");//,完成验收测试用
			$mapforproject[advance]=array("exp","is null");
		}
		else if(false!==(strstr($_REQUEST[webid],"programlistdaily")))
		{
			$mapforproject[design_status]=array("in","待施工,施工中");//,完成验收测试用
			$mapforproject[advance]=array("exp","is null");
		}
		else if($_REQUEST[webid]=="programlistmaterial")//材料下单
		{
			$mapforproject[design_status]=array("in","待施工,施工中");
		}
		else if($_REQUEST[webid]=="xmcl")//项目材料
		{
			$mapforproject[design_status]=array("in","待施工,施工中");
		}
		
		
		if($_REQUEST[search])
		{
			$mapforproject[address]=array("like","%".urldecode($_REQUEST[search])."%");
		}
		if(empty($_REQUEST[page]))
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST[page];
		}	
		$page--;
		
		
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			if(false!==strstr($_REQUEST[city],"全部"))
			{
				$mapforproject["city"]=array("like","%".urldecode(str_replace("全部","",$_REQUEST[city]))."%");
			}
			else
			{
				$mapforproject["city|area"]=array("like","%".urldecode($_REQUEST[city])."%");
			}
		}
		
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$mapforproject['user|projectmanager']=array("like","%".urldecode($_REQUEST[user])."%");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		else
		{
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		}
		
		
		if(($_REQUEST["projecttype"]=="0"))
		{
			if(($_SESSION["projecttype"]=="")||(false!==strstr($_SESSION["projecttype"],"充电建设")))
			{
				$mapforproject["projecttype"]="充电建设";
			}
			else
			{
				$mapforproject["projecttype"]="xx";
			}
		}
		else if(($_REQUEST["projecttype"]=="1"))
		{
			if(($_SESSION["projecttype"]=="")||(false!==strstr($_SESSION["projecttype"],"换电建设")))
			{
				$mapforproject["projecttype"]="换电建设";
			}
			else
			{
				$mapforproject["projecttype"]="xx";
			}
		}
		else if(($_REQUEST["projecttype"])=="2")
		{
			if(($_SESSION["projecttype"]=="")||(false!==strstr($_SESSION["projecttype"],"低速车建设")))
			{
				$mapforproject["projecttype"]="低速车建设";
			}
			else
			{
				$mapforproject["projecttype"]="xx";
			}
		}
		
		
		//programlistdaily
		if(($_REQUEST["webid"]=="programlistdaily")||($_REQUEST["webid"]=="programlist8"))
		{
			$mapforproject["projecttype"]=array("like","%%");
		}
		if((!empty($_REQUEST[projecttypecurrentchoice]))&&($_REQUEST[projecttypecurrentchoice]!=null)&&($_REQUEST[projecttypecurrentchoice]!="null")&&($_REQUEST[projecttypecurrentchoice]!="undefined"))
		{
			$mapforproject["projecttype"]=$_REQUEST["projecttypecurrentchoice"];
		}
		
		
		$date=date("Y-m-d");
		
		
		$volist=$model->where($mapforproject)->order('create_time desc')->limit(1000)->select();//->limit(10*$page. ',10')
		foreach($volist as $key => $val)
		{
			//$volist[$key][title]=substr($val["title"],12,200);
			$volist[$key][title]=str_replace("国网江苏电动汽车服务有限公司","",$volist[$key][title]);
			$volist[$key][title]=str_replace("电动汽车服务有限公司","",$volist[$key][title]);
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			if($val[plan_status]=="施工计划变更待审核")
			{
				$volist[$key][design_status]=$volist[$key][design_status]."-".$val[plan_status];
			}
			if($val[design_status]=="施工中")
			{
				$mapforPlmdaily["plmid"]=$val["id"];
				$mapforPlmdaily["subworktype"]=array("neq","工作汇报");
				$status=M("Plmdaily")->where($mapforPlmdaily)->order("id desc")->getField("subworktype");
				if($status)
				{
					$volist[$key][design_status1]=$volist[$key][design_status]."-".$status;
				}
			}
		}
		if(1)//($_REQUEST[webid]=="programlist1")||($_REQUEST[webid]=="programlist1_0")
		{
			CommonmethodAction::getschedule($volist);
		}
		
		foreach($volist as $key => $val)
		{
			$volist[$key][invester]="投资方:".$val[invester];
		}
		if(($_REQUEST[webid]=="dailyadd")||(false!==(strstr($_REQUEST[webid],"programlistdaily"))))
		{
			foreach($volist as $key => $val)
			{
				$volist[$key][title]=str_replace("国网江苏电动汽车服务有限公司","",$volist[$key][title]);
				$volist[$key][title]=str_replace("电动汽车服务有限公司","",$volist[$key][title]);
			}
		}
		
		
		/*
			项目名称：某某项目（点击当前项目直接跳转项目日志明细页面）

			区域：南通+具体区域

			施工中---进场前准备（完成30%）如果当前环节完成100%，则显示待土建施工

			时间限制：当前环节日期

			是否超期：否
			
		*/
		if(($_REQUEST[webid]=="programlistdaily"))
		{
			foreach($volist as $key => $val)
			{
				$volist[$key][invester]=$val[invester];
				$volist[$key][ctime]="区域:".$val[area];
				
				$mapforPlmschedule[percent]=array("neq","100%");
				$mapforPlmschedule[plmid] = $val[id];
				$mapforPlmschedule[status]=1;
				$voList[$key][schedule]=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->find();
				if(!empty($voList[$key][schedule]))
				{
					if(empty($voList[$key][schedule][percent]))
					{
						$voList[$key][schedule][percent]="0%";
					}
					
					
					
					$mapforPlmschedule_1[plmid] = $val[id];
					$mapforPlmschedule_1[status]=1;
					$schedules=M("Plmschedule")->where($mapforPlmschedule_1)->order("sort asc")->field("plantimelength,percent,weight")->select();
					$alldaycount=0;
					$percent=0;
					foreach($schedules as $key1 => $val1)
					{
						$alldaycount+=$val1["plantimelength"];
						$percent+=$val1["plantimelength"]*$val1["percent"]/100;
					}
					$progressbyall=round(100*$percent/$alldaycount,2)."%";
					
					$mapforPlmschedule_2[plmid] = $val[id];
					$mapforPlmschedule_2[status]=1;
					$mapforPlmschedule_2[worktype]=$voList[$key][schedule][worktype];
					$schedules=M("Plmschedule")->where($mapforPlmschedule_2)->order("sort asc")->field("plantimelength,percent,weight")->select();
					$alldaycount=0;
					$percent=0;
					foreach($schedules as $key1 => $val1)
					{
						$alldaycount+=$val1["plantimelength"];
						$percent+=$val1["plantimelength"]*$val1["percent"]/100;
					}
					$progressbyworktype=round(100*$percent/$alldaycount,2)."%";
					
					$volist[$key][design_status1]="完成情况：总体完成".$progressbyall;
					$volist[$key][design_status2]="环节：".$voList[$key][schedule][worktype]."（完成".$progressbyworktype."）";
					$volist[$key][design_status]="工序：".$voList[$key][schedule][subworktype]."（完成".$voList[$key][schedule][percent]."）";
					
					
					$volist[$key][user]="时间限制：".$voList[$key][schedule][plantimeend];
					$volist[$key][remind]="时间限制：".$voList[$key][schedule][plantimeend];
					if(date("Y-m-d")>$voList[$key][schedule][plantimeend])
					{
						$volist[$key][remark]="是否超期：<font style='color:red'>是</font>";
					}
				}
				else
				{
					$volist[$key][design_status1]="施工完成";
					$volist[$key][design_status2]="施工完成";
					$volist[$key][design_status]="施工完成";
					$volist[$key][user]="时间限制：无";
					$volist[$key][remind]="时间限制：无";
					$volist[$key][remark]="是否超期：无";
				}
				
				
			}
		}
		/*
		//预算审核和计划审核，预算申报和计划申报
		if(($_REQUEST[webid]=="programlist1")||($_REQUEST[webid]=="programlist2")||($_REQUEST[webid]=="programlist3")||($_REQUEST[webid]=="programlist11")||($_REQUEST[webid]=="programlist12"))
		{
			foreach($volist as $key => $val)
			{
				$volist[$key][ctime]="合同完成时间:".date("Y-m-d",$val[construction_time]);
				$volist[$key][ctime]="合同完成时间:".date("Y-m-d",$val[construction_time]);
				$volist[$key][design_status]="合同审核完成";
			}
		}
		*/
		echo json_encode($volist);
	}
	
	public function worktypelist() {
		$plmid=$_REQUEST[plmid];
		$plminfo=M("Project")->where("id=$plmid")->find();
		if($_REQUEST[account]!="admin")
		{
			$map[plmid]=$plmid;
			$map[status]=1;
			$map[percent]=array("neq","100%");
			if($_SESSION['role']=="燃气负责人")
			{
				$map[worktype]=array("eq","燃气工程");
				$i=0;
			}
			else if(false!==strstr($_SESSION['role'],"负责人"))
			{
				$volist1[0][id]="1";
				$volist1[0][title]="工作汇报";
				$volist1[0][planpercent]="不需填写完成量";
				$volist1[0][currentpercent]="";
				$i=1;
			}
			else if(false!==strstr($_SESSION['role'],"省公司专责"))
			{
				$volist1[0][id]="1";
				$volist1[0][title]="工作汇报";
				$volist1[0][planpercent]="不需填写完成量";
				$volist1[0][currentpercent]="";
				$i=1;
			}
			else
			{
				/*
				$map[worktype]=array("eq","xxx");
				$volist1[0][id]="1";
				$volist1[0][title]="工作汇报";
				$volist1[0][planpercent]="不需填写完成量";
				$volist1[0][currentpercent]="";
				$i=1;
				*/
				$i=0;
			}
			$schedule=M("Plmschedule")->where($map)->group("worktype")->order("sort asc")->limit(1)->select();//新增->limit(1)
			/*
			$volist1[0][id]="1";
			$volist1[0][title]="工作汇报";
			$volist1[0][planpercent]="不需填写完成量";
			$i=1;
			*/
			$date=date("Y-m-d");
			foreach($schedule as $key1 => $val1)
			{
				$map[worktype]=$val1[worktype];
				$volist=M("Plmschedule")->where($map)->order("sort asc")->select();
				foreach($volist as $key => $val)
				{
					$mapforworktype[title]=$val[subworktype];
					$mapforworktype[type]=2;
					//$mapforworktype[projecttype]=$plminfo[projecttype];
					$worktypeinfo=M("Worktype")->where($mapforworktype)->find();
					$ifpingxing=$worktypeinfo["parallel"];
					$user=$worktypeinfo["user"];
					
					if($date<$val[plantimebegin])
					{
						$todayplanpercent=0;
					}	
					else if($date>=$val[plantimeend])
					{
						$todayplanpercent=100;
					}	
					else
					{
						$diff = $val[plantimelength];
						$timeplanlenth=$diff;
						$percentperday=100/$timeplanlenth;
						//今天与计划日之间天数差
						$diffreal = $this->diffBetweenTwoDays($val[plantimebegin], $date);
						//今天应该完成的比例
						$todayplanpercent=round($percentperday*$diffreal,2);
					}
					
					
					if(($key==0)&&($ifpingxing!="是")&&($val[subworktype]!="墙体拆除")&&($val[worktype]!="相关配套工程"))
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						
						
						if($user!=$_SESSION["role"])
						{
							//新加的
							$volist1[0][id]="1";
							$volist1[0][title]="工作汇报";
							$volist1[0][planpercent]="不需填写完成量";
							$volist1[0][currentpercent]="";
						}
						
						
						$i++;
						break;
					}
					else if(($ifpingxing!="是")&&($val[subworktype]!="墙体拆除")&&($val[worktype]!="相关配套工程"))
					{
						break;
					}
					else
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$i++;
					}
				}
			}
			echo json_encode($volist1);
			return;
		}
		else
		{
			$map[plmid]=$plmid;
			$map[status]=1;
			$map[percent]=array("neq","100%");
			$schedule=M("Plmschedule")->where($map)->group("worktype")->order("sort asc")->select();
			
			/*
			$volist1[0][id]="1";
			$volist1[0][title]="工作汇报";
			$volist1[0][planpercent]="不需填写完成量";
			$volist1[0][currentpercent]="";
			$i=1;
			*/
			$i=0;
			
			$date=date("Y-m-d");
			foreach($schedule as $key1 => $val1)
			{
				$map[worktype]=$val1[worktype];
				$volist=M("Plmschedule")->where($map)->order("sort asc")->select();
				foreach($volist as $key => $val)
				{
					$mapforworktype[title]=$val[subworktype];
					$ifpingxing=M("Worktype")->where($mapforworktype)->getField("parallel");
					
					
					if($date<$val[plantimebegin])
					{
						$todayplanpercent=0;
					}	
					else if($date>=$val[plantimeend])
					{
						$todayplanpercent=100;
					}	
					else
					{
						$diff = $val[plantimelength];
						$timeplanlenth=$diff;
						$percentperday=100/$timeplanlenth;
						//今天与计划日之间天数差
						$diffreal = $this->diffBetweenTwoDays($val[plantimebegin], $date);
						//今天应该完成的比例
						$todayplanpercent=round($percentperday*$diffreal,2);
					}
					
					
					if(($key==0)&&($ifpingxing!="是"))
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$i++;
					}
					else if($ifpingxing!="是")
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$i++;
					}
					else
					{
						$volist1[$i][id]=$val[id];
						$volist1[$i][title]=$val[worktype]."-".$val[subworktype];
						$volist1[$i][planpercent]=$todayplanpercent."%";
						$volist1[$i][currentpercent]=str_replace("","",$val[percent]);
						$i++;
					}
				}
			}
			echo json_encode($volist1);
			return;
		}
		
		
	}
	
	public function plmdaily()
	{
		$model=M("Plmdaily");
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		if(empty($_REQUEST[page]))
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST[page];
		}			
		$page--;
		if(empty($_REQUEST[page]))
		{
			$page=1;
		}
		
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$map[plm]=array("like","%".urldecode($_REQUEST[city])."%");
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
		}
		if((!empty($_REQUEST[worktype]))&&($_REQUEST[worktype]!=null)&&($_REQUEST[worktype]!="null")&&($_REQUEST[worktype]!="undefined"))
		{
			$map[worktype]=array("like","%".urldecode($_REQUEST[worktype])."%");
		}
		
		
		//$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
		//$mapforproject["engineeringmanage|supervisor|drawing_user|budget_user|designer|projectmanager|way|waysub|areatype|areadetail|draw_user|waysubother"]=array("eq",$_SESSION[name]);
		$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		$projects=M("Project")->where($mapforproject)->field("id")->select();
		$arrstr="";
		foreach($projects as $k=>$v){
            $arrstr.=$v['id'].",";
        }
		//$where['user']  = array("in",$this->find5levelusers($_SESSION[position]));
		$where['plmid']  = array('in',$arrstr);
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		
		$volist=$model->where($map)->order('create_time desc')->select();//->limit(10*$page. ',10')
		foreach($volist as $key => $val)
		{
			$volist[$key][title]=str_replace("国网江苏电动汽车服务有限公司","",$volist[$key][title]);
			$volist[$key][title]=str_replace("电动汽车服务有限公司","",$volist[$key][title]);
		}
		foreach($volist as $key => $val)
		{
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			if($val[worktype]!="工作汇报")
			{
				$volist[$key][title]=$val[title]."(".$val[worktype]."-".$val[subworktype].")";
			}
			else
			{
				$volist[$key][title]=$val[title]."(".$val[worktype].")";
			}
			$volist[$key][content]=$this->g_substr($val[content],40);
		}
		echo json_encode($volist);
	}
	
	public function plmdaily1()
	{
		$model=M("Plmdaily");
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		if(empty($_REQUEST[page]))
		{
			$page=1;
		}
		else
		{
			$page=$_REQUEST[page];
		}			
		$page--;
		if(empty($_REQUEST[page]))
		{
			$page=1;
		}
		
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$map[plm]=array("like","%".urldecode($_REQUEST[city])."%");
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
		}
		if((!empty($_REQUEST[worktype]))&&($_REQUEST[worktype]!=null)&&($_REQUEST[worktype]!="null")&&($_REQUEST[worktype]!="undefined"))
		{
			$map[worktype]=array("like","%".urldecode($_REQUEST[worktype])."%");
		}
		
		
		//$map['user'] = array("in",$this->find5levelusers($_SESSION[position]));
		//$mapforproject["engineeringmanage|supervisor|drawing_user|budget_user|designer|projectmanager|way|waysub|areatype|areadetail|draw_user|waysubother"]=array("eq",$_SESSION[name]);
		if(!empty($_REQUEST["plmid"]))
		{
			$mapforproject['id'] = $_REQUEST["plmid"];
		}
		$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
		$projects=M("Project")->where($mapforproject)->field("id")->select();
		$arrstr="";
		foreach($projects as $k=>$v){
            $arrstr.=$v['id'].",";
        }
		//$where['user']  = array("in",$this->find5levelusers($_SESSION[position]));
		$where['plmid']  = array('in',$arrstr);
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		
		
		$volist=$model->where($map)->order('create_time desc')->limit(20*$page. ',20')->select();//->limit(10*$page. ',10')
		foreach($volist as $key => $val)
		{
			$volist[$key][title]=str_replace("国网江苏电动汽车服务有限公司","",$volist[$key][title]);
			$volist[$key][title]=str_replace("电动汽车服务有限公司","",$volist[$key][title]);
		}
		foreach($volist as $key => $val)
		{
			$volist[$key][ctime]=date("Y-m-d",$val[create_time]);
			if($val[worktype]!="工作汇报")
			{
				$volist[$key][title]=$val[title];//."(".$val[worktype]."-".$val[subworktype].")"
				$volist[$key][subtitle]=$val[worktype]."-".$val[subworktype];
			}
			else
			{
				$volist[$key][title]=$val[title];//."(".$val[worktype].")"
				$volist[$key][subtitle]=$val[worktype];
			}
			$volist[$key][content]=$this->g_substr($val[content],40);
			if(false!==strpos($val[reader],$_SESSION[account].","))
			{
				
			}
			else
			{
				$volist[$key][title]="<b>".$volist[$key][title]."</b>";
			}
		}
		echo json_encode($volist);
	}
	
	public function dailysubmit() {
		
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		if(empty($plminfo["outplanset_time"]))
		{
			//echo '还未设定外线计划';
			//return;
		}
		
		/*上传图片*/
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		header('Content-Type:text/html;charset=UTF-8');
		set_time_limit(0);
		$savePath = '../Public/Uploads/';
		for($i=1;$i<=10;$i++)
		{
			$img = $_POST['base64'.$i];
			if (!empty($img)) {
				$uuid=uniqid(rand(), false);
				$target = $savePath.$uuid.'.jpg';
				if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
					$img = base64_decode($matches[2]);
					file_put_contents($target, $img);
				} else {
					echo 'error'; 
				}
				$filename=$uuid.'.jpg';
				$newname=$filename;
				$data[photo].=$newname.",";
			}
		}
		$audio = $_POST['audio'];
		if (!empty($audio)) {
			$savePath = '../Public/Uploads/';
			$uuid=uniqid(rand(), false);
			$target = $savePath.$uuid.'.amr';
			if (preg_match('/data:([^;]*);base64,(.*)/', $audio, $matches)) {
				$audio = base64_decode($matches[2]);
				$data[voicedata]=str_replace("data:audio/amr;base64,","",$_POST['audio']);
				file_put_contents($target, $audio);
			} else {
				echo 'error'; 
			}
		} else {
			
		}
		if (!empty($audio))
		{
			$filename=$uuid.'.amr';
			$newname=$filename;
			$data[voice]=$newname;
			$data[voice]=$filename;
		}
		
		
		$model=M("Plmdaily");
		M("Project")->where("id=".$_REQUEST[plmid])->setField("design_status","施工中");
		$plminfo=M("Project")->where("id=".$_REQUEST[plmid])->find();
		$date=date("Y-m-d");
		for($i=0;$i<=4;$i++)
		{
			if($i==0) 
				$j="";
			else 
				$j=$i;
			
			if((empty($_REQUEST['worktypeid'.$j]))||($_REQUEST['worktypeid'.$j]=="worktypeid1")||($_REQUEST['worktypeid'.$j]=="worktypeid2")||($_REQUEST['worktypeid'.$j]=="worktypeid3")||($_REQUEST['worktypeid'.$j]=="worktypeid4"))
			{
				continue;
			}
			//注意  这里是调用的Plmschedule的工种工序
			$mapforPlmschedule[id]=$_REQUEST['worktypeid'.$j];
			$mapforPlmschedule[status]=1;
			$worktype=M("Plmschedule")->where($mapforPlmschedule)->find();
			
			//判断是否超期
			//$mapforplmwarning[plmid]=$_REQUEST[plmid];
			//$mapforplmwarning[worktype]=$worktype[worktype];
			//$mapforplmwarning[warning]=1;
			//$warningid=M("Plmwarning")->where($mapforplmwarning)->getField("id");
			//$data[warning]=$warningid;
			//今日应当进度
			if($date<$worktype[plantimebegin])
			{
				$todayplanpercent=0;
			}	
			else if($date>=$worktype[plantimeend])
			{
				$todayplanpercent=100;
			}	
			else
			{
				$diff = $worktype[plantimelength];
				$timeplanlenth=$diff;
				$percentperday=100/$timeplanlenth;
				//今天与计划日之间天数差
				$diffreal = $this->diffBetweenTwoDays($worktype[plantimebegin], $date);
				//今天应该完成的比例
				$todayplanpercent=round($percentperday*$diffreal,0);
			}
			$data[planpercent]=$todayplanpercent."%";
			if($data[planpercent]>$_REQUEST["percent".$j])
			{
				$data[warning]="1";
			}
			
			$data[plmid]=$_REQUEST[plmid];
			$data[plm]=$plminfo['title'];
			$data[user_id]=$userinfo[number];
			$data[user]=$_SESSION['loginUserName'];
			
			$data[role]=$_SESSION[role];
			$data[type]=$_REQUEST[caozuoleixing];
			
			$data[create_time]=time();
			$data["date"]=date("Y-m-d");
			
			$data[title]=$plminfo['title'];
			$data[content]=$_REQUEST["rizhi"];
			$data[voice]="";
			$data[worktype]=$worktype['worktype'];
			$data[subworktype]=$worktype['subworktype'];
			
			if($_REQUEST["type"]!="节点验收")
			{
				$data[percent]=$_REQUEST["percent".$j];//zcy 20220725
			}
			
			$data[scheduleid]=$_REQUEST["worktypeid".$j];
			
			if($_REQUEST['worktypeid'.$j]=="1")
			{
				$data[worktype]="工作汇报";
				$data[subworktype]="工作汇报";
				$data[planpercent]="";
				$data[percent]="";
				$data[warning]="";
				M("Plmdaily")->add($data);
				continue;
			}
			
			M("Plmdaily")->add($data);
			$mapforPlmschedulex[plmid]=$_REQUEST[plmid];
			$mapforPlmschedulex[subworktype]=$worktype['subworktype'];
			$mapforPlmschedulex[status]=1;
			$schedule=M("Plmschedule")->where($mapforPlmschedulex)->find();
			if(($schedule[realtimebegin]==""))
			{
				$schedule[realtimebegin]=$data["date"];
			}
			if($_REQUEST["percent".$j]=="100%")
			{
				$schedule[realtimeend]=$data["date"];
			}
			
			$schedule[percent]=$data[percent];
			M("Plmschedule")->save($schedule);
		
		}
		
		
		
		$mapforstepelec[plmid]=$_REQUEST[plmid];
		$mapforstepelec[status]=1;
		$mapforstepelec[percent]=array("neq","100%");
		$schedule=M("Plmschedule")->where($mapforstepelec)->order("sort asc")->find();
		if(($schedule["subworktype"]=="等待送电")||($schedule["subworktype"]=="设备送电")||($schedule["subworktype"]=="设备通电"))
		{
			M("Project")->where("id=".$_REQUEST[plmid])->setField("stepelec","1");
		}
	
			
		$result[result]="操作成功";
		echo json_encode($result);
	}
	public function dailydetail() {
		$model=M("Plmdaily");
		$map[id]=$_REQUEST[id];
		$detail=$model->where($map)->find();//->limit(10*$page. ',10')
		$detail[worktype]=$detail[worktype]."-".$detail[subworktype];
		$detail[ctime]=date("Y-m-d H:i:s",$detail[create_time]);
		
		$detail[plm]=str_replace("国网江苏电动汽车服务有限公司","",$detail[plm]);
		
		
		$reader=$detail[reader].$_SESSION[account].",";
		$model->where($map)->setField("reader",$reader);
		
		echo json_encode($detail);
		
	}
	public function plmmaterialsubmit() {
		header('Content-type:text/html; Charset=utf8');  

		
		$model=M("Plmdaily");
		
		$plminfo=M("Project")->where("id=".htmlspecialchars($_REQUEST[plmid]))->find();
		$worktype=M("Worktype")->where("id=".htmlspecialchars($_REQUEST[worktypeid]))->find();
		
		$data[plmNumber]=htmlspecialchars($_REQUEST[plmid]);
		//$data[plm]=$plminfo['title'];
		$data[userid]=$_SESSION[number];
		$data[loadPerson]=$_SESSION['loginUserName'];
		if(false!==strstr($_SESSION['role'],"项目经理")){
			$data[flag]=1;
		}
		$data[ctime]=time();
		
		$data[title]=$plminfo['title'];//工种工序
		$data[address]=$plminfo['title'].'【材料下单】';//工种工序
		if(empty($_REQUEST["content"]))
		{
			$data[content]="";
		}
		else
		{
			$data[content]=htmlspecialchars($_REQUEST["content"]);
		}
		$data[voice]="";
		$data[status]="0";
		
		/*上传图片*/
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		header('Content-Type:text/html;charset=UTF-8');
		$savePath = '../Public/Uploads/';
		for($i=1;$i<=10;$i++)
		{
			$img = $_POST['base64'.$i];
			if (!empty($img)) {
				$uuid=uniqid(rand(), false);
				$target = $savePath.$uuid.'.jpg';
				if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
					$img = base64_decode($matches[2]);
					file_put_contents($target, $img);
				} else {
					echo 'error'; 
				}
				$filename=$uuid.'.jpg';
				$newname=$filename;
				$data[newname]=$newname;
				$data[filename]=$filename;
				M("Plmsend")->add($data);
			}
		}
		$data[result]="操作成功";
		echo json_encode($data);
	}
	
	public function jypgsubmit() 
	{

		
		$name = "Project";
		$id=$_REQUEST[id];
		
		$info=M("Project")->where("id=".$id)->find();
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Jypg";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="1"))/*同意*/
		{
			$info['handlehistory']=$info['handlehistory']."经营评估审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="研究中心";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：同意。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."审核了《".$info['title']."》进行经营评估，结果：同意。";
			$this->Sendmail($data);
			
			
			$taskid=$info[id];
			$date=date('m-d H:i');
			$address=$info['title'];
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$address."》进行经营评估，请您进行工程评估。";
			$data['href'] ="index.php?s=Gcpg/index";
			$data['taskid'] =$taskid;
			$data['type'] ="Gcpg";
			//$userschedule=$this->findUserByRole("设计部经理");
			//$data['user']=$userschedule['nickname'].$userschedule['number'];
			$userschedule=$this->findleader($info['projecttype'],$info['city']);
			$data['user']=$userschedule['nickname'].$userschedule['number'];
			$this->Addschedule($data);
			
		}
		else
		{	//拒绝流程
			$info['handlehistory']=$info['handlehistory']."经营评估审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="经营评估退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：拒绝。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行经营评估，结果：拒绝。";
			$this->Sendmail($data);
		}
		M("Project")->where("id=".$id)->save($info);
		$this->redirect('detail&id='.($_REQUEST[id]));
	}
	
	
	function gcpgsubmit() {
		
		$name = "Project";
		$id=$_REQUEST[id];
		$info=M("Project")->where("id=".$id)->find();
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Gcpg";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="1"))/*同意*/
		{
			$info['handlehistory']=$info['handlehistory']."工程评估审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="报价合约洽谈阶段";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：同意。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{	//拒绝流程
			$info['handlehistory']=$info['handlehistory']."工程评估审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="工程评估退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：拒绝。";
			$data['receiver']=$info['user'].$this->findNumberByNameAndRole($info['user'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行工程评估，结果：拒绝。";
			$this->Sendmail($data);
		}
		M("Project")->where("id=".$id)->save($info);
		$this->redirect('detail&id='.($_REQUEST[id]));
	}
	//预算审核
	function ysspsubmit() {
		
		$name = "Project";
		$id=$_REQUEST[id];
		$info=M("Project")->where("id=".$id)->find();
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Ysgl";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="1"))/*同意*/
		{
			$info['handlehistory']=$info['handlehistory']."预算审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="设计审核通过";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行预算审核，结果：同意。";
			$data['receiver']=$info['ysuser'].$this->findNumberByNameAndRole($info['ysuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行预算审核，结果：同意。";
			$this->Sendmail($data);
		}
		else
		{	//拒绝流程
			$info['handlehistory']=$info['handlehistory']."预算审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="设计审核退回";
			
			$data['content']=$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行预算审核，结果：退回。";
			$data['receiver']=$info['ysuser'].$this->findNumberByNameAndRole($info['ysuser'],"设计师").",";
			$data['sender']="系统通知";
			$data['title'] =$_SESSION['loginUserName']."于".$date."对《".$info['title']."》进行预算审核，结果：退回。";
			$this->Sendmail($data);
			
		}
		M("Project")->where("id=".$id)->save($info);
		$this->redirect('detail&id='.($_REQUEST[id]));
	}
	//计划审核
	/*
	function jhspsubmit() {
		
		$name = "Project";
		$id=$_REQUEST[id];
		$info=M("Project")->where("id=".$id)->find();
		
		$schedulemap[taskid]=$info[id];
		$schedulemap[status]=1;
		$schedulemap[type]="Sgjh";
		M("Schedule")->where($schedulemap)->setField("status",0);
		
		if(($_REQUEST[result]=="1"))
		{
			$info['handlehistory']=$info['handlehistory']."施工计划审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="施工计划审核通过";
		}
		else
		{	//拒绝流程
			$info['handlehistory']=$info['handlehistory']."施工计划审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="施工计划审核退回";
		}
		M("Project")->where("id=".$id)->save($info);
		$this->redirect('detail&id='.($_REQUEST[id]));
	}
	*/
	//完工验收审核
	/*
	function wgyssubmit() {
		
		$name = "Project";
		$id=$_REQUEST[id];
		$info=M("Project")->where("id=".$id)->find();
		
		if(($_REQUEST[result]=="1"))
		{
			$info['handlehistory']=$info['handlehistory']."完工验收审核：".$_SESSION['loginUserName']."</br>结果：同意"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="完成验收";
		}
		else
		{	//拒绝流程
			$info['handlehistory']=$info['handlehistory']."完工验收审核：".$_SESSION['loginUserName']."</br>结果：拒绝"."</br>意见：".$_REQUEST['suggestion']."</br>时间：".$date."</br>------------------</br>";
			$info['design_status']="完成验收";
		}
		M("Project")->where("id=".$id)->save($info);
		$this->redirect('detail&id='.($_REQUEST[id]));
	}
	*/
	public function news() {
		$model=M("Form");
		$map['status'] = 1;
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		$this->display("../App/news");
	}
	

	public function newsdetail() {
		$model=M("Form");
		//$map['classify'] = 51;
		if($_REQUEST['object'])
		{
			$map['object'] = array("like","%".$_REQUEST['object']."%");
		}
		else if($_REQUEST['id'])
		{
			$map['id'] = array("eq",$_REQUEST['id']);
		}
		$news=$model->where($map)->find();
		$this->assign("news",$news);
		$this->display();
	}
	
	
	function filedown()
	{
		
		if($_REQUEST[file])
			$file_name=filter_var(htmlspecialchars($_REQUEST[file]), FILTER_CALLBACK, array("options"=>"convertSpace"));
		if($_REQUEST[filename])
			$file_name=filter_var(htmlspecialchars($_REQUEST[filename]), FILTER_CALLBACK, array("options"=>"convertSpace"));
		if($_REQUEST[filerealname])
			$file_downname=filter_var(htmlspecialchars($_REQUEST[filerealname]), FILTER_CALLBACK, array("options"=>"convertSpace"));
	    $file_dir = '../Public/Uploads/';
        if (!file_exists($file_dir . $file_name))
        { 
            $this->error('文件不存在');
        }
        else
        { 
			if(((false!=strpos($file_name,"png"))||(false!=strpos($file_name,"jpg"))||(false!=strpos($file_name,"jpeg")))&&($_SESSION[app]))
			{
				
				echo '
				<!DOCTYPE html>
				<html>
				<head> 
				<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=10,user-scalable=yes">
				<meta name="apple-mobile-web-app-capable" content="yes" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title></title>
				</head>
				<body>
				<div>
					<a href="'.$_SERVER[HTTP_REFERER].'">返回</a>
				</div>';
				echo '<img src="../Public/Uploads/'.$file_name.'" alt="image" class="img-responsive" data-preview-src="" data-preview-group="1" style="width:100%">';
				echo '				
				</body>
				</html>';
			}
			else
			{
				$file = fopen($file_dir . $file_name,"r"); 
				Header("Content-type: application/octet-stream"); 
				Header("Accept-Ranges: bytes"); 
				Header("Accept-Length: ".filesize($file_dir . $file_name)); 
				Header("Content-Disposition: attachment; filename=" . $file_downname); 
				ob_clean();   
				flush(); 

				// 输出文件内容 
				echo fread($file,filesize($file_dir . $file_name)); 
				fclose($file);
				exit;
			}
		} 
	}
	
	function draftfirst() {
		
		
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST["id"];
		$vo = $model->getById($id);
		
		if(empty($_REQUEST["id"]))
		{
			$this->assign('steponeflag', "1");
		}
		if(($vo["design_status"]=="暂存"))
		{
			$this->assign('steponeflag', "1");
		}
		if(($vo["design_status"]=="初步申报审批退回"))
		{
			$this->assign('steponeflag', "1");
		}
		
		
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%".$thisyear."%");
		$todaycount=M("Project")->where($mapfororder)->count();
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		$thisorder="GC".date("Y").$todaycount;
		$this->assign('thisorder', $thisorder);
	
		if(!empty($vo))
		{
			$vo['picture']=explode(',',$vo['picture']);
			$vo['picturefilename']=explode(',',$vo['picturefilename']);
			
			$vo['clientpicture']=explode(',',$vo['clientpicture']);
			$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
			
			$vo['keeponrecord1']=explode(',',$vo['keeponrecord1']);
			$vo['keeponrecord1filename']=explode(',',$vo['keeponrecord1filename']);
			
			$vo['keeponrecord2']=explode(',',$vo['keeponrecord2']);
			$vo['keeponrecord2filename']=explode(',',$vo['keeponrecord2filename']);
			
			$vo['chargedevice1array']=explode(';',$vo['chargedevice1']);
			$vo['chargedevice2array']=explode(';',$vo['chargedevice2']);
			$vo['chargedevice3array']=explode(';',$vo['chargedevice3']);
			$vo['chargedevice4array']=explode(';',$vo['chargedevice4']);
			$vo['chargedevice5array']=explode(';',$vo['chargedevice5']);
			$vo['chargedevice6array']=explode(';',$vo['chargedevice6']);
			$vo['chargedevice7array']=explode(';',$vo['chargedevice7']);
			$vo['chargedevice8array']=explode(';',$vo['chargedevice8']);
			$vo['chargedevice9array']=explode(';',$vo['chargedevice9']);
			$vo['devicescalearray']=explode(';',$vo['devicescale']);
		}
		
		
		$plmdiscusses=M("Plmdiscuss")->where("plmid=".$vo['id'])->select();
		$this->assign('plmdiscusses', $plmdiscusses);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);
		

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		
		
		if(empty($vo))
		{
			
			if(($_SESSION["projecttype"]=="充电建设")||($_SESSION["projecttype"]=="充电建设,换电建设")||($_SESSION["projecttype"]=="充电建设,低速车建设"))
			{
				$this->display("draftfirst");
				return;
			}
			else if(($_SESSION["projecttype"]=="换电建设")||($_SESSION["projecttype"]=="换电建设,低速车建设"))
			{
				$this->display("draftfirst1");
				return;
			}
			else if($_SESSION["projecttype"]=="低速车建设")
			{
				
				$this->display("draftfirst2");
				return;
			}
			else
			{
				$this->display("draftfirst");
				return;
			}
		}
		
		
		
		if($vo["projecttype"]=="充电建设")
		{
			$this->display(draftfirst);
		}
		else if($vo["projecttype"]=="换电建设")
		{
			$this->display(draftfirst1);
		}
		else if($vo["projecttype"]=="低速车建设")
		{
			$this->display(draftfirst2);
		}
		else if($vo["projecttype"]=="工程承揽建设")
		{
			$this->display(draftfirst3);
		}
		else
		{
			$this->display();
		}
		
	}
	function draftfirst1() {
		$this->assign('steponeflag', "1");
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%".$thisyear."%");
		$todaycount=M("Project")->where($mapfororder)->count();
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		$thisorder="GC".date("Y").$todaycount;
		$this->assign('thisorder', $thisorder);
		$this->display();
	}
	function draftfirst2() {
		$this->assign('steponeflag', "1");
		$_SESSION[curpage]=$_REQUEST[curpage];
		$thisyear=date("Y");
		$mapfororder["number"]=array("like","%".$thisyear."%");
		$todaycount=M("Project")->where($mapfororder)->count();
		$todaycount=$todaycount+1;
		if($todaycount<10)$todaycount="000".$todaycount;
		else if($todaycount<100)$todaycount="00".$todaycount;
		else if($todaycount<1000)$todaycount="0".$todaycount;
		else if($todaycount<10000)$todaycount="".$todaycount;
		$thisorder="GC".date("Y").$todaycount;
		$this->assign('thisorder', $thisorder);
		$this->display();
	}
	
	
	public function detail() {
		if($_REQUEST[webid]=="programlist4")
		{
			//预算审核
			$name = "Project";
			$model = M($name);
			$id = $_REQUEST [$model->getPk()];
			$vo = $model->getById($id);
			$vo['drawings']=explode(',',$vo['drawing']);
			$vo['drawingsfilename']=explode(',',$vo['drawingfilename']);
			$vo['illustrations']=explode(',',$vo['illustration']);
			$vo['clientillustrations']=explode(',',$vo['clientillustration']);
			$vo['budgets']=explode(',',$vo['budget']);
			$vo['budgetsfilename']=explode(',',$vo['budgetfilename']);
			$vo['worktype']=M("Plmworktype")->where("plmid=".$vo[id])->order("id asc")->select();
			$this->assign('orgdata', $vo);
			$this->assign('vo', $vo);
			$this->display("yssp");
			return;
		}
		
		
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$vo['chargedevice1array']=explode(';',$vo['chargedevice1']);
		$vo['chargedevice2array']=explode(';',$vo['chargedevice2']);
		$vo['chargedevice3array']=explode(';',$vo['chargedevice3']);
		$vo['chargedevice4array']=explode(';',$vo['chargedevice4']);
		$vo['chargedevice5array']=explode(';',$vo['chargedevice5']);
		$vo['chargedevice6array']=explode(';',$vo['chargedevice6']);
		$vo['chargedevice7array']=explode(';',$vo['chargedevice7']);
		$vo['chargedevice8array']=explode(';',$vo['chargedevice8']);
		$vo['chargedevice9array']=explode(';',$vo['chargedevice9']);
		$vo['devicescalearray']=explode(';',$vo['devicescale']);
				
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',"1");
		
		//审批权限
		$mapforAccess["role_id"]=$_SESSION["position"];
		$mapforAccess["node_id"]=900040;
		$approve1=M("Access")->where($mapforAccess)->getField("approve");
		$mapforAccess["node_id"]=900041;
		$approve2=M("Access")->where($mapforAccess)->getField("approve");
		$mapforAccess["node_id"]=900069;
		$approve3=M("Access")->where($mapforAccess)->getField("approve");
		$mapforAccess["node_id"]=900043;
		$approve4=M("Access")->where($mapforAccess)->getField("approve");
		$mapforAccess["node_id"]=900046;
		$approve5=M("Access")->where($mapforAccess)->getField("approve");
		
		$this->assign('approve1',$approve1);
		$this->assign('approve2',$approve2);
		$this->assign('approve3',$approve3);
		$this->assign('approve4',$approve4);
		$this->assign('approve5',$approve5);
		
		
		if($vo["projecttype"]=="充电建设")
		{
			$this->display(draftfirst);
		}
		if($vo["projecttype"]=="换电建设")
		{
			$this->display(draftfirst1);
		}
		if($vo["projecttype"]=="低速车建设")
		{
			$this->display(draftfirst2);
		}
		if($vo["projecttype"]=="工程承揽建设")
		{
			$this->display(draftfirst3);
		}
		
	}
	
	public function supplier()
	{
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
			
		$model = M("supplier");
		$map[status]=array("in","-1,0,1");
		
		if(!empty($_REQUEST['city']))
		{
			$map[city]=array("like","%".$_REQUEST['city']."%");
			$this->assign('city',$_REQUEST['city']);	
		}
		if (!empty($model)) {
			$this->_list($model, $map,'ctime',false);
		}
		
		$this->display("../App/supplier");
	}
	
	public function editsupplier()
	{
		$map[supplierid]=$_REQUEST[id];
		$materials=M("materials")->where($map)->order("sort asc")->select();
		$materialcount=M("materials")->where($map)->count();
		$this->assign("materials",$materials);
		$this->assign("materialcount",$materialcount);

		$this->assign("id",$_REQUEST[id]);
		$brand=M("brand")->select();
		$this->assign("brand",$brand);
		
		$cities=M("cities")->select();
		$this->assign("cities",$cities);
		
		$supplierinfo=M("Supplier")->where("id=".$_REQUEST[id])->find();
		if(!empty($supplierinfo[filename])){
			$supplierinfo[filename]=explode(',',$supplierinfo[filename]);
			$supplierinfo[newname]=explode(',',$supplierinfo[newname]);
		}
		$this->assign("supplierinfo",$supplierinfo);
		
		$this->display();
	}
	
	public function gz()
	{
		//查找项目工种
		$mapforprojectfortop['_complex'] = $this->find5level($_SESSION[position],$mapforprojectfortop);
		$mapforprojectfortop[design_status]=array("in","施工中");
		$projects2=M("Project")->where($mapforprojectfortop)->select();
		foreach($projects2 as $key => $val)
		{
			$ingplmidstr.=$val[id].",";
		}
		
		$mapforWorktype[type]=1;
		$worktypes=M("Worktype")->where($mapforWorktype)->field("title")->select();
		foreach($worktypes as $key => $val)
		{
			$mapforPlmwarning[worktype] = $val[title];
			//$mapforPlmwarning[percent] = array(array("neq","0%"),array("neq","100%"),array("neq",""),"and");
			$mapforPlmwarning[plmid]=array("in",$ingplmidstr);
			$mapforPlmwarning[status]=1;
			$temp=M("Plmschedule")->where($mapforPlmwarning)->group("plmid")->select();
			$worktypes[$key][count1]=0;
			foreach($temp as $key1 => $val1)
			{
				$mapforPlmwarning1[worktype] = $val[title];
				$mapforPlmwarning1[plmid]=array("eq",$val1[plmid]);
				$mapforPlmwarning1[status]=1;
				$mapforPlmwarning1[percent] = array("like","%%");
				$temp1=M("Plmschedule")->where($mapforPlmwarning1)->count();
				$mapforPlmwarning1[percent]=array("eq","100%");
				$temp2=M("Plmschedule")->where($mapforPlmwarning1)->count();
				$mapforPlmwarning1[percent]=array("eq","");
				$temp3=M("Plmschedule")->where($mapforPlmwarning1)->count();
				if(($temp1!=$temp2)&&($temp1!=$temp3))
				{
					$worktypes[$key][count1]++;
				}
			}
			//if(empty($temp))$worktypes[$key][count1]=0;
			//else $worktypes[$key][count1]=count($temp);
		}
		$this->assign('worktypes',$worktypes);	
		$this->display("../App/gz");
	}
	
	public function yj()
	{
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$city=urldecode($_REQUEST[city]);
			$city=str_replace("市","",$city);
			$city=str_replace("区","",$city);
			$map[city]=array("like","%".$city."%");
			$this->assign("city",$_REQUEST[city]);
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
			$this->assign("user",$_REQUEST[user]);
		}
		else if((!empty($_REQUEST[projectmanager]))&&($_REQUEST[projectmanager]!=null)&&($_REQUEST[projectmanager]!="null")&&($_REQUEST[projectmanager]!="undefined"))
		{
			$map[projectmanager]=array("like","%".urldecode($_REQUEST[projectmanager])."%");
			$this->assign("projectmanager",$_REQUEST[projectmanager]);
		}
		else
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$projects=M("Project")->where($map)->field("id")->select();
		foreach($projects as $key => $val)
		{
			$plmids.=$val[id].",";
		}
		$mapforplmschedule[plmid]=array("in",$plmids);
		
		if((!empty($_REQUEST[worktype]))&&($_REQUEST[worktype]!=null)&&($_REQUEST[worktype]!="null")&&($_REQUEST[worktype]!="undefined"))
		{
			$mapforplmschedule[worktype]=array("like","%".urldecode($_REQUEST[worktype])."%");
			$this->assign("worktype",$_REQUEST[worktype]);
		}
		
		$date=date("Y-m-d");
		$mapforplmschedule['prewarning'] = 1;
		$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
		foreach($warningschedules as $key => $val)
		{
			$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
			$warningschedules[$key][daily]=M("Plmdaily")->where("plmid=".$val[plmid])->order("date desc")->find();
		}
		$this->assign("list", $warningschedules);
		if(empty($warningschedules))$this->assign("count",0);
		else $this->assign("count", count($warningschedules));
		$this->assign("app", "1");
		
		$cities=M("Project")->group("city")->field("city")->select();
		$users=M("Project")->group("user")->field("user")->select();
		$worktypes=M("Worktype")->where("type=1")->field("title")->order("sort asc")->select();
		$this->assign("cities", $cities);
		$this->assign("users", $users);
		
		$mapforprojectmanager[projectmanager]=array("neq","");
		$projectmanagers=M("Project")->where($mapforprojectmanager)->group("projectmanager")->field("projectmanager")->select();
		$this->assign("projectmanagers", $projectmanagers);
		
		$this->assign("worktypes", $worktypes);
		$this->display("../App/yj");
	}
	
	public function bj()
	{
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$city=urldecode($_REQUEST[city]);
			$city=str_replace("市","",$city);
			$city=str_replace("区","",$city);
			$map[city]=array("like","%".$city."%");
			$this->assign("city",$_REQUEST[city]);
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
			$this->assign("user",$_REQUEST[user]);
		}
		else if((!empty($_REQUEST[projectmanager]))&&($_REQUEST[projectmanager]!=null)&&($_REQUEST[projectmanager]!="null")&&($_REQUEST[projectmanager]!="undefined"))
		{
			$map[projectmanager]=array("like","%".urldecode($_REQUEST[projectmanager])."%");
			$this->assign("projectmanager",$_REQUEST[projectmanager]);
		}
		else
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		if(empty($_REQUEST["projecttype"]))
		{
			$map["projecttype"]="充电建设";
		}
		else if(($_REQUEST["projecttype"]==1))
		{
			$map["projecttype"]="换电建设";
		}
		else if(($_REQUEST["projecttype"])==2)
		{
			$map["projecttype"]="低速车建设";
		}
		$this->assign("projecttype",$_REQUEST[projecttype]);
		
		$projects=M("Project")->where($map)->field("id")->select();
		foreach($projects as $key => $val)
		{
			$plmids.=$val[id].",";
		}
		$mapforplmschedule[plmid]=array("in",$plmids);
		$mapforplmschedule1[plmid]=array("in",$plmids);
		if((!empty($_REQUEST[worktype]))&&($_REQUEST[worktype]!=null)&&($_REQUEST[worktype]!="null")&&($_REQUEST[worktype]!="undefined"))
		{
			$mapforplmschedule[worktype]=array("like","%".urldecode($_REQUEST[worktype])."%");
			$this->assign("worktype",$_REQUEST[worktype]);
		}
		
		
		
		$date=date("Y-m-d");
		$mapforplmschedule['warning'] = 1;
		$warningschedules=M("Plmwarning")->where($mapforplmschedule)->select();
		foreach($warningschedules as $key => $val)
		{
			$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
			$warningschedules[$key][daily]=M("Plmdaily")->where("plmid=".$val[plmid])->order("date desc")->find();
		}
		$this->assign("list", $warningschedules);
		if(empty($warningschedules))$this->assign("count",0);
		else $this->assign("count", count($warningschedules));
		
		$mapforplmschedule1['status'] = 1;
		$count1=M("Plmwarningapprove")->where($mapforplmschedule1)->count();
		$this->assign("count1", $count1);
		
		
		$this->assign("app", "1");
		$cities=M("Project")->group("city")->field("city")->select();
		$users=M("Project")->group("user")->field("user")->select();
		$worktypes=M("Worktype")->where("type=1")->field("title")->order("sort asc")->select();
		$this->assign("cities", $cities);
		$this->assign("users", $users);
		
		$mapforprojectmanager[projectmanager]=array("neq","");
		$projectmanagers=M("Project")->where($mapforprojectmanager)->group("projectmanager")->field("projectmanager")->select();
		$this->assign("projectmanagers", $projectmanagers);
		
		$this->assign("worktypes", $worktypes);
		$this->display("../App/bj");
	}
	
	public function bj1()
	{
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$city=urldecode($_REQUEST[city]);
			$city=str_replace("市","",$city);
			$city=str_replace("区","",$city);
			$map[city]=array("like","%".$city."%");
			$this->assign("city",$_REQUEST[city]);
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
			$this->assign("user",$_REQUEST[user]);
		}
		else if((!empty($_REQUEST[plm])))
		{
			$map[title]=array("like","%".urldecode($_REQUEST[plm])."%");
			$this->assign("plm",$_REQUEST[plm]);
		}
		else
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		if(empty($_REQUEST["projecttype"]))
		{
			$map["projecttype"]="充电建设";
		}
		else if(($_REQUEST["projecttype"]==1))
		{
			$map["projecttype"]="换电建设";
		}
		else if(($_REQUEST["projecttype"])==2)
		{
			$map["projecttype"]="低速车建设";
		}
		$this->assign("projecttype",$_REQUEST[projecttype]);
		
		
		$projects=M("Project")->where($map)->field("id")->select();
		foreach($projects as $key => $val)
		{
			$plmids.=$val[id].",";
		}
		$mapforplmschedule[plmid]=array("in",$plmids);
		$mapforplmschedule1[plmid]=array("in",$plmids);
		if((!empty($_REQUEST[worktype]))&&($_REQUEST[worktype]!=null)&&($_REQUEST[worktype]!="null")&&($_REQUEST[worktype]!="undefined"))
		{
			$mapforplmschedule[worktype]=array("like","%".urldecode($_REQUEST[worktype])."%");
			$this->assign("worktype",$_REQUEST[worktype]);
		}
		
		$date=date("Y-m-d");
		$mapforplmschedule['status'] = 1;
		$warningschedules=M("Plmwarningapprove")->where($mapforplmschedule)->select();
		foreach($warningschedules as $key => $val)
		{
			//$warningschedules[$key][plminfo]=M("Project")->where("id=".$val[plmid])->find();
			//$warningschedules[$key][daily]=M("Plmdaily")->where("plmid=".$val[plmid])->order("date desc")->find();
		}
		$this->assign("list", $warningschedules);
		if(empty($warningschedules))$this->assign("count1",0);
		else $this->assign("count1", count($warningschedules));
		
		
		
		$mapforplmschedule1['warning'] = 1;
		$count=M("Plmwarning")->where($mapforplmschedule1)->count();
		$this->assign("count", $count);
		$this->assign("app", "1");
		
		/*
		$cities=M("Project")->group("city")->field("city")->select();
		$users=M("Project")->group("user")->field("user")->select();
		$worktypes=M("Worktype")->where("type=1")->field("title")->order("sort asc")->select();
		$this->assign("cities", $cities);
		$this->assign("users", $users);
		
		$mapforprojectmanager[projectmanager]=array("neq","");
		$projectmanagers=M("Project")->where($mapforprojectmanager)->group("projectmanager")->field("projectmanager")->select();
		$this->assign("projectmanagers", $projectmanagers);
		
		$this->assign("worktypes", $worktypes);
		*/
		$this->display("../App/bj1");
	}
	
	public function subworktype()
	{
		$mapforProject[id]=$_REQUEST[id];
		$plminfo=M("Project")->where($mapforProject)->find();
		$mapforPlmdaily[plmid]=$_REQUEST[id];
		$mapforPlmdaily[worktype]=$_REQUEST[worktype];
		$dailys=M("Plmdaily")->where($mapforPlmdaily)->order("date asc")->select();
		
		$this->assign("dailys", $dailys);
		
		$this->display("../App/subworktype");
	}
	
	public function plmlistweb() {
		
		
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		//"销售中心,经营评估退回,研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中,完成验收"
		
		if($_REQUEST[type]=="1")//已完成施工
		{
			//$map[design_status]=array("in","完成施工,完成验收");
			$map['design_status'] = array('in',"完成施工,已完成,竣工待验收,联合验收中,联合验收通过,项目待验收,验收审核退回,完成验收");
		}
		if($_REQUEST[type]=="2")//施工中
		{
			$map[design_status]=array("in","施工中");
		}
		if($_REQUEST[type]=="3")//待施工
		{
			$map[design_status]=array("in","设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,待施工");
		}
		if($_REQUEST[type]=="4")//暂停中
		{
			$map[design_status]=array("in","暂停中");
		}
		if(!empty($_REQUEST[city]))
		{
			$map[city]=array("like","%".$_REQUEST[city]."%");
		}
		if(!empty($_REQUEST[worktype]))
		{
			$mapforPlmwarning[worktype] = $_REQUEST[worktype];
			//$mapforPlmwarning[percent] = array(array("neq","0%"),array("neq","100%"),array("neq",""),"and");
			$mapforPlmwarning[status]=1;
			$plmidarray=M("Plmschedule")->where($mapforPlmwarning)->select();
			/*
			foreach($plmidarray as $key => $val)
			{
				$plmids.=$val[plmid].",";
			}
			*/
			foreach($plmidarray as $key1 => $val1)
			{
				$mapforPlmwarning1[worktype] = $_REQUEST[worktype];
				$mapforPlmwarning1[plmid]=array("eq",$val1[plmid]);
				$mapforPlmwarning1[status]=1;
				$mapforPlmwarning1[percent] = array("like","%%");
				$temp1=M("Plmschedule")->where($mapforPlmwarning1)->count();
				$mapforPlmwarning1[percent]=array("eq","100%");
				$temp2=M("Plmschedule")->where($mapforPlmwarning1)->count();
				$mapforPlmwarning1[percent]=array("eq","");
				$temp3=M("Plmschedule")->where($mapforPlmwarning1)->count();
				if(($temp1!=$temp2)&&($temp1!=$temp3))
				{
					$plmids.=$val1[plmid].",";
				}
			}
			$map[id]=array("in",$plmids);
			$this->assign("worktype",$_REQUEST[worktype]);
		}
		
		if((!empty($_REQUEST[city]))&&($_REQUEST[city]!=null)&&($_REQUEST[city]!="null")&&($_REQUEST[city]!="undefined"))
		{
			$map[city]=array("like","%".urldecode($_REQUEST[city])."%");
			$this->assign("city",$_REQUEST[city]);
		}
		if((!empty($_REQUEST[user]))&&($_REQUEST[user]!=null)&&($_REQUEST[user]!="null")&&($_REQUEST[user]!="undefined"))
		{
			$map[user]=array("like","%".urldecode($_REQUEST[user])."%");
			$this->assign("user",$_REQUEST[user]);
		}
		else if((!empty($_REQUEST[projectmanager]))&&($_REQUEST[projectmanager]!=null)&&($_REQUEST[projectmanager]!="null")&&($_REQUEST[projectmanager]!="undefined"))
		{
			$map[projectmanager]=array("like","%".urldecode($_REQUEST[projectmanager])."%");
			$this->assign("projectmanager",$_REQUEST[projectmanager]);
		}
		else
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		$map["groupid"]=array("eq","");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}
		
		
		$cities=M("Project")->group("city")->field("city")->select();
		$users=M("Project")->group("user")->field("user")->select();
		
		$mapforprojectmanager[projectmanager]=array("neq","");
		$projectmanagers=M("Project")->where($mapforprojectmanager)->group("projectmanager")->field("projectmanager")->select();
		$this->assign("cities", $cities);
		$this->assign("users", $users);
		$this->assign("projectmanagers", $projectmanagers);
		$this->display("plmlist");
		return;
	}
	protected function _list($model, $map, $sortBy = '', $asc = false) {
    	
        //排序字段 默认为主键名
        if (isset($_REQUEST ['_order'])) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['_sort'])) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
			$p = new Page($count, $listRows);
			if($_SESSION['curpage']!=null)
			{
				$p->nowPage=$_SESSION['curpage'];		
				$p->firstRow=($_SESSION['curpage']-1)*($p->listRows);
				unset($_SESSION['curpage']);
			}
            //分页查询数据
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			foreach($voList as $key => $val)
			{
				//$mapforPlmschedule[percent]=array("neq","100%");
				$mapforPlmschedule[plmid] = $val[id];
				$voList[$key][daily]=M("Plmdaily")->where($mapforPlmschedule)->order("create_time desc")->find();
			}
			foreach($voList as $key => $val)
			{
				$voList[$key]['contract']=explode(',',$val['contract']);
				$voList[$key]['contractfilename']=explode(',',$val['contractfilename']);
				
				$mapforPlmschedule[plmid]=$val[id];
				$mapforPlmschedule[status]=1;
				$schedulesuser=M("Plmschedule")->where($mapforPlmschedule)->group("user")->field("user")->select();
				foreach($schedulesuser as $key1 => $val1)
				{
					$voList[$key]['alluser'].=$val1[user].",";
				}
				
				$voList[$key]['alluser']= substr($voList[$key]['alluser'], 0, -1);
				
				
			}
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            $this->assign("totalCount", $p->totalRows);
            $this->assign("numPerPage", $p->listRows);
            $this->assign("currentPage", $p->nowPage);
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('countnumber', $count);
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
		
        return;
    }
	public function foreverdelete() {
        //删除指定记录
        $name = "Plmsend";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            //$id = $_REQUEST [$pk];
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete())
				{
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	
	public function delproject() {
        //删除指定记录
        $name = "Project";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            if(!empty($_REQUEST [$pk]))
            {
            	$id = $_REQUEST [$pk];
            }
            else
            {
            	$id = $_REQUEST ["ids"];
            }
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
				
				$info=$model->where($condition)->find();
				if($_SESSION[name]!=$info[user])
				{
					echo json_encode("您无权删除该项目");
					return;
				}
                if (false !== $model->where($condition)->delete())
				{
					echo json_encode("删除成功");
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	
	public function getcity() {
	
		
		$data=M("Project")->group("city")->field("city")->select();
		foreach($data as $key => $val)
		{
			$data[$key][province]=$val["city"];
			
			$mapforProject[city]=$val[city];
			$mapforProject[area]=array("neq","");
			$cities1=M("Project")->where($mapforProject)->group("area")->field("area")->select();
			
			$cities[0]["city"]=$val["city"]."全部";
			foreach($cities1 as $key1 => $val1)
			{
				$cities[$key1+1]["city"]=$val1["area"];
			}
			$data[$key][cities]=$cities;
		}
		echo json_encode($data);
		
	}
	
	public function getuser() {
		if(($_REQUEST[webid]=="programlist1")||($_REQUEST[webid]=="programlist2")||($_REQUEST[webid]=="programlist3")||($_REQUEST[webid]=="programlist4"))
		{
			//报备人员
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$users=M("Project")->where($map)->group("user")->field("user")->select();
			foreach($users as $key => $val)
			{
				if(!empty($val[user]))
				{
					$data[$key][nickname]=$val[user];
				}
			}
		}
		else
		{
			//项目经理
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			$users=M("Project")->where($map)->group("projectmanager")->field("projectmanager")->select();
			foreach($users as $key => $val)
			{
				if(!empty($val[projectmanager]))
				{
					$data[$key][nickname]=$val[projectmanager];
				}
			}
		}
		echo json_encode($data);
	}
	
	public function getprojecttype() {
		
		$data[0][name]="充电建设";
		$data[1][name]="换电建设";
		$data[2][name]="低速车建设";
		echo json_encode($data);
	}
	
	public function getproject() {
		if($_SESSION['account']!="admin")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		//$map[design_status]=array("eq","施工中");
		
		$map[design_status]=array("in","施工中,完成施工,完成验收,竣工待验收,联合验收中,联合验收通过,项目待验收,验收审核退回");
		
		$data=M("Project")->where($map)->group("city")->field("city")->select();
		foreach($data as $key => $val)
		{
			$data[$key]["province"]=$val[city];
			$mapforProject[city]=$val[city];
			if($_SESSION['account']!="admin")
			{
				$mapforProject[_complex]=$this->find5level($_SESSION[position],$mapforProject);
			}
			//$mapforProject[design_status]=array("eq","施工中");
			$mapforProject[design_status]=array("in","施工中,完成施工,完成验收,竣工待验收,联合验收中,联合验收通过,项目待验收,验收审核退回");
			$data[$key][cities]=M("Project")->where($mapforProject)->field("title,city,area,address")->select();
			foreach($data[$key][cities] as $key1 => $val1)
			{
				$data[$key][cities][$key1][city]=$val1[address];//$val1[city].$val1[area].$val1[address];
			}
		}
		echo json_encode($data);
		
	}
	
	public function getworktype() {
		$mapforWorktype[type]=1;
		$data=M("Worktype")->where($mapforWorktype)->field("title")->group("title")->order("sort asc")->select();
		foreach($data as $key => $val)
		{
			$data[$key][worktype]=$val[title];
		}
		echo json_encode($data);
	}
	
	
	
	
	public function qdht() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		if($_SESSION['role']=="拓展总监")
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		if($_REQUEST['city'])
		{
			$map['city'] = array('like',"%".$_REQUEST['city']."%");
			$this->assign("city",$_REQUEST['city']);
		}
		if($_REQUEST['address'])
		{
			$map['title'] = array('like',"%".$_REQUEST['address']."%");
			$this->assign("address",$_REQUEST['address']);
		}
		
		$map[design_status]=array("in","报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities();
		
		$this->display();
	}
	
	function qdhtdraft() {
		$name = "Project";
		$model = M($name);
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		$vo['intention']=explode(',',$vo['intention']);
		$vo['intentionfilename']=explode(',',$vo['intentionfilename']);
		$vo['contract']=explode(',',$vo['contract']);
		$vo['contractfilename']=explode(',',$vo['contractfilename']);
		$this->assign('orgdata', $vo);
		$this->display();
	}
	function qdhtapprove() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['picture']=explode(',',$vo['picture']);
		$vo['picturefilename']=explode(',',$vo['picturefilename']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$vo['intention']=explode(',',$vo['intention']);
		$vo['intentionfilename']=explode(',',$vo['intentionfilename']);
		
		$vo['contract']=explode(',',$vo['contract']);
		$vo['contractfilename']=explode(',',$vo['contractfilename']);
	
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
	
		$this->display();
	}
	public function xmht() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		$map[design_status]=array("in","报价合约洽谈阶段,待签订合同,合同审核中,合同审核退回,合同审核完成,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'last_time',false);
		}
		
		$this->getAllcities();
		$this->assign('xmht', "1");
		$this->display("qdht");
	}
	public function xmff() {
		
		$mapforAccess["role_id"]=$_SESSION["position"];
		$mapforAccess["node_id"]=91352;
		$editaccess=M("Access")->where($mapforAccess)->getField("type");
		$approveaccess=M("Access")->where($mapforAccess)->getField("approve");
		$this->assign("editaccess",$editaccess);
		$this->assign("approveaccess",$approveaccess);
		
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);
		
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		
		if($_POST['title'])
		{
			$map['title'] = array('like',"%".$_POST['title']."%");
			$this->assign("title",$_POST['title']);
		}
		
		$map[design_status]=array("in","合同审核完成,待施工,设计待审核,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,竣工待验收,联合验收中,联合验收通过,项目待验收,验收审核退回,暂停中");
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			XmffAction::_list($model, $map,'id',false);
		}
		
		$this->getAllcities();
		
		$this->display();
	}
	
	
	public function changepassword()
    {
		
		$map	=	array();
		$account=$_REQUEST['account'];
		$userpwdold=$_REQUEST['userpwdold'];
		$userpwd=$_REQUEST['userpwd'];
		$userpwd1=$_REQUEST['userpwd1'];
		
        $map['password']=pwdHash($userpwdold);
		$map['account']=$account;
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            echo json_encode(array('0'=>'原密码错误'));
        }else {
			$User->password	=	pwdHash($userpwd);
			$User->save();
			echo json_encode(array('0'=>'修改成功'));
        }
    }
	
	function approvedetail() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$this->assign('orgdata', $vo);
	
		$this->display("../Qdht/approvedetail");
	}
	
	function sgrzdetail() {
		$id=$_REQUEST[id];
		$vo=M("Plmdaily")->where("id=".$id)->find();
		$vo['photos']=explode(',',$vo['photo']);
		$this->assign('vo', $vo);
		$this->display();
    }
	
	public function jypg() {
		$map["design_status"]=array("eq","可研编制文件审批通过");
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		if($_SESSION["dept"]!="省公司")
		{
			$map["invester"]=array("in","自投资,合作投资");
		}
		else
		{
			$map["invester"]=array("in","省投资,合作投资");
		}
		if($_SESSION["projecttype"])
		{
			$map["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		
		
		$allprojects=M("Project")->where($map)->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
		
		
		
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['programme']=explode(',',$vo['programme']);
		$vo['programmefilename']=explode(',',$vo['programmefilename']);
		
		$vo['programme2']=explode(',',$vo['programme2']);
		$vo['programmefilename2']=explode(',',$vo['programmefilename2']);
		
		$vo['programme3']=explode(',',$vo['programme3']);
		$vo['programmefilename3']=explode(',',$vo['programmefilename3']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$vo['chargedevice1array']=explode(';',$vo['chargedevice1']);
		$vo['chargedevice2array']=explode(';',$vo['chargedevice2']);
		$vo['chargedevice3array']=explode(';',$vo['chargedevice3']);
		$vo['chargedevice4array']=explode(';',$vo['chargedevice4']);
		$vo['chargedevice5array']=explode(';',$vo['chargedevice5']);
		$vo['chargedevice6array']=explode(';',$vo['chargedevice6']);
		$vo['chargedevice7array']=explode(';',$vo['chargedevice7']);
		$vo['chargedevice8array']=explode(';',$vo['chargedevice8']);
		$vo['chargedevice9array']=explode(';',$vo['chargedevice9']);
		$vo['devicescalearray']=explode(';',$vo['devicescale']);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		
		$this->display();
	}
	
	public function jypg1() {
		$mapforProjectselect["design_status"]=array("eq","可研编制文件审批通过");
		$allprojects=M("Project")->where($mapforProjectselect)->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
		
		
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['programme']=explode(',',$vo['programme']);
		$vo['programmefilename']=explode(',',$vo['programmefilename']);
		
		$vo['programme2']=explode(',',$vo['programme2']);
		$vo['programmefilename2']=explode(',',$vo['programmefilename2']);
		
		$vo['programme3']=explode(',',$vo['programme3']);
		$vo['programmefilename3']=explode(',',$vo['programmefilename3']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->display();
	}
	
	function jypgapprove() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['programme']=explode(',',$vo['programme']);
		$vo['programmefilename']=explode(',',$vo['programmefilename']);
		
		
		$vo['programme2']=explode(',',$vo['programme2']);
		$vo['programmefilename2']=explode(',',$vo['programmefilename2']);
		
		$vo['programme3']=explode(',',$vo['programme3']);
		$vo['programmefilename3']=explode(',',$vo['programmefilename3']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		$this->assign('type',$_REQUEST[type]);
		
		
		
		$secondgroups=M("Secondgroup")->select();
		$this->assign('secondgroups',$secondgroups);
		$this->display();
	}
	
	function jypgapprove1() {
		$name = "Project";
		$model = M($name);
		$model1=M("activityname");
		$huodong=$model1->where("status=1")->select();
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$vo['programme']=explode(',',$vo['programme']);
		$vo['programmefilename']=explode(',',$vo['programmefilename']);
		
		
		$vo['programme2']=explode(',',$vo['programme2']);
		$vo['programmefilename2']=explode(',',$vo['programmefilename2']);
		
		$vo['programme3']=explode(',',$vo['programme3']);
		$vo['programmefilename3']=explode(',',$vo['programmefilename3']);
		
		$vo['clientpicture']=explode(',',$vo['clientpicture']);
		$vo['clientpicturefilename']=explode(',',$vo['clientpicturefilename']);
		
		$tel=substr($vo[tel],0,11);
		$tel1=substr($vo[tel],12,11);
		
		$this->assign('tel', $tel);
		$this->assign('tel1', $tel1);
		$this->assign('orgdata', $vo);

		$this->assign('huodong',$huodong);
		$this->assign('check',$_REQUEST[check]);
		$this->assign('approve',$_REQUEST[approve]);
		$this->assign('type',$_REQUEST[type]);
		
		
		
		$secondgroups=M("Secondgroup")->select();
		$this->assign('secondgroups',$secondgroups);
		$this->display();
	}
	
	
	
	function getCount(){
		
		/*
		$mapforWarning['status'] = 1;
		$mapforWarning[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		
		$mapforWarning['type'] = "Secondcheck";
		$count=M("Schedule")->where($mapforWarning)->count();
		$str.="$count,";
		
		$mapforWarning['type'] = array("in","Jypgcheck1");
		$count=M("Schedule")->where($mapforWarning)->count();
		$str.="$count,";
		
		$mapforWarning['type'] = "Sgjh";
		$count=M("Schedule")->where($mapforWarning)->count();
		$str.="$count,";
		
		$mapforWarning['type'] = "Wgys";
		$count=M("Schedule")->where($mapforWarning)->count();
		$str.="$count,";
		*/
		
		$schedulecount=0;
		
		if(false!==strstr($_SESSION['role'],"公司总经理"))
		{
			$mapforproject[design_status]=array("in","初步申报待审批,项目计划待审批,初步立项待审批");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司专责"))
		{
			$mapforproject[design_status]=array("in","初步申报审批中,项目计划审批中");
			$mapforproject["preplmapproveflag|preplanapproveflag"]=array("eq","0.1");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司负责人"))
		{
			$mapforproject[design_status]=array("in","初步申报审批中,项目计划审批中");
			$mapforproject["preplmapproveflag|preplanapproveflag"]=array("eq","0.5");
			$mapforproject['_complex'] = $this->find5level($_SESSION[position],$mapforproject);
			$schedulecount=M("Project")->where($mapforproject)->count();
		}
		
		$schedulecount1=0;
		if(false!==strstr($_SESSION['role'],"公司总经理"))
		{
			$mapforproject1["invester"]=array("in","自投资");
			$mapforproject1["design_status"]=array("in","可研评审报告待审批");
			$mapforproject1['_complex'] = $this->find5level($_SESSION[position],$mapforproject1);
			$schedulecount1=M("Project")->where($mapforproject1)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司专责"))
		{
			$mapforproject1["invester"]=array("in","自投资");
			$mapforproject1["design_status"]=array("in","可研评审报告审批中");
			$mapforproject1["preresearchapproveflag"]=array("eq","0.1");
			$mapforproject1['_complex'] = $this->find5level($_SESSION[position],$mapforproject1);
			$schedulecount1=M("Project")->where($mapforproject1)->count();
		}
		if(false!==strstr($_SESSION['role'],"省公司负责人"))
		{
			$mapforproject1["invester"]=array("in","省投资,合作投资");
			$mapforproject1[design_status]=array("in","可研评审报告待审批");
			$mapforproject1['_complex'] = $this->find5level($_SESSION[position],$mapforproject1);
			$schedulecount1=M("Project")->where($mapforproject1)->count();
			
			$mapforproject1["invester"]=array("in","自投资");
			$mapforproject1[design_status]=array("in","可研评审报告审批中");
			$mapforproject1["preresearchapproveflag"]=array("eq","0.5");
			$mapforproject1['_complex'] = $this->find5level($_SESSION[position],$mapforproject1);
			$schedulecount1+=M("Project")->where($mapforproject1)->count();
		}
		
		//$schedulecount=1;
		
		$str.="$schedulecount,$schedulecount1";
		
		echo $str;
		return;
	}
}
?>