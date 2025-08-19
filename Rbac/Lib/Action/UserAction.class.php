<?php
// 后台用户模块
class UserAction extends CommonAction {
	function _filter(&$map){
		//$map['id'] = array('egt',2);
		
		
		if(!empty($_REQUEST[account]))
		{
			$map['account'] = array('like',"%".$_POST['account']."%");
			$this->assign('account',$_REQUEST[account]);
		}
		if(!empty($_REQUEST[nickname]))
		{
			$map['nickname'] = array('like',"%".$_POST['nickname']."%");
			$this->assign('nickname',$_REQUEST[nickname]);
		}
		/*
		$userarray=M("User")->select();
		foreach($userarray as $key => $val)
		{
			$mapforUser[id]=$val[id];
			M("User")->where($mapforUser)->setField("intime",date("Y-m-d",$val[create_time]));
		}
		*/
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		//$map['account']  = array('neq','admin');/*modified by zcy at 2012-11-8*/
		$name = $this->getActionName();
		$model = D($name);
		if(!empty($_REQUEST[deptid]))
		{
			$map[department]=$_REQUEST[deptid];
			$this->assign('deptid',$map[department]);
		}
		if(!empty($_REQUEST[roleid]))
		{
			$map[position]=$_REQUEST[roleid];
			$this->assign('roleid',$map[position]);
		}
		if (!empty($model)) {
			$this->_list($model, $map,"number",true);
		}
		if($_SESSION[skin]!=3)
		{
			$depts=M(Dept)->order('id asc')->field('id,name')->select();
			foreach($depts as $key => $val)
			{
				$mapforUser[department]=$val[id];
				$depts[$key]["count"]=M(User)->where($mapforUser)->count();
				$allcount+=$depts[$key]["count"];
			}
			$this->assign('dept',$depts);
			$this->assign('allcount',$allcount);
			/*dump($vodept);*/
			$namerole='role';
			$modelrole = M($namerole);
			$vorole=$modelrole->field('id,name')->order("name")->select();
			foreach($vorole as $key => $val)
			{
				$mapforUser1[position]=$val[id];
				$vorole[$key]["count"]=M(User)->where($mapforUser1)->count();
				$allcount1+=$vorole[$key]["count"];
			}
			$this->assign('vorole', $vorole);
			$this->assign('allcount1', $allcount1);
			if(((!empty($_REQUEST[deptid]))||(!empty($_REQUEST[pageNum])))&&($_REQUEST[flag]==1))
			{
				$this->display(listindex);
			}
			else
			{
				$this->display(indexoa);
			}
		}
		else
		{
			$this->display();
		}
		return;
	}
	
	
	public function indexfornote() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		//$map['account']  = array('neq','admin');/*modified by zcy at 2012-11-8*/
		if($_GET['deptid']!=null)
		{
			$map['department']= $_GET['deptid'];
			$this->assign('deptid', $_GET['deptid']);
		}
		$name = $this->getActionName();
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map);
		}
	
		
		$Dept = D ( "Dept" );
		$listdept = $Dept->getField ('id,name');
		$this->assign('listdept', $listdept);
		if($_SESSION[skin]!=3)
		{
			/*if($_GET[search]==2)
			{
				$this->display(notelist1);
			}*/
			/*else */if(($_GET[deptid]!=null)||($_GET[search]==1))
			{
				$this->display(notelist);
			}
			else
			{
				$this->display(indexfornoteoa);
			}
		}
		else
		{
			$this->display();
		}
		return;
	}
	

	public function sys() {
		$users=M("User")->where("status=1")->field("id,tel")->select();
		
		$access_token = S('access_token');
		if($cacheValue == null){
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
		
		foreach($users  as $key => $val)
		{
			
			if(!empty($val["tel"]))
			{
				$data = array(
					'mobile'	=>	$val['tel']
				);
				$res = AppAction::send("https://qyapi.weixin.qq.com/cgi-bin/user/getuserid",json_encode($data),$access_token); //send调用的是在公共函数里的方法,注意data需要转成json
			
				$result=json_decode($res,true);
				$userid=$result['userid'];
				M("User")->where("id=".$val["id"])->setField("clientid",$userid);
			}
		}
		
		$this->success("同步成功");
		
		
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
			//分页查询数据
	
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			
			foreach ($voList as $keyvo => $valvo) {
				$info1 = CommonAction::finddept($valvo['department']);
				$info2 = CommonAction::findposition($valvo['position']);
				$voList[$keyvo][dept]=$info1[name];
				$voList[$keyvo][pos]=$info2[name];
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
	
	
	
	// 检查帐号
	public function checkAccount() {
        //if(!preg_match('/^[0-9]\w{4,}$/i',$_POST['account'])) 
        //    $this->error( '用户名必须是字母，且5位以上！');

		
		if(empty($_REQUEST['account']))
		{
		    $this->ajaxReturn('必须填写用户名！','必须填写用户名！',0);
		}
		else
		{
		    $User = M("User");
            // 检测用户名是否冲突
            $name  =  $_REQUEST['account'];
            $result  =  $User->getByAccount($name);
            if($result) 
		    {
        	    $this->ajaxReturn('该用户名已经存在！','该用户名已经存在！',0);
            }
			else 
			{
           	    $this->ajaxReturn('该用户名可以使用！','该用户名可以使用！',1);
            }
		}
    }
	
	// 插入数据
	public function insert() {
		
		
		$reg = '/^(?=.*[a-zA-Z])(?=.*[1-9])(?=.*[\W]).{6,}$/';
		preg_match($reg,$_REQUEST[password],$matches);
		if(!$matches){
			//$this->error('密码必须包含英文,数字,符号！');
		}


		// 创建数据对象
		$User	 =	 D("User");
		$account=$_REQUEST["account"];
		$accountarray=explode("、",$account);
		$nicknamearray=explode("、",$_REQUEST["nickname"]);
		$telarray=explode("、",$_REQUEST["tel"]);
		$numberarray=explode("、",$_REQUEST["number"]);
		if(count($accountarray)>=2)
		{
			foreach($accountarray as $key => $val)
			{
				$User->account=$val;
				$User->nickname=$nicknamearray[$key];
				$User->tel=$telarray[$key];
				$User->skin=1;
				$User->status=1;
				$User->number=$numberarray[$key];
				$User->position=$_REQUEST[position];
				$User->department=$_REQUEST[department];
				$User->password=md5($_REQUEST[password]);
				$User->projecttype=$_REQUEST[projecttype];
				
				
				
				$cityarray=$_REQUEST["city"];
				foreach($cityarray as $key => $val)
				{
					$city.=$val.",";
				}
				$User->city=$city;
				
				$User->create_time=time();
				$position=$User->position;
				if($result	 =	 $User->add()) {
					
					
					$roleinfo=M("Role")->where("id=".$_REQUEST[position])->find();
					if(false!==strstr($roleinfo["name"],"采购供应商"))
					{
						$supplierdata["supplier"]=$nicknamearray[$key];
						$supplierdata["name"]=$nicknamearray[$key];
						$supplierdata["telephone"]=$telarray[$key];
						$supplierdata["status"]=1;
						$supplierdata["ctime"]=time();
						M("Supplier")->add($supplierdata);
					}
					
					$this->addRole($result,$position);
				}else{
					$this->error('用户添加失败！');
				}
			}
			///////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->redirect('index');
			return;
		}
		
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			$nickname=$User->nickname;
			$numflag=preg_match('/\d/is', $nickname);
			if(!empty($numflag))
			{
				$this->error("姓名中不能包含数字");
			}
			
			$User->skin=1;
			$User->usernamefortalk = $Talk['username'];	
			$User->status=1;
			$User->number=$_REQUEST[number];
			$position=$User->position;
			
			$cityarray=$_REQUEST["city"];
			foreach($cityarray as $key => $val)
			{
				$city.=$val.",";
			}
			$User->city=$city;
			
				
			if($result	 =	 $User->add()) {
				$this->addRole($result,$position);
				
				
				$roleinfo=M("Role")->where("id=".$_REQUEST[position])->find();
				if(false!==strstr($roleinfo["name"],"采购供应商"))
				{
					$supplierdata["supplier"]=$_REQUEST["nickname"];
					$supplierdata["name"]=$_REQUEST["nickname"];
					$supplierdata["telephone"]=$_REQUEST["tel"];
					$supplierdata["status"]=1;
					$supplierdata["ctime"]=time();
					M("Supplier")->add($supplierdata);
				}
					
					
					
				$this->redirect('index');
				//$this->success('用户添加成功！员工权限自适应当前职位，如需另外授权，请到<角色权限>中操作');
			}else{
				$this->error('用户添加失败！');
			}
		}
		
	}
	
	
	function update() {
		if($_REQUEST[password]!="")
		{
			$reg = '/^(?=.*[a-zA-Z])(?=.*[1-9])(?=.*[\W]).{6,}$/';
			preg_match($reg,$_REQUEST[password],$matches);
			if(!$matches)
			{
				//$this->error('密码必须包含英文,数字,符号！');
			}
		}
		//B('FilterString');
		$User	 =	 D("User");
		$info=M("User")->where("id=".$_REQUEST[id])->find();
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create())
		{
			$this->error($model->getError());
		}
		// 更新数据
		/*
		if($model->number==0)
		{
			$this->error("编号不能为0");
		}
		
		if(!is_numeric($model->number))
		{
			$this->error("编号必须为纯数字");
		}
		*/
		$nickname=$model->nickname;
		$numflag=preg_match('/\d/is', $nickname);
		if(!empty($numflag))
		{
			$this->error("姓名中不能包含数字");
		}
		
		$userid=$model->id;
		$roleid=$model->position;
		$model->skin=1;
		if($_REQUEST[password]!="")
		{
			$model->password=md5($_REQUEST[password]);
		}
		else
		{
			$model->password=$info[password];
		}
		$model->number=$_REQUEST[number];
		$cityarray=$_REQUEST["city"];
		foreach($cityarray as $key => $val)
		{
			$city.=$val.",";
		}
		$model->city=$city;
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			///////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			
			$map['user_id']=$userid;
			$RoleUser = M("RoleUser");
			$RoleUser->where($map)->delete();
			$this->addRole($userid,$roleid);
			
			$roleinfo=M("Role")->where("id=".$roleid)->find();
			if(false!==strstr($roleinfo["name"],"采购供应商"))
			{
				$supplierdata["supplier"]=$_REQUEST["nickname"];
				$supplierdata=M("Supplier")->where($supplierdata)->find();
				$supplierdata["name"]=$_REQUEST["nickname"];
				$supplierdata["telephone"]=$_REQUEST["tel"];
				$supplierdata["status"]=1;
				M("Supplier")->save($supplierdata);
			}
				
				
				
			$this->redirect('index');
			//$this->success('编辑成功!员工权限自适应当前职位');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}

	protected function addRole($userId,$roleId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入网站编辑组
        //$RoleUser->role_id	=	3;
		$RoleUser->role_id	=	$roleId;
		$RoleUser->add();
	}

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['usrid'];
        $password = $_POST['password'];
        
        if(md5($_POST['verify'])	!= $_SESSION['verify']) {
        	$this->error('验证码错误！');
        }
        
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = M('User');
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
		
		//$Users = M('Users');
		//$data['password']	=	md5($password);
		//$map['id']=$id;
		//$results	=	$Users->where($map)->save($data);
        if((false !== $result)/*&(false !== $results)*/) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
	function edit() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	
    	$namedept='dept';
    	$modeldept = M($namedept);
    	$vodept=$modeldept->field('id,name')->select();
    	
    	/*dump($vodept);*/
    	$namerole='role';
    	$modelrole = M($namerole);
    	$vorole=$modelrole->field('id,name')->select();
    	
    	$this->assign('vodept', $vodept);
    	$this->assign('vo', $vo);
    	$this->assign('vorole', $vorole);
    	
		
		$userimsi=M("userimsi");
		$user=M("user");
		$userId=$_REQUEST['id'];
		$userNumber=$user->getFieldById($userId,'number');
		$imsi=$userimsi->getFieldByUsernumber($userNumber,'imsi');
		
		$this->assign('imsi', $imsi);
		
		if($_SESSION[skin]!=3)
		{
			$this->display(editoa);
		}
		else
		{
			$this->display();
		}
    }
    public function add() {
    	$name = $this->getActionName();
    	$model = M($name);
    	$id = $_REQUEST [$model->getPk()];
    	$vo = $model->getById($id);
    	 
    	$namedept='dept';
    	$modeldept = M($namedept);
    	$vodept=$modeldept->field('id,name')->select();
    	 
    	$namerole='role';
    	$modelrole = M($namerole);
    	$vorole=$modelrole->field('id,name')->select();
    	 
    	$this->assign('vodept', $vodept);
    	$this->assign('vo', $vo);
    	$this->assign('vorole', $vorole);
    	if($_SESSION[skin]!=3)
		{
			$this->display(addoa);
		}
		else
		{
			$this->display();
		};
    }	
	
	
	public function addnote() {
		$this->display(addnoteoa);
    }
	function editnote() {
        $name = "Usernote";
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3)
        {
        	$this->display(editnoteoa);
        }
        else
        {
        $this->display();
        }
    }
	
	 function updatenote() {
        $name = "Usernote";
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
		$model->update_time=time();
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            ///////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->redirect('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	public function foreverdeletenote() {
        //删除指定记录
        $name = "Usernote";
        $model = D($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                if (false !== $model->where($condition)->delete())
				{
                    //echo $model->getlastsql();
                    $this->redirect('index');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
        $this->forward();
    }
	public function search_list()
	{	    
        $this->display();
	}
	
	function indexforusernote()
	{
		$map[number] = $_SESSION[number];
		$name = "Usernote";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map);
		}
		if($_SESSION[skin]!=3)
		{
			$this->display(notelist1);
		}
		else
		{
			$this->display();
		}
		return;
	}
	
	function insertnote() {
        //B('FilterString');
        $name = "Usernote";
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
		$model->number=$_SESSION[number];
		$model->create_time=time();
        $list = $model->add();
        if ($list !== false) { //保存成功
            ///////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }
	
	//根据编号查找用户
	public function getNicknameById($id)
	{
	   $User=M('User');
	   return $User->getFieldByNumber($id,'nickname');
	}
	public function getDeptnameById($id)
	{
	   $Dept=M('Dept');
	   return $Dept->getFieldById($id,'name');
	}
	public function getPostnameById($id)
	{
	   $role=M('role');
	   return $role->getFieldById($id,'name');
	}
	public function getPostlevelById($id)
	{
	   $role=M('role');
	   return $role->getFieldById($id,'level');
	}
	
	public function forbid() {
		$name = $this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		$id = $_REQUEST [$pk];
		$condition = array($pk => array('in', $id));
		if("admin"==$model->where($condition)->getField("account"))
		{
			$this->error('无法禁用管理员！');
		}
		$list = $model->forbid($condition);
		if ($list !== false) {
			//$this->assign("jumpUrl", Cookie::get('_currentUrl_'));
			$this->success('状态禁用成功');
		} else {
			$this->error('状态禁用失败！');
		}
	}
	
	public function foreverdelete() {
		//删除指定记录
		$name = $this->getActionName();
		$model = D($name);
		if (!empty($model)) {
			$pk = $model->getPk();
			$id = $_REQUEST [$pk];
			if (isset($id)) {
				$condition = array($pk => array('in', explode(',', $id)));
				
				$oldinfo=$model->where($condition)->find();
				
				if("admin"==$model->where($condition)->getField("account"))
				{
					$this->error('无法删除管理员！');
				}
				
				if (false !== $model->where($condition)->delete())
				{
					//echo $model->getlastsql();
					$logdata = array();
					$logdata['account']	=	$_POST['account'];
					$logdata['time']	=	time();
					$logdata['content']	=	$_SESSION["name"]."于".date("Y-m-d H:i:s")."删除账号".$oldinfo["account"];
					M("Logerr")->add($logdata);
					
					$this->success('删除成功！');
					//$this->redirect('index');
				} else {
					$this->error('删除失败！');
				}
			} else {
				$this->error('非法操作');
			}
		}
		$this->forward();
	}
	function resume() {
		//恢复指定记录
		$name = $this->getActionName();
		$model = D($name);
		$pk = $model->getPk();
		$id = $_GET [$pk];
		$condition = array($pk => array('in', $id));
		if (false !== $model->resume($condition)) {
			//$this->assign("jumpUrl", Cookie::get('_currentUrl_'));
			$this->success('状态恢复成功！');
		} else {
			$this->error('状态恢复失败！');
		}
	}
	
	function ajax(){
		$nickname=$_REQUEST[nickname];
		$numflag=preg_match('/\d/is', $nickname);
		$map[account]=$_REQUEST[account];		
		$user=M("user")->where($map)->find();
		$map1[number]=$_REQUEST[number];		
		$user1=M("user")->where($map1)->find();
		/*
		if(($_REQUEST[number]==0)||($_REQUEST[number]==""))
		{
			echo json_encode("编号不能为0或空");
		}		
		elseif(!is_numeric($_REQUEST[number]))
		{
			echo json_encode("编号必须为纯数字");
		}
		else*/if(empty($_REQUEST[nickname]))
		{
			echo json_encode("请填写姓名");
		}	
		elseif(!empty($numflag))
		{
			echo json_encode("姓名中不能包含数字");
		}			
		elseif(!empty($user))
		{
			echo json_encode('用户名已存在');
		}			
		elseif(!empty($user1))
		{
			echo json_encode('编号已存在');
		}
        else{
			echo 1;
		}		
	}
	
	function ajax1(){
		$nickname=$_REQUEST[nickname];
		$numflag=preg_match('/\d/is', $nickname);
		$map[id]=array("neq",$_REQUEST[id]);
		$map[account]=$_REQUEST[account];		
		$user=M("user")->where($map)->find();
		$map1[id]=array("neq",$_REQUEST[id]);
		$map1[number]=$_REQUEST[number];		
		$user1=M("user")->where($map1)->find();
		/*
		if(($_REQUEST[number]==0)||($_REQUEST[number]==""))
		{
			echo json_encode("编号不能为0或空");
		}		
		elseif(!is_numeric($_REQUEST[number]))
		{
			echo json_encode("编号必须为纯数字");
		}
		else*/if(empty($_REQUEST[nickname]))
		{
			echo json_encode("请填写姓名");
		}	
		elseif(!empty($numflag))
		{
			echo json_encode("姓名中不能包含数字");
		}			
		elseif(!empty($user))
		{
			echo json_encode('用户名已存在');
		}	
		elseif(!empty($user1))
		{
			echo json_encode('编号已存在');
		}
        else{
			echo 1;
		}		
	}
	
	function ajax2(){
		M("user")->where("id='".htmlspecialchars($_REQUEST[id])."'")->delete();
		echo json_encode(htmlspecialchars($_REQUEST[id]));
	}
	
	
	public function toexcel()
	{
		$model=M("User");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map[status]=1;
		$volist=$model->where($map)->order('id asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			
			
			$data[$i]['account']=$volist[$i]["account"];
			$data[$i]['password']=$volist[$i]["password"];
			$data[$i]['nickname']=$volist[$i]["nickname"];
			$data[$i]['number']=$volist[$i]["number"];
			$mapforDept[id]=$volist[$i]["department"];
			$data[$i]['department']=M("Dept")->where($mapforDept)->getField("name");
			$mapforRole[id]=$volist[$i]["position"];
			$data[$i]['position']=M("Role")->where($mapforRole)->getField("name");
			
			$data[$i]['tel']=$volist[$i]["tel"];
			$data[$i]['email']=$volist[$i]["email"];
		}
		
		$file="用户列表";
		$title="用户列表";
		$subtitle='用户列表';
		
		$th_array=array('用户名','密码','姓名','编号','部门','职位','电话','邮箱');
		
		//function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	
	public function toexcel1()
	{
		$model=M("User");
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
	
		$volist=$model->where($map)->order('id asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			
			$data[$i]['number']=$i+1;
			$data[$i]['intime']=$volist[$i]["intime"];
			$data[$i]['nickname']=$volist[$i]["nickname"];
		}
		
		$map[status]=0;
		$volist=$model->where($map)->order('id asc')->select();
		$number=count($volist);
		for($i=0;$i<$number;$i++)
		{
			
			$data[$i]['number']=$i+1;
			$data[$i]['outtime']=$volist[$i]["outtime"];
			$data[$i]['nickname1']=$volist[$i]["nickname"];
		}
		
		$file="人员信息变动表";
		$title="人员信息变动表";
		$subtitle='人员信息变动表';
		
		$th_array=array('序号','新增时间','新增人员','注销时间','注销人员');
		

		$this->createExel($file,$title,$subtitle,$th_array,$data,$file);
	}
	
	
	function createExel($file,$title,$subtitle,$array_th,$data,$excelname="")
	{
		Vendor ('Excel.PHPExcel');

		//创建一个读Excel模版的对象
		$objReader = PHPExcel_IOFactory::createReader ( 'Excel5' );
		$objPHPExcel = $objReader->load ("../Public/template/template.xls" );
		//获取当前活动的表
		$objActSheet = $objPHPExcel->getActiveSheet ();
		$objActSheet->setTitle ($title);
		
		$objActSheet->setCellValue ( 'A1', $title );
		$objActSheet->setCellValue ( 'A2', '导出时间：' . date ( 'Y-m-d H:i:s' ) );
		$objActSheet->setCellValue ( 'F2', $subtitle);
		
		if($array_th==null)
		{
			$array_th=array_keys($data[0]);
		}
	
		foreach($array_th as $key=>$value)
		{
			$objActSheet->getCellByColumnAndRow($key,4)->setValue($value);		
		}
		
		$baseRow = 5; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
		foreach ( $data as $r => $dataRow ) 
	    {
			$row = $baseRow + $r;
			//将数据填充到相对应的位置
			$arraykeys=array_keys($dataRow);//数组键值
			$keyscnt=count($arraykeys);
			foreach($arraykeys as $key=>$value)
			{		 
				$objPHPExcel->getActiveSheet ()->getCellByColumnAndRow($key,$row)->setValue($dataRow [$value]);
			}
			//$objPHPExcel->getActiveSheet()->getStyle('A'.$row.':T'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
		}
  
		//$filename = $file;
		$filename = $excelname."_".time();
		
		header ( 'Content-Type: application/vnd.ms-excel' );
		header ( 'Content-Disposition: attachment;filename="' . $filename . '.xls"' ); //"'.$filename.'.xls"
		header ( 'Cache-Control: max-age=0' );
		ob_clean();   
        flush(); 
		
		$objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' ); //在内存中准备一个excel2003文件
		$objWriter->save ( 'php://output' );

	}
	
	
	
	
	public function getexcel()
	{
		if(empty($_FILES["file"]["name"]))
		{
			$this->error("请上传文件！");
		}
		$file_name = explode(".",$_FILES["file"]["name"]);
		if(($_FILES["file"]["type"] == "application/vnd.ms-excel")||($_FILES["file"]["type"] == "application/octet-stream")||($_FILES["file"]["type"] == "application/kset"))
		{												
			header("Content-type: text/html; charset=utf-8");
			error_reporting(E_ALL ^ E_NOTICE);
			$Import_TmpFile = $_FILES['file']['tmp_name'];
			Vendor('Excelload.reader');  //导入thinkphp 中第三方插件库
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('UTF-8');
			$data->read($Import_TmpFile);
			$array =array();
			for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
			{
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) 
				{
					$array[$i][$j] = $data->sheets[0]['cells'][$i][$j];
				}
			}
			$num=count($array);
			$number=$num;
			$time=time();
			
			for($k=5;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					$mapforDept["name"]=$array[$k]['5'];
					$department=M("Dept")->where($mapforDept)->getField("id");
					if(empty($department))
					{
						$this->error($array[$k]['5']."部门不存在");
					}
					
					$mapforRole["name"]=array("eq",$array[$k]['6']);
					$position=M("Role")->where($mapforRole)->getField("id");
					if(empty($position))
					{
						$this->error($array[$k]['6']."职位不存在");
					}
				}
				
			}
	
			for($k=2;$k<=$number;$k++)
			{		
				if($array[$k]['1']=="")
				{
					continue;
				}
				else
				{
					$datasave[account]=$array[$k]['1'];
					$datasave[password]=md5($array[$k]['2']);
					$datasave[nickname]=$array[$k]['3'];
					$datasave[skin]=1;
					$datasave[status]=1;
					$datasave[number]=$array[$k]['4'];//编号
					
					
					$mapforDept["name"]=$array[$k]['5'];
					$department=M("Dept")->where($mapforDept)->getField("id");
					
					$mapforRole["name"]=array("eq",$array[$k]['6']);
					$position=M("Role")->where($mapforRole)->getField("id");
					
					
					if(!empty($array[$k]['7']))
					{
						$datasave[projecttype]=$array[$k]['7'];
					}
					else
					{
						$datasave[projecttype]="";
					}
					
					$datasave[position]=$position;
					$datasave[department]=$department;
					
					$datasave[create_time]=time();
					$result=M("User")->add($datasave);
				
					$this->addRole($result,$position);
					
				}					
			}
			
			$this->success('上传成功!');
		}
		else
		{
			$this->error('上传的文件类型非法!');
		}
	}
}
?>