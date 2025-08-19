<?php

class CommonAction extends Action {
	function send_success($msg,$data = ''){
       	 	$array = array("status"=>"OK","msg"=>$msg,"data"=>$data);
        		echo json_encode($array);
    	}
    	function send_error($msg){
        		$array = array("status"=>"ERROR","msg"=>$msg);
        		echo json_encode($array);
    	}
	function writelog($msg){
		$fd = fopen("C:\a.txt","a");
		$str = "[".date("Y-m-d h:i:s",time())."]".$msg;
		fwrite($fd, $str."\r\n");
		fclose($fd);
	}
	function getAllcities($flag)
	{
		if($flag)
		{
			$map["design_status"]=array("eq","施工中");
		}
		$map['_complex'] = $this->find5level_1($_SESSION[position],$map);
		
		$allprojects=M("Project")->where($map)->order("id desc")->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
	}
	
	function getAllprojects()
	{
		$map["design_status"]=array("neq","完成验收");
		
		if($_REQUEST["_URL_"][0]=="Second")
		{
			$map["step1"]=array("neq","1");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		else if($_REQUEST["_URL_"][0]=="Jypg")
		{
			//$map["step2"]=array("neq","1");
			
			$map["design_status"]=array("eq","初步立项审批通过");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			
			if($_SESSION["dept"]!="省公司")
			{
				$map["invester"]=array("in","自投资,合作投资");
			}
			else
			{
				$map["invester"]=array("in","省投资,合作投资");
				
			}
		}
		else if($_REQUEST["_URL_"][0]=="Jypg1")
		{
			//$map["step2"]=array("neq","1");
			$map["design_status"]=array("eq","可研编制文件审批通过");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			
			/* 20220804临时去掉测地市流程
			if($_SESSION["dept"]!="省公司")
			{
				$map["invester"]=array("in","自投资");
			}
			else
			{
				$map["invester"]=array("in","省投资,合作投资");
			}
			*/
		}
		else if($_REQUEST["_URL_"][0]=="Qdbook")
		{
			//$map["step3"]=array("neq","1");
		}
		else if($_REQUEST["_URL_"][0]=="Cqtx")
		{
			//$map["step4"]=array("neq","1");
			//$map["design_status"]=array("eq","可研编制文件审批通过");
			$map["design_status"]=array("eq","可研评审报告审批通过");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			
			if($_SESSION["dept"]!="省公司")
			{
				$map["invester"]=array("in","自投资,合作投资");
			}
			else
			{
				$map["invester"]=array("in","省投资,合作投资");
			}
		}
		else if($_REQUEST["_URL_"][0]=="Qdht")
		{
			//$map["step5"]=array("neq","1");
			$map["design_status"]=array("eq","招标审核通过");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			
			if($_SESSION["dept"]!="省公司")
			{
				$map["invester"]=array("in","自投资,合作投资");
			}
			else
			{
				$map["invester"]=array("in","省投资,合作投资");
			}
		}
		else if($_REQUEST["_URL_"][0]=="Secondpublish")
		{
			$map["step6"]=array("neq","1");
			//$map["department"]=array("eq",$_SESSION["dept"]);2021-08-02
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		else
		{
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
		}
		
		
		
		if($_SESSION["projecttype"])
		{
			$map["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		
		
		$allprojects=M("Project")->where($map)->order("id desc")->select();
		foreach($allprojects as $key => $val)
		{
			$allprojects[$key][value]=$val['title'];
		}
		$this->assign('allprojects',$allprojects);
	}
	function getAllworktypes()
	{
		$mapforWorktype[type]=1;
		$worktypes=M("Worktype")->where($mapforWorktype)->select();
		$this->assign('worktypes',$worktypes);
	}
	
	public function ajaxgetWorkers(){
        $mapforWorkers['plmid']=$_REQUEST["plmid"];
		$workers=M("Plmworker")->where($mapforWorkers)->order("id asc")->select();
		echo json_encode($workers);
    }
	
	
	public function ajaxgetWorkersByGroupId(){
        $mapforWorkers['groupid']=$_REQUEST["groupid"];
		$workers=M("Plmworker")->where($mapforWorkers)->order("id asc")->select();
		
		//判断是否已考勤
		foreach ($workers as $key => $val) 
		{
			$mapforPlmattendance["number"]=$val["number"];
			$mapforPlmattendance["date"]=$_REQUEST["date"];
			$mapforPlmattendance["status"]="在岗";
			$workers[$key][attendance]=M("Plmattendance")->where($mapforPlmattendance)->find();
			
			if($workers[$key][attendance]["status"]!="在岗")
			{
				$workers[$key][attendance]["status"]="离岗";
			}
		}
		
		echo json_encode($workers);
    }
	
	
    function _initialize() {
		
		ini_set('session.gc_maxlifetime',0);
		import('@.ORG.Util.Cookie');
		if($_REQUEST["moduletitle"])
		{
			$_SESSION["moduletitle"]=$_REQUEST["moduletitle"];
		}
		else
		{
			$_REQUEST["moduletitle"]=$_SESSION["moduletitle"];
		}
		if($_REQUEST["moduletitle1"])
		{
			$_REQUEST["moduletitle"]=$_REQUEST["moduletitle1"];
		}
		if($_GET[account])
		{
		
			$_SESSION['app']=1;
			$account=$_GET[account];
			$mapforUser[account]=$account;
			$userinfo=M("User")->where($mapforUser)->find();
			$_SESSION[C('USER_AUTH_KEY')]=$userinfo[id];
			$_SESSION["id"]=$userinfo[id];
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
			
			$_SESSION['projecttype']=$userinfo['projecttype'];
			$_SESSION['projecttype1']	=	$authInfo['projecttype1'];
			if(false!==strstr($_SESSION['role'],"项目经理"))
			{
				$_SESSION["role1"]="项目经理";
			}
			if((false!==strstr($_SESSION['role'],"市场开发"))&&(false!==strstr($_SESSION['role'],"经理")))
			{
				$_SESSION["role1"]="开发经理";
			}
			if((false!==strstr($_SESSION['role'],"商务"))&&(false!==strstr($_SESSION['role'],"经理")))
			{
				$_SESSION["role1"]="商务经理";
			}
			if(false!==strstr($_SESSION['role'],"技术总监"))
			{
				$_SESSION["role1"]="设计经理";
			}
			if((false!==strstr($_SESSION['role'],"供应链部"))&&(false!==strstr($_SESSION['role'],"经理")))
			{
				$_SESSION["role1"]="采购经理";
			}
			
		}
		$mapforNode[name]=MODULE_NAME;
		$nodecount=M("Node")->where($mapforNode)->count();
		if($nodecount>1)
		{
			$mapforNode[title]=$_REQUEST["moduletitle"];
		}
		$moduletitle=M("Node")->where($mapforNode)->getField("title");
		$this->assign('moduletitle',$moduletitle);
		$this->assign('moduletitle',$_REQUEST["moduletitle"]);
		
		$mapforAccess["role_id"]=$_SESSION["position"];
		$mapforAccess["node_id"]=M("Node")->where($mapforNode)->getField("id");
		$access=M("Access")->where($mapforAccess)->find();
		//dump($mapforAccess);
		$accesstype=$access[type];
		$accessapprove=$access[approve];
		$this->assign('accesstype',$accesstype);
		$this->assign('accessapprove',$accessapprove);
		$this->assign('hostip',$_SERVER['HTTP_HOST']);
		
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('@.ORG.Util.RBAC');
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    //redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
					if($_REQUEST["app"]=="1")
					{
						redirect("/projecttest/Rbac/App/login");
					}
					else
					{
						redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
					}
					
                }
                // 没有权限 抛出错误
                /*
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }*/
            }
        }
		
		$action_name=ACTION_NAME;
		if(($_SESSION[app])&&($action_name=="index"))
		{
			$mapforNode[name]=MODULE_NAME;
			$mapforNode[title]=$_SESSION["moduletitle"];
			$mapforAccess["node_id"]=M("Node")->where($mapforNode)->getField("id");
			$access=M("Access")->where($mapforAccess)->find();
			if(empty($access))
			{
				header('Content-Type: text/html; charset=utf-8');
				echo '<div style="font-size:32px;text-align:center;">您没有该项权限</div>';
				exit;
			}
		}
	
		$mapforModulelog[modulename]=MODULE_NAME;
		$mapforModulelog[account]=$_SESSION[account];
		$exist=M("Modulelog")->where($mapforModulelog)->find();
		if($exist)
			M("Modulelog")->where($mapforModulelog)->setInc("number");
		else
		{
			$module[moduletitle]=$moduletitle;
			$module[modulename]=MODULE_NAME;
			$module[account]=$_SESSION[account];
			$module[number]=1;
			M("Modulelog")->add($module);
		}
		
    }

    public function index() {
        //列表过滤器，生成查询Map对象
    	//$this->getnumber();
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
		if($_SESSION[skin]!=3)
		{
        	$this->display(indexoa);
		}
		else
		{
			$this->display();
		}
        return;
    }

    /**
      +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        //$name = $this->getActionName();
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
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
			if (!empty($_REQUEST ['all'])) {
				$listRows=200;
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
            foreach ($voList as $vokey => $voval) {
            	$voList[$vokey][urltitle]=urlencode($voList[$vokey][title]);
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
        return;
    }
    function insert() 
	{
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
            /////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }

    public function add() 
	{
    	if($_SESSION[skin]!=3)
    	{
    		$this->display(addoa);
    	}
    	else
    	{
        $this->display();
    	}
    }
	
    function read() 
	{
        $this->edit();
    }

    function edit() 
	{
        $name = $this->getActionName();
        $model = M($name);
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->getById($id);
        $this->assign('vo', $vo);
        if($_SESSION[skin]!=3)
        {
        	$this->display(editoa);
        }
        else
        {
        $this->display();
        }
    }

    function update() 
	{
        //B('FilterString');
        $name = $this->getActionName();
        $model = D($name);
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            /////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->redirect('index');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
	
	public function choice() 
	{
		$key = $_REQUEST ["key"];
		$this->assign('key',$key);
    	$this->display();
    }
	
    /**
      +----------------------------------------------------------
     * 默认删除操作
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public function delete() 
	{
        //删除指定记录
        $name = $this->getActionName();
        $model = M($name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST [$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('status', - 1);
                if ($list !== false) {
                    $this->success('删除成功！');
                } else {
                    $this->error('删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }

    public function deleteapp() 
	{
    	//删除指定记录
    	$name = $this->getActionName();
    	$model = M($name);
    	if (!empty($model)) {
    		$pk = $model->getPk();
    		$id = $_REQUEST [$pk];
    		if (isset($id)) {
    			$condition = array($pk => array('in', explode(',', $id)));
    			$list = $model->where($condition)->setField('status', 0);
    			if ($list !== false) {
    				$this->success('禁用成功！');
    			} else {
    				$this->error('禁用失败！');
    			}
    		} else {
    			$this->error('非法操作');
    		}
    	}
    }
    
    public function foreverdelete() {
        //删除指定记录
        $name = $this->getActionName();
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
                    //echo $model->getlastsql();
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

    public function clear() {
        //删除指定记录
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            if (false !== $model->where('status=1')->delete()) {
                $this->assign("jumpUrl", $this->getReturnUrl());
                $this->success(L('_DELETE_SUCCESS_'));
            } else {
                $this->error(L('_DELETE_FAIL_'));
            }
        }
        $this->forward();
    }

    /**
      +----------------------------------------------------------
     * 默认禁用操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    public function forbid() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_REQUEST [$pk];
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition);
        if ($list !== false) {
            $this->assign("jumpUrl", Cookie::get('_currentUrl_'));
            $this->success('状态禁用成功');
        } else {
            $this->error('状态禁用失败！');
        }
    }

    public function checkPass() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->checkPass($condition)) {
            $this->assign("jumpUrl", $this->getReturnUrl());
            $this->success('状态批准成功！');
        } else {
            $this->error('状态批准失败！');
        }
    }

    public function recycle() {
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->recycle($condition)) {

            $this->assign("jumpUrl", Cookie::get('_currentUrl_'));
            $this->success('状态还原成功！');
        } else {
            $this->error('状态还原失败！');
        }
    }

    public function recycleBin() {
        $map = $this->_search();
        $map ['status'] = - 1;
        $name = $this->getActionName();
        $model = D($name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
    }

    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        //恢复指定记录
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition)) {
            $this->assign("jumpUrl", Cookie::get('_currentUrl_'));

            $this->success('状态恢复成功！');
        } else {
            $this->error('状态恢复失败！');
        }
    }
    function movetomore() {
    	//恢复指定记录
    	$name = $this->getActionName();
    	$model = D($name);
    	$pk = $model->getPk();
    	$id = $_GET [$pk];
    	//$condition = array($pk => array('in', $id));
    	$data['more']=1;
    	$data['id']=$id;
    	if (false !== $model->save($data)) {
    		$this->assign("jumpUrl", Cookie::get('_currentUrl_'));
    		$this->success('移动到more成功！');
    	} else {
    		$this->error('移动到more失败！');
    	}
    }
    function movefrommore() {
    	//恢复指定记录
    	$name = $this->getActionName();
    	$model = D($name);
    	$pk = $model->getPk();
    	$id = $_GET [$pk];
    	//$condition = array($pk => array('in', $id));
    	$data['id']=$id;
    	$data['more']=0;
    	if (false !== $model->save($data)) {
    		$this->assign("jumpUrl", Cookie::get('_currentUrl_'));
    
    		$this->success('移动到more成功！');
    	} else {
    		$this->error('移动到more失败！');
    	}
    }
	public function findpositioniframe() 
	{	$lat=$_REQUEST[lat];
	    $lng=$_REQUEST[lng];
		$this->assign('lat', $lat);
		$this->assign('lng', $lng);
		$this->display("../Secondcheck/findpositioniframe");
	}
	
    /*find my department*/
    public function finddept($id)
    {
	    $modeldept = D("Dept");
	    $mapdept['id'] = $id;
	    $userdept=$modeldept->where($mapdept)->select();
	    return $userdept[0];
    }
    /*find my position*/
    public function findposition($id)
    {
    	$modelrole = D("Role");
    	$maprole['id'] = $id;
    	$userrole=$modelrole->where($maprole)->select();
    	return $userrole[0];
    }
    
	public function findUserByAccount($account)
    {
		$mapuser['account']=$account;
		$mapuser['status']=1;
		$user=M("User")->where($mapuser)->find();
    	return $user;
    }
	public function findUserByName($name)
    {
		$mapuser['nickname']=$name;
		$mapuser['status']=1;
		$user=M("User")->where($mapuser)->find();
    	return $user;
    }
	public function findUserByRole($role,$projecttype)//同一部门的
    {
    	$maprole['name'] = array("like","%".$role."%");
		$positions=M("Role")->where($maprole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
    	$mapuser['position']=array("in",$pline);
		$mapuser['department']=$_SESSION["department"];
		$mapuser['status']=1;
		
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
		}
		
		$user=M("User")->where($mapuser)->find();
    	return $user;
    }
	
	
	public function findUserByRolewithoutdept($role,$dept)
    {
    	$maprole['name'] = array("like","%".$role."%");
		$positions=M("Role")->where($maprole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
    	$mapuser['position']=array("in",$pline);
		if($dept)
			$mapuser['department']=$dept;
		$mapuser['status']=1;
		$user=M("User")->where($mapuser)->find();
    	return $user;
    }
	
	public function findNumberByNameAndRole($name,$role)
    {
		/*
    	$maprole['name'] = array("like","%".$role."%");
		$positions=M("Role")->where($maprole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
    	$mapuser['position']=array("in",$pline);
		*/
		$mapuser['nickname']=$name;
		$user=M("User")->where($mapuser)->find();
    	return $user[number];
    }
	
	
	
	
	
	
	
	
	
	
	public function findleaderbyrole($role,$projecttype,$city)
    {
    	$maprole['name'] = array("like","%".$role."%");
		$positions=M("Role")->where($maprole)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
    	$mapuser['position']=array("in",$pline);
		$mapuser['status']=1;
		
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			
		}
		if($city)
		{
			$mapuser['city']=array("like","%".$city."%");
		}
		
		$usercount=M("User")->where($mapuser)->count();
		if($usercount>1)
		{
			$mapuser1['position']=array("in",$pline);
			$mapuser1['status']=1;
			$mapuser1["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			if($city)
			{
				$mapuser1['city']=array("like","%".$city."%");
			}
			$mapuser1['main']="是";
			$user=M("User")->where($mapuser1)->getField("nickname");
			if(empty($user))
			{
				$user=M("User")->where($mapuser)->getField("nickname");
			}
		}
		else
		{
			$user=M("User")->where($mapuser)->getField("nickname");
		}
		
    	return $user;
    }
	
	
	public function finduserleader($user,$projecttype,$city)
    {
		$mapforUser["nickname"]=$user;
		$userinfo=M("User")->where($mapforUser)->find();
    	$maprole['id'] = array("eq",$userinfo["position"]);
		$leaderrole=M("Role")->where($maprole)->getField("pid");
		
    	$mapuser['position']=array("eq",$leaderrole);
		$mapuser['account']=array("neq","admin");
		$mapuser['status']=1;
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
		}
		if($city)
		{
			$mapuser['city']=array("like","%".$city."%");
		}
		
		$usercount=M("User")->where($mapuser)->count();
		if($usercount>1)
		{
			$mapuser1['position']=array("eq",$leaderrole);
			$mapuser1['account']=array("neq","admin");
			$mapuser1['status']=1;
			$mapuser1["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			if($city)
			{
				$mapuser1['city']=array("like","%".$city."%");
			}
			$mapuser1['main']="是";
			$user=M("User")->where($mapuser1)->getField("nickname");
			if(empty($user))
			{
				$user=M("User")->where($mapuser)->getField("nickname");
			}
		}
		else
		{
			$user=M("User")->where($mapuser)->getField("nickname");
		}
    	return $user;
    }
	
	public function findmyleader($projecttype,$city)
    {
    	$maprole['id'] = array("eq",$_SESSION["position"]);
		$leaderrole=M("Role")->where($maprole)->getField("pid");
		
    	$mapuser['position']=array("eq",$leaderrole);
		$mapuser['account']=array("neq","admin");
		$mapuser['status']=1;
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
		}
		if($city)
		{
			$mapuser['city']=array("like","%".$city."%");
		}
		
		$usercount=M("User")->where($mapuser)->count();
		if($usercount>1)
		{
			$mapuser1['position']=array("eq",$leaderrole);
			$mapuser1['account']=array("neq","admin");
			$mapuser1['status']=1;
			$mapuser1["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			if($city)
			{
				$mapuser1['city']=array("like","%".$city."%");
			}
			$mapuser1['main']="是";
			$user=M("User")->where($mapuser1)->getField("nickname");
			if(empty($user))
			{
				$user=M("User")->where($mapuser)->getField("nickname");
			}
		}
		else
		{
			$user=M("User")->where($mapuser)->getField("nickname");
		}
    	return $user;
    }
	
	
	
	
	
	
	
	
	public function findleader($projecttype,$city)
    {
    	$maprole['id'] = array("eq",$_SESSION["position"]);
		$leaderrole=M("Role")->where($maprole)->getField("pid");
		
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			$mapuser1["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
		}
		if($city)
		{
			$mapuser['city']=array("like","%".$city."%");
		}
		
		
    	$mapuser['position']=array("eq",$leaderrole);
		$mapuser['account']=array("neq","admin");
		$mapuser['status']=1;
		$mapuser1['position']=array("eq",$leaderrole);
		$mapuser1['account']=array("neq","admin");
		$mapuser1['status']=1;
		$projecttype=str_replace("建设","",$projecttype);
		
		
		$usercount=M("User")->where($mapuser)->count();
		if($usercount>1)
		{
			$mapuser1['main']="是";
			$user=M("User")->where($mapuser1)->find();
			if(empty($user))
			{
				$user=M("User")->where($mapuser)->find();
			}
		}
		else
		{
			$user=M("User")->where($mapuser)->find();
		}
		
		
    	return $user;
    }
	
	
	public function findleaderbyroleid($roleid,$projecttype,$city)
    {
    	$mapuser['position']=array("in",$roleid);
		$mapuser['status']=1;
		$mapuser['account']=array("neq","admin");
		if($projecttype)
		{
			$mapuser["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
		}
		if($city)
		{
			$mapuser['city']=array("like","%".$city."%");
		}
		
		$usercount=M("User")->where($mapuser)->count();
		if($usercount>1)
		{
			$mapuser1['account']=array("neq","admin");
			$mapuser1['position']=array("in",$roleid);
			$mapuser1['status']=1;
			if($projecttype)
			{
				$mapuser1["projecttype"]=array(array("like","%".$projecttype."%"),array("eq",""),"or");
			}
			if($city)
			{
				$mapuser1['city']=array("like","%".$city."%");
			}
			$mapuser1['main']="是";
			$user=M("User")->where($mapuser1)->find();
			if(empty($user))
			{
				$user=M("User")->where($mapuser)->find();
			}
		}
		else
		{
			$user=M("User")->where($mapuser)->find();
		}
		
    	return $user;
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	function find5level($roleid,&$map1)
	{
		if($_SESSION["projecttype"])
		{
			$map1["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		$actionname=$this->getActionName();
		$mapforNode["name"]=$actionname;
		$mapforNode["status"]=1;
		$alldata=M("Node")->where($mapforNode)->getField("alldata");
		if((($_SESSION[account]=="admin")||(!empty($alldata))||($_SESSION[datapower]=="全部数据")))
		{
			$where["name"]=array("like","%%");
			$where["user"]=array("like","%%");
			$where["ysuser"]=array("like","%%");
			$where["charge"]=array("like","%%");
			$where["director"]=array("like","%%");
			$where["projectmanager"]=array("like","%%");
			$where["xiaoshouuser"]=array("like","%%");
			$where["yanjiuuser"]=array("like","%%");
			$where["baojiauser"]=array("like","%%");
			$where["toubiaouser"]=array("like","%%");
			$where["hetonguser"]=array("like","%%");
			$where["shigonguser"]=array("like","%%");
			$where["fileuser"]=array("like","%%");
			$where['_logic'] = 'or';
			return $where;
		}
		
		//$roleids=$roleid.",";
		if(false!==strstr($_SESSION["role"],"职员"))
		{
			//$roleids=$roleid.",";
		}
		
		
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$mapusers[position] = array("in",$roleids);
		$users=M("User")->where($mapusers)->field("nickname")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].",";
		}
		$subordinates.=$_SESSION[name];
		$where["projectmanager"]=array("in",$subordinates);
		$where["kaifa"]=array("in",$subordinates);
		$where["kaifauser"]=array("in",$subordinates);
		$where["sheji"]=array("in",$subordinates);
		$where["shejiuser"]=array("in",$subordinates);
		$where["caigou"]=array("in",$subordinates);
		$where["caigouuser"]=array("in",$subordinates);
		$where["gongcheng"]=array("in",$subordinates);
		$where["gongchenguser"]=array("in",$subordinates);
		$where["shangwu"]=array("in",$subordinates);
		$where["shangwuuser"]=array("in",$subordinates);
		$where["fileuser"]=array("in",$subordinates);
		//$where["name"]=array("in",$subordinates);
		//$where["user"]=array("in",$subordinates);
		//$where["ysuser"]=array("in",$subordinates);
		//$where["charge"]=array("in",$subordinates);
		//$where["director"]=array("in",$subordinates);
		//$where["xiaoshouuser"]=array("in",$subordinates);
		//$where["yanjiuuser"]=array("in",$subordinates);
		//$where["baojiauser"]=array("in",$subordinates);
		//$where["toubiaouser"]=array("in",$subordinates);
		//$where["hetonguser"]=array("in",$subordinates);
		//$where["shigonguser"]=array("in",$subordinates);
		
		$where['_logic'] = 'or';
		
		
		
		return $where;
	}	
	
	
	function find5level_1($roleid,&$map1)
	{
		if($_SESSION["projecttype"])
		{
			$map1["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		
		$actionname=$this->getActionName();
		$mapforNode["name"]=$actionname;
		$mapforNode["status"]=1;
		$alldata=M("Node")->where($mapforNode)->getField("alldata");
		
		if((($_SESSION[account]=="admin")||($_SESSION[datapower]=="全部数据")||(!empty($alldata))))
		{
			$where["name"]=array("like","%%");
			$where["user"]=array("like","%%");
			$where["ysuser"]=array("like","%%");
			$where["charge"]=array("like","%%");
			$where["director"]=array("like","%%");
			$where["projectmanager"]=array("like","%%");
			$where["xiaoshouuser"]=array("like","%%");
			$where["yanjiuuser"]=array("like","%%");
			$where["baojiauser"]=array("like","%%");
			$where["toubiaouser"]=array("like","%%");
			$where["hetonguser"]=array("like","%%");
			$where["shigonguser"]=array("like","%%");
			$where['_logic'] = 'or';
			return $where;
		}
		
		//$roleids=$roleid.",";
		if(false!==strstr($_SESSION["role"],"职员"))
		{
			//$roleids=$roleid.",";
		}
		
		
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$mapusers[position] = array("in",$roleids);
		$users=M("User")->where($mapusers)->field("nickname")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].",";
		}
		$subordinates.=$_SESSION[name];
		//dump($subordinates);
		$where["projectmanager"]=array("in",$subordinates);
		$where["kaifa"]=array("in",$subordinates);
		$where["kaifauser"]=array("in",$subordinates);
		$where["sheji"]=array("in",$subordinates);
		$where["shejiuser"]=array("in",$subordinates);
		$where["caigou"]=array("in",$subordinates);
		$where["caigouuser"]=array("in",$subordinates);
		$where["gongcheng"]=array("in",$subordinates);
		$where["gongchenguser"]=array("in",$subordinates);
		$where["shangwu"]=array("in",$subordinates);
		$where["shangwuuser"]=array("in",$subordinates);
		
		$where["name"]=array("in",$subordinates);
		$where["user"]=array("in",$subordinates);
		$where["ysuser"]=array("in",$subordinates);
		$where["charge"]=array("in",$subordinates);
		$where["director"]=array("in",$subordinates);
		$where["xiaoshouuser"]=array("in",$subordinates);
		$where["yanjiuuser"]=array("in",$subordinates);
		$where["baojiauser"]=array("in",$subordinates);
		$where["toubiaouser"]=array("in",$subordinates);
		$where["hetonguser"]=array("in",$subordinates);
		$where["shigonguser"]=array("in",$subordinates);
		$where['_logic'] = 'or';
		
		
		
		return $where;
	}	
	
	function find5levelusers($roleid)
	{
		if($_SESSION[account]=="admin")
		{
			$users=M("User")->field("nickname")->select();
			foreach($users as $key=>$val)
			{
				$subordinates.=$val[nickname].",";
			}
			return $subordinates;
		}
		
		//$roleids=$roleid.",";//平级的不能看
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$mapusers[position] = array("in",$roleids);
		$users=M("User")->where($mapusers)->field("nickname")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].",";
		}
		/*
		$where["charge"]=array("in",$subordinates);
		$where["user"]=array("in",$subordinates);
		$where["designer"]=array("in",$subordinates);
		$where["designmanager"]=array("in",$subordinates);
		$where["projectmanager"]=array("in",$subordinates);
		$where["supervisor"]=array("in",$subordinates);
		$where["engineeringmanage"]=array("in",$subordinates);
		$where["caiwu"]=array("in",$subordinates);
		$where["budget_user"]=array("in",$subordinates);
		$where["drawing_user"]=array("in",$subordinates);
		$where['_logic'] = 'or';*/
		$subordinates.=$_SESSION[name].",";
		return $subordinates;
	}	
	
	function find5leveluserswithnumber($roleid)
	{
		$roleids=$roleid.",";
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$mapusers[position] = array("in",$roleids);
		$users=M("User")->where($mapusers)->field("nickname,number")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].$val[number].",";
		}
		/*
		$where["charge"]=array("in",$subordinates);
		$where["user"]=array("in",$subordinates);
		$where["designer"]=array("in",$subordinates);
		$where["designmanager"]=array("in",$subordinates);
		$where["projectmanager"]=array("in",$subordinates);
		$where["supervisor"]=array("in",$subordinates);
		$where["engineeringmanage"]=array("in",$subordinates);
		$where["caiwu"]=array("in",$subordinates);
		$where["budget_user"]=array("in",$subordinates);
		$where["drawing_user"]=array("in",$subordinates);
		$where['_logic'] = 'or';*/
		return $subordinates;
	}	
	
	public function findProjectusers($projectid,$classify)
    {
		
    	$userarray=M("Project")->where("id=".$projectid)->field("gongcheng,kaifa,sheji,caigou,shangwu,gongchenguser,kaifauser,shejiuser,caigouuser,shangwuuser")->find();
		
		if(false!==strstr($classify,"主项"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongchenguser")->find();
		}
    	if(false!==strstr($classify,"开发"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("kaifauser")->find();
		}
		if(false!==strstr($classify,"设计"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("shejiuser")->find();
		}
		if(false!==strstr($classify,"采购"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("caigouuser")->find();
		}
		if(false!==strstr($classify,"施工"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongchenguser")->find();
		}
		if(false!==strstr($classify,"商务"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("shangwuuser")->find();
		}
		
		
		foreach($userarray as $key=>$val)
		{
			$userstr.=$val.",";
		}
		//$userstr.="管理员,";
		$mapforUser["nickname"]=array("in",$userstr);
		$users=M("User")->where($mapforUser)->field("nickname,number")->select();
		foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].$val[number].",";
		}
    	return $subordinates;
    }
	
	public function findProjectuser($projectid,$classify)
    {
		
		if(false!==strstr($classify,"主项"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongchenguser")->find();
		}
    	if(false!==strstr($classify,"开发"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("kaifauser")->find();
		}
		if(false!==strstr($classify,"设计"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("shejiuser")->find();
		}
		if(false!==strstr($classify,"采购"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("caigouuser")->find();
		}
		if(false!==strstr($classify,"施工"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongchenguser")->find();
		}
		if(false!==strstr($classify,"商务"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("shangwuuser")->find();
		}
		
		foreach($userarray as $key=>$val)
		{
			$userstr.=$val.",";
		}
		//$userstr.="管理员,";
		$mapforUser["nickname"]=array("in",$userstr);
		$userinfo=M("User")->where($mapforUser)->field("nickname,number")->find();
    	return $userinfo;
    }
	public function findProjectleaders($projectid,$classify)
    {
		$userarray=M("Project")->where("id=".$projectid)->field("gongcheng,kaifa,sheji,caigou,shangwu,gongchenguser,kaifauser,shejiuser,caigouuser,shangwuuser")->find();
		
		foreach($userarray as $key=>$val)
		{
			$userstr.=$val.",";
		}
		//$userstr.="管理员,";
		$mapforUser["nickname"]=array("in",$userstr);
		$users=M("User")->where($mapforUser)->field("nickname,number")->select();
    	foreach($users as $key=>$val)
		{
			$subordinates.=$val[nickname].$val[number].",";
		}
    	return $subordinates;
    }
	public function findProjectleader($projectid,$classify)
    {
		if(false!==strstr($classify,"主项"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongcheng")->find();
		}
    	if(false!==strstr($classify,"开发"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("kaifa")->find();
		}
		if(false!==strstr($classify,"设计"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("sheji")->find();
		}
		if(false!==strstr($classify,"采购"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("caigou")->find();
		}
		if(false!==strstr($classify,"施工"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("gongcheng")->find();
		}
		if(false!==strstr($classify,"商务"))
		{
			$userarray=M("Project")->where("id=".$projectid)->field("shangwuuser")->find();
		}
		
		foreach($userarray as $key=>$val)
		{
			$userstr.=$val.",";
		}
		//$userstr.="管理员,";
		$mapforUser["nickname"]=array("in",$userstr);
		$userinfo=M("User")->where($mapforUser)->field("nickname,number")->find();
    	return $userinfo;
    }
	function getfirstchar($s0){    
		$fchar = ord($s0{0});  
		if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});  
		$s1 = iconv("UTF-8","gb2312//IGNORE", $s0);  
		$s2 = iconv("gb2312","UTF-8//IGNORE", $s1);  
		//$s1 = get_encoding($s0,'GB2312');
		//$s2 = get_encoding($s1,'UTF-8');
		if($s2 == $s0){$s = $s1;}else{$s = $s0;}  
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;  
		if($asc >= -20319 and $asc <= -20284) return "A";  
		if($asc >= -20283 and $asc <= -19776) return "B";  
		if($asc >= -19775 and $asc <= -19219) return "C";  
		if($asc >= -19218 and $asc <= -18711) return "D";  
		if($asc >= -18710 and $asc <= -18527) return "E";  
		if($asc >= -18526 and $asc <= -18240) return "F";  
		if($asc >= -18239 and $asc <= -17923) return "G";  
		if($asc >= -17922 and $asc <= -17418) return "I";  
		if($asc >= -17417 and $asc <= -16475) return "J";  
		if($asc >= -16474 and $asc <= -16213) return "K";  
		if($asc >= -16212 and $asc <= -15641) return "L";  
		if($asc >= -15640 and $asc <= -15166) return "M";  
		if($asc >= -15165 and $asc <= -14923) return "N";  
		if($asc >= -14922 and $asc <= -14915) return "O";  
		if($asc >= -14914 and $asc <= -14631) return "P";  
		if($asc >= -14630 and $asc <= -14150) return "Q";  
		if($asc >= -14149 and $asc <= -14091) return "R";  
		if($asc >= -14090 and $asc <= -13319) return "S";  
		if($asc >= -13318 and $asc <= -12839) return "T";  
		if($asc >= -12838 and $asc <= -12557) return "W";  
		if($asc >= -12556 and $asc <= -11848) return "X";  
		if($asc >= -11847 and $asc <= -11056) return "Y";  
		if($asc >= -11055 and $asc <= -10247) return "Z";  
		return null;  
	}  
	/** 
	 * @name: get_encoding 
	 * @description: 自动检测内容编码进行转换 
	 * @param: string data 
	 * @param: string to  目标编码 
	 * @return: string 
	**/  
	function get_encoding($data,$to){  
		$encode_arr=array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');   
		$encoded=mb_detect_encoding($data, $encode_arr);   
		$data = mb_convert_encoding($data,$to,$encoded);   
		return $data;  
	}   
	   
	function pinyin1($zh){  
		$ret = "";  
		$s1 = iconv("UTF-8","gb2312", $zh);  
		$s2 = iconv("gb2312","UTF-8", $s1);  
		if($s2 == $zh){$zh = $s1;}  
		for($i = 0; $i < strlen($zh); $i++){  
			$s1 = substr($zh,$i,1);  
			$p = ord($s1);  
			if($p > 160){  
				$s2 = substr($zh,$i++,2);  
				$ret .= $this->getfirstchar($s2);  
			}else{  
				$ret .= $s1;  
			}  
		}  
		return $ret;  
	}  
	
	function find5leveldesigners($roleid)
	{
		$roleids=$roleid.",";
		$map['pid']=$roleid;
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}	
		}
		$map['pid']=array("in",$pline.'xxxxx');
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			if($pval)
			{
				$pline.=$pval[id].",";
				$roleids.=$pval[id].",";
			}
		}
		//$mapusers[position] = array("in",$roleids);
		
		
		/*查找所有设计师*/ 
		$mapfordesigner['name']=array("like","%设计师%");
		$mapfordesigner['id']=array("in",$roleids);
		$positions=M("Role")->where($mapfordesigner)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		
		$mapuser["position"]=array("in",$pline);
		$mapuser[status]=1;
		$shejishi=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
		foreach($shejishi as $key=>$val)
		{
			$shejishi[$key][pinyin]=$this->pinyin1($val[nickname]);
		}
		$this->assign('shejishi', $shejishi);
		$this->assign('shejishitel', $shejishi);
		return;
	}	
	
	function findRelativePersons()
	{
		/*查找所有设计师*/ 
		$map['name']=array("like","%设计%");
		$positions=M("Role")->where($map)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuser["position"]=array("in",$pline);
		$mapuser[status]=1;
		$shejishi=M("User")->where($mapuser)->order("nickname asc")->field("nickname,tel")->select();
		foreach($shejishi as $key=>$val)
		{
			$shejishi[$key][pinyin]=$this->pinyin1($val[nickname]);
		}
		$this->assign('shejishi', $shejishi);
		$this->assign('shejishitel', $shejishi);
		
		$mapmanager['name']=array("like","%设计部经理%");
		$positions=M("Role")->where($mapmanager)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusermanager["position"]=array("in",$pline);
		$mapusermanager[status]=1;
		$shejibujingli=M("User")->where($mapusermanager)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('shejibujingli', $shejibujingli);
		$this->assign('shejibujinglitel', $shejibujingli);
		
		
		
		$mapxiangmujingli['name']=array("like","%项目经理%");
		$positions=M("Role")->where($mapxiangmujingli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiangmujingli["position"]=array("in",$pline);
		$mapuserxiangmujingli[status]=1;
		$xiangmujingli=M("User")->where($mapuserxiangmujingli)->order("nickname asc")->field("nickname,tel")->select();
		foreach($xiangmujingli as $key=>$val)
		{
			$xiangmujingli[$key][pinyin]=$this->pinyin1($val[nickname]);
		}
		
		
		
		$this->assign('xiangmujingli', $xiangmujingli);
		$this->assign('manager', $xiangmujingli);
		$this->assign('xiangmujinglitel', $xiangmujingli);
		
		
		$mapjianli['name']=array("like","%监理%");
		$positions=M("Role")->where($mapjianli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserjianli["position"]=array("in",$pline);
		$mapuserjianli[status]=1;
		$jianli=M("User")->where($mapuserjianli)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('jianli', $jianli);
		$this->assign('jianlitel', $jianli);
		
		
		$mapgongchengjingli['name']=array("like","%施工专责%");
		$positions=M("Role")->where($mapgongchengjingli)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusergongchengjingli["position"]=array("in",$pline);
		$mapusergongchengjingli[status]=1;
		$gongchengshi=M("User")->where($mapusergongchengjingli)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('gongchengshi', $gongchengshi);
		
		
		
		$mapcaiwu['name']=array("like","%班长%");
		$positions=M("Role")->where($mapcaiwu)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusercaiwu["position"]=array("in",$pline);
		$mapusercaiwu[status]=1;
		$banzhang=M("User")->where($mapusercaiwu)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('banzhang', $banzhang);
		
		
		$mapxiaoguotu['name']=array("like","%公司专责%");
		$positions=M("Role")->where($mapxiaoguotu)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiaoguotushi["position"]=array("in",$pline);
		$mapuserxiaoguotushi[status]=1;
		$xianchangfuzeren=M("User")->where($mapuserxiaoguotushi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('xianchangfuzeren', $xianchangfuzeren);
		
		
		
		$mapyusuan['name']=array("like","%预算%");
		$positions=M("Role")->where($mapyusuan)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuseryusuan["position"]=array("in",$pline);
		$mapuseryusuan[status]=1;
		$yusuan=M("User")->where($mapuseryusuan)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('yusuan', $yusuan);
		
		
		
		$mapranqi['name']=array("like","%燃气%");
		$positions=M("Role")->where($mapranqi)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserranqi["position"]=array("in",$pline);
		$mapuserranqi[status]=1;
		$ranqi=M("User")->where($mapuserranqi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('ranqi', $ranqi);
		
		$mapxiaofang['name']=array("like","%消防%");
		$positions=M("Role")->where($mapxiaofang)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserxiaofang["position"]=array("in",$pline);
		$mapuserxiaofang[status]=1;
		$xiaofang=M("User")->where($mapuserxiaofang)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('xiaofang', $xiaofang);
		
		
		$mapruodian['name']=array("like","%弱电%");
		$positions=M("Role")->where($mapruodian)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserruodian["position"]=array("in",$pline);
		$mapuserruodian[status]=1;
		$ruodian=M("User")->where($mapuserruodian)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('ruodian', $ruodian);
		
		
		$mapdaiban['name']=array("like","%带班%");
		$positions=M("Role")->where($mapdaiban)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuserdaiban["position"]=array("in",$pline);
		$mapuserdaiban[status]=1;
		$daiban=M("User")->where($mapuserdaiban)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('daiban', $daiban);
		
		
		$mapcailiao['name']=array("like","%材料%");
		$positions=M("Role")->where($mapcailiao)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusercailiao["position"]=array("in",$pline);
		$mapusercailiao[status]=1;
		$cailiao=M("User")->where($mapusercailiao)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('cailiao', $cailiao);
		
		
		$mapshichang['name']=array("like","%市场%");
		$positions=M("Role")->where($mapshichang)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusershichang["position"]=array("in",$pline);
		$mapusershichang[status]=1;
		$shichang=M("User")->where($mapusershichang)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('shichang', $shichang);
		
		
		$mapyongchi['name']=array("like","%泳池%");
		$positions=M("Role")->where($mapyongchi)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapuseryongchi["position"]=array("in",$pline);
		$mapuseryongchi[status]=1;
		$yongchi=M("User")->where($mapuseryongchi)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('yongchi', $yongchi);
		
		
		$mapgongcheng['name']=array("like","%工程负责人%");
		$positions=M("Role")->where($mapgongcheng)->field("id")->select();
		$pline="";
		foreach($positions as $pkey=>$pval)
		{
			$pline.=$pval[id].",";
		}
		$mapusergongcheng["position"]=array("in",$pline);
		$mapusergongcheng[status]=1;
		$gongcheng=M("User")->where($mapusergongcheng)->order("nickname asc")->field("nickname,tel")->select();
		$this->assign('gongcheng', $gongcheng);
		
	}
	
    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $name = $this->getActionName();
            $model = D($name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                //if (!$result) {
                //    break;
                //}
            }
            
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }
    
    public function select()
    {
    	
    	//$map = $this->_search();
    	//创建数据对象
    	$Group = D('User');
    	//查找满足条件的列表数据
    	$user    = $Group->field('id,nickname,number,department,position')->select();
    	foreach ($user as $key=>$val)
    	{
    		$user[$key][namenumber]=$val[department].'.'.$val[nickname].$val[number].'.'.$val[position];
    		$userdept.=$user[$key][namenumber].',';
    	}
    	$this->assign('user',$user);
    	
    	//$userdept='a.b';
    	$this->assign('userdept',$userdept);
    	 
    	$Group = D('Dept');
    	//查找满足条件的列表数据
    	$dept     = $Group->field('id,name')->select();
    	$this->assign('dept',$dept);
    	 
    	$Group = D('Role');
    	//查找满足条件的列表数据
    	$role     = $Group->field('id,name')->select();
    	$this->assign('role',$role);
    	
    	$this->display();
    	
    	return;
    }
    
    
	
	
	
    /*
     $data['content'] ="邮件内容";
	 $data['receiver']="收件人,格式：管理员12,员工13,";
	 $data['sender']="发件人,格式：管理员12";
	 $data['title'] ="标题";
    */
    public function Sendmail($data,$outmailflag)
    {
    	$data['create_time']=time();
    	$data['commit_time']='0';
    	$data['update_time']='0';
    	$data['status']=1;
    	$nameemail = "Sendmail";
    	$modelemail = D($nameemail);
		
		$receiver=$data['receiver'];
		$receiverarray=explode(",",$receiver);
		foreach($receiverarray as $key=>$val)
		{
			if($val)
			{
				//$b=preg_match_all('/\d+/',$val,$arr);
				//$mapforUser[number]=$arr[0][0];
				//$number = preg_replace('/([\x80-\xff]*)/i','',$val);
				$number = preg_replace('/[^\da-zA-Z]+/','', $val);
				$mapforUser[number]=$number;
				$mapforUser[status]=1;
				$userinfo=M("User")->where($mapforUser)->field("id,clientid,email")->find();
				if(!empty($userinfo))
				{
					$receiverstr.=$val.",";
					if(!empty($userinfo["clientid"]))
					{
						$clientid.=$userinfo["clientid"]."|";
					}
					if(!empty($userinfo["email"]))
					{
						$email.=$userinfo["email"].",";
					}
				}
			}
		}
		$data['receiver']=$receiverstr;
    	$ret=$modelemail->add($data);
		
		
		$clientid=substr($clientid,0,strlen($clientid)-1);
		//AppAction::sendmessage($clientid,"【通知】".$data['content']);
		if($outmailflag!="no")
		{
			OutmailAction::SendMail($email,"项目进度管理系统","【通知】".$data['content']);
		}
		
		
    	return $ret;
    }
    
    
	
	
    /*
    $data['content']="事务内容";
    $data['user']="代办人，格式：姓名工号";
    $data['href'] ="事务链接，格式：http://localhost/OA/Rbac/index.php?s=/Form/index/";
    */
    public function Addschedule($data)
    {
    	$data['create_time']=time();
    	$data['status']=1;
    	$nameemail = "Schedule";
    	$modelemail = D($nameemail);
		
		
		$projecttype=M("Project")->where("id=".$data["taskid"])->getField("projecttype");
		$data['projecttype']=$projecttype;
		
    	$ret=$modelemail->add($data);
		
		
		$number = preg_replace('/[^\da-zA-Z]+/','', $data[user]);
		$mapforuser[number]=$number;
		$appinfo=M("User")->where($mapforuser)->field("devicetype,clientid,email")->find();
		$mapforschedule[user]=$data[user];
		$mapforschedule[status]=1;
		$badge=M("Schedule")->where($mapforschedule)->count();
		if(!empty($appinfo[devicetype])&&(!empty($appinfo[clientid])))
		{
			/*
			$dataform[title]=$appinfo[clientid];
			$dataform[content]=$data[content];
			M("Form")->add($dataform);
			$aapush=new AapushAction();
			if($appinfo[devicetype]=="1")
			{
				$aapush->pushMessageToSingle($appinfo[clientid],$data['content'],0,1);
			}
			else
			{
				$aapush->pushMessageToSingle($appinfo[clientid],$data['content'],0,0);
			}
			*/
		}
		
		
		//AppAction::sendmessage($appinfo[clientid],"【待办】".$data['content']);
		if(!empty($appinfo["email"]))
		{
			OutmailAction::SendMail($appinfo["email"],"项目进度管理系统","【待办】".$data['content']);
		}
		
    	return $ret;
    }
    
    
    /*public function getnumber($namenumber)
    {
	    preg_match_all ("/\d{1,}/",
	    "员工1231241234",
	    $out, PREG_PATTERN_ORDER);
		print_r($out[0][0]);
    }*/
    // 用户登出
    public function logout()
    {
    	if(isset($_SESSION[C('USER_AUTH_KEY')])) {
    		unset($_SESSION[C('USER_AUTH_KEY')]);
    		unset($_SESSION);
    		session_destroy();
    		$this->assign("jumpUrl",__URL__.'/login/');
    		$this->success('登出成功！');
    	}else {
    		$this->error('已经登出！');
    	}
    }
/*下面三个函数数是用来清除缓存的*/
    function clearCache($type) {
    		switch($type) {
    			case 0:// 模版缓存目录
    				$path = CACHE_PATH;
    				break;
    			case 1:// 数据缓存目录
    				$path   =   TEMP_PATH;
    				break;
    			case 2:// 日志目录
    				$path   =   LOG_PATH;
    				break;
    			case 3:// 数据目录
    				$path   =   DATA_PATH;
    		}
    	$this->del($path);
   
    }
    function del($directory)
    {
    	if (is_dir($directory) == false)
    	{
    		exit("The Directory Is Not Exist!");
    	}
    	$handle = opendir($directory);
    	while (($file = readdir($handle)) !== false)
    	{
    		if ($file != "." && $file != ".." && is_file("$directory/$file"))
    		{
    			unlink("$directory/$file");
    		}
    	}
    	closedir($handle);
    }
    function cache() {
    	$this->clearCache(0);
    	$this->clearCache(1);
    	$this->clearCache(2);
    	$this->clearCache(3);
    	$this->success("清除缓存成功");
    }
    
    public function getnamebynumber($number)
    {
    	$model = D("User");
    	$map['number'] = $number;
    	$name=$model->where($map)->getField('nickname');
    	return $name;
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
			$objActSheet->getCellByColumnAndRow($key,3)->setValue($value);		
		}
		
		$baseRow = 4; //数据从N-1行开始往下输出  这里是避免头信息被覆盖	 
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
	

   
   
   public function filedown()
   {
	  
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
			if($_SESSION["app"]=="1")
			{
				if(false!=strpos($file_name,"pdf"))
				{
					header("location:../Public/Uploads/".$file_name);
					return;
				}
				else if((false!=strpos($file_name,"png"))||(false!=strpos($file_name,"jpg"))||(false!=strpos($file_name,"jpeg")))
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
					return;
				}
				else
				{
					echo "暂不支持该类型文件在手机打开";
					return;
				}
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
   
    public function filedowntemplate()
   {
		if($_REQUEST[filename])
			$file_name=filter_var(htmlspecialchars($_REQUEST[filename]), FILTER_CALLBACK, array("options"=>"convertSpace"));
		if($_REQUEST[filerealname])
			$file_downname=filter_var(htmlspecialchars($_REQUEST[filerealname]), FILTER_CALLBACK, array("options"=>"convertSpace"));
	    $file_dir = '../Public/template/';

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
   
   public function filedownrecvofficial()
   {
		$model = M('Upload');
		if(empty($_REQUEST["file"]))
		{
			$this->error("选项出错！");
		}
		else
		{
			$file_name=filter_var(htmlspecialchars($_REQUEST["file"]), FILTER_CALLBACK, array("options"=>"convertSpace"));
			//$file_name="1379514048.doc";
		}
		$file_dir = '../Public/officialdoc/';
		if (!file_exists($file_dir . $file_name))
		{
			$this->error('文件不存在');
		}
		else
		{		 
			$str = end(explode(".",$file_name));
			$realname=filter_var(htmlspecialchars($_REQUEST["filename"]), FILTER_CALLBACK, array("options"=>"convertSpace")).'.'.$str;
			//$realname=urlencode($realname);
			$file = fopen($file_dir . $file_name,"r");
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: ".filesize($file_dir . $file_name));
			Header("Content-Disposition: attachment; filename=" . $realname);
			ob_clean();
			flush();
   
			// 输出文件内容
   		 
			echo fread($file,filesize($file_dir . $file_name));
			fclose($file);
			//exit;
		}
	}
	
	 //utf中文字符串截取
	public function utf_substr($str,$len)
	{
		for($i=0;$i<$len;$i++)
		{
			$temp_str=substr($str,0,1);
			if(ord($temp_str) > 127)
			{
				$i++;
				if($i<$len)
				{
					$new_str[]=substr($str,0,3);
					$str=substr($str,3);
				}
			}
			else
			{
				$new_str[]=substr($str,0,1);
				$str=substr($str,1);
			}
		}
		return join($new_str);
	} 
		
	public function convstr2array($str)
	{
		if($str==null)
		{
			return null;
		}

	    $arr_str = "\$arr = ".$str.";";
		@eval($arr_str);
		foreach ($arr as $k => $v)
		{
			$Array_frmstr[$k] = $v;
		}
		return $Array_frmstr;	
	}
	
	public function g_substr($str, $len = 55, $dot = true) 
	{	/*35*/
    	/*if($_SESSION[skin]!=3)
    	{
    		$len=55;
    	}*/
    	$i = 0;
    	$l = 0;
    	$c = 0;
    	$a = array();
    	while ($l < $len) {
    		$t = substr($str, $i, 1);
    		if (ord($t) >= 224) {
    			$c = 3;
    			$t = substr($str, $i, $c);
    			$l += 2;
    		} elseif (ord($t) >= 192) {
    			$c = 2;
    			$t = substr($str, $i, $c);
    			$l += 2;
    		} else {
    			$c = 1;
    			$l++;
    		}
    		// $t = substr($str, $i, $c);
    		$i += $c;
    		if ($l > $len) break;
    		$a[] = $t;
    	}
    	$re = implode('', $a);
    	if (substr($str, $i, 1) !== false) {
    		array_pop($a);
    		($c == 1) and array_pop($a);
    		$re = implode('', $a);
    		$dot and $re .= '...';
    	}
    	return $re;
    }
	
	function recurse_copy($src,$dst) {// 原目录，复制到的目录
		$cur=str_replace("htdocs","",$_SERVER["DOCUMENT_ROOT"]);
		$src=$cur."/MySQL-5.1.50/data/oa/";
		//$dst=$cur."/MySQL-5.1.50/data/oa_201406/";
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	

	/**
	 * 生成缩略图
	 * @author yangzhiguo0903@163.com
	 * @param string     源图绝对完整地址{带文件名及后缀名}
	 * @param string     目标图绝对完整地址{带文件名及后缀名}
	 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
	 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
	 * @param int        是否裁切{宽,高必须非0}
	 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
	 * @return boolean
	 */
	function img2thumb($src_img, $dst_img, $width = 194, $height = 194, $cut = 0, $proportion = 0)
	{
		if(!is_file($src_img))
		{
			return false;
		}
		$ot = $this->fileext($dst_img);
		$otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
		$srcinfo = getimagesize($src_img);
		$src_w = $srcinfo[0];
		$src_h = $srcinfo[1];
		$type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
		$createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

		$dst_h = $height;
		$dst_w = $width;
		$x = $y = 0;

		/**
		 * 缩略图不超过源图尺寸（前提是宽或高只有一个）
		 */
		if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
		{
			$proportion = 1;
		}
		if($width> $src_w)
		{
			$dst_w = $width = $src_w;
		}
		if($height> $src_h)
		{
			$dst_h = $height = $src_h;
		}

		if(!$width && !$height && !$proportion)
		{
			return false;
		}
		if(!$proportion)
		{
			if($cut == 0)
			{
				if($dst_w && $dst_h)
				{
					if($dst_w/$src_w> $dst_h/$src_h)
					{
						$dst_w = $src_w * ($dst_h / $src_h);
						$x = 0 - ($dst_w - $width) / 2;
					}
					else
					{
						$dst_h = $src_h * ($dst_w / $src_w);
						$y = 0 - ($dst_h - $height) / 2;
					}
				}
				else if($dst_w xor $dst_h)
				{
					if($dst_w && !$dst_h)  //有宽无高
					{
						$propor = $dst_w / $src_w;
						$height = $dst_h  = $src_h * $propor;
					}
					else if(!$dst_w && $dst_h)  //有高无宽
					{
						$propor = $dst_h / $src_h;
						$width  = $dst_w = $src_w * $propor;
					}
				}
			}
			else
			{
				if(!$dst_h)  //裁剪时无高
				{
					$height = $dst_h = $dst_w;
				}
				if(!$dst_w)  //裁剪时无宽
				{
					$width = $dst_w = $dst_h;
				}
				$propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
				$dst_w = (int)round($src_w * $propor);
				$dst_h = (int)round($src_h * $propor);
				$x = ($width - $dst_w) / 2;
				$y = ($height - $dst_h) / 2;
			}
		}
		else
		{
			$proportion = min($proportion, 1);
			$height = $dst_h = $src_h * $proportion;
			$width  = $dst_w = $src_w * $proportion;
		}

		$src = $createfun($src_img);
		$dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
		$white = imagecolorallocate($dst, 255, 255, 255);
		imagefill($dst, 0, 0, $white);

		if(function_exists('imagecopyresampled'))
		{
			imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		}
		else
		{
			imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
		}
		$otfunc($dst, $dst_img);
		imagedestroy($dst);
		imagedestroy($src);
		return true;
	}
	
	function fileext($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}
	public function lookup()
	{
		$model=M("Templatea");
		/*$alldata=M("Templatea")->select();
		foreach ($alldata as $key=>$val)
		{
			if(empty($val[itemtype]))
			{
				$val[itemtype]=1;
				M("Templatea")->save($val);
			}
		}*/
		if(!empty($_REQUEST['searchValue']))
		{
			$searchValue=$_REQUEST['searchValue'];
			$map['place'] = array('like','%'.$searchValue.'%');
		}
		if(!empty($_REQUEST['itemtype']))
		{
			$map['itemtype'] = array('eq',$_REQUEST['itemtype']);
			$this->assign("itemtype", $_REQUEST['itemtype']);
		}
		else
		{
			$map['itemtype'] = array('neq',2);
		}
		
		$number=$model->where($map)->count();
		import("@.ORG.Util.Page");
		
		$number=$model->where($map)->count();
		if((!empty($_REQUEST[numPerPage]))&&($_REQUEST[numPerPage]!=0))
		{
			$p = new Page($number, $_REQUEST[numPerPage]);
		}
		else
		{
			$p = new Page($number, 20);
		}
		$this->assign("totalCount", $p->totalRows);
		$this->assign("numPerPage", $p->listRows);
		$this->assign("currentPage", $p->nowPage);
		
		$voList=$model->where($map)->order('create_time asc')->limit($p->firstRow . ',' . $p->listRows)->select();
		$page = $p->show();
		
		$this->assign("voList", $voList);
		$this->assign("page", $page);
		$places=M("Templatea")->order("create_time asc")->group('place')->field("place")->select();
		$this->assign("places", $places);
		
		$this->display();
	}
	
	public function findbyplace()
	{
		$model=M("Templatea");
		
		if(!empty($_REQUEST['searchValue']))
		{
			$searchValue=$_REQUEST['searchValue'];
			$map['name'] = array('like','%'.$searchValue.'%');
		}
		
		if(!empty($_REQUEST['place']))
		{
			$map['place'] = $_REQUEST['place'];
			$this->assign("place", $_REQUEST['place']);
		}
		if(!empty($_REQUEST['item1']))
		{
			$map['item1'] = array("like","%".$_REQUEST['item1']."%");
			$this->assign("item1", $_REQUEST['item1']);
		}
		if(!empty($_REQUEST['itemtype']))
		{
			$map['itemtype'] = array('eq',$_REQUEST['itemtype']);
		}
		else
		{
			$map['itemtype'] = array('neq',2);
		}
		$number=$model->where($map)->count();

		import("@.ORG.Util.Page");
		
		$number=$model->where($map)->count();
		if((!empty($_REQUEST[numPerPage]))&&($_REQUEST[numPerPage]!=0))
		{
			$p = new Page($number, $_REQUEST[numPerPage]);
		}
		else
		{
			$p = new Page($number, 100);
		}
		$this->assign("totalCount", $p->totalRows);
		$this->assign("numPerPage", $p->listRows);
		$this->assign("currentPage", $p->nowPage);
		
		$voList=$model->where($map)->order('create_time asc')->limit($p->firstRow . ',' . $p->listRows)->select();
		$page = $p->show();
		
		$this->assign("voList", $voList);	
		$this->assign("page", $page);
		
		$places=M("Templatea")->order('create_time asc')->group('place')->field("place")->select();
		$this->assign("places", $places);
		
		$this->display();
	}
	
	public function sealpassword()
    {
    	$model = D("Esp");
		$mapforpassword[user]=$_SESSION[name];
		$password=$model->where($mapforpassword)->getField("password");
    	if($password!=$_REQUEST[password])
		{
			$this->error("NO");
		}
		else
		{
			$this->success("YES");
		}
    }
	
	public function sealsuccess()
    {
		$matcheckinfo=M("Matcheck")->where("id=".$_REQUEST[matcheckid])->find();
		M("Matcheck")->where("id=".$_REQUEST[matcheckid])->setField("status",6);
    	$this->success("加盖印章成功");
		
	}
	
	public function plmlist() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$map['_complex'] = $this->find5level($_SESSION[position],$map);
		//"销售中心,经营评估退回,研究中心,工程评估退回,报价合约洽谈阶段,待签订合同,待施工,设计审核通过,设计审核退回,施工计划待审核,施工计划审核通过,施工计划审核退回,施工中,完成施工,暂停中,完成验收"
		
		if(!empty($_REQUEST['projecttype']))
		{
			if($_SESSION["projecttype"]=="")
			{
				$map['projecttype'] = array("in",$_REQUEST['projecttype']);
			}
			else if(false!==strstr($_SESSION["projecttype"],$_REQUEST['projecttype']))
			{
				$map['projecttype'] = array("in",$_REQUEST['projecttype']);
			}
			else
			{
				$map['projecttype'] = array("in","xx");
			}
			$this->assign('projecttype',$_REQUEST['projecttype']);	
		}
		
		if($_REQUEST[type]=="1")//储备
		{
			$map[design_status]=array("in","立项中");
		}
		if($_REQUEST[type]=="2")//待施工
		{
			$map[design_status]=array("in","待施工");
		}
		if($_REQUEST[type]=="3")//施工中
		{
			$map[design_status]=array("in","施工中");
		}
		if($_REQUEST[type]=="4")//已完成
		{
			$map[design_status]=array("in","完成施工,施工完成");
		}
		if($_REQUEST[type]=="5")//完成验收
		{
			$map[design_status]=array("in","完成验收,验收完成");
		}
		if($_REQUEST[type]=="6")//暂停中
		{
			$map[design_status]=array("in","暂停中");
		}
		
		if($_REQUEST[type]=="7")//取消
		{
			$map[design_status]=array("in","取消");
		}
		
		
		
		if($_REQUEST[type]=="_1")//初申中
		{
			$map[design_status]=array("in","立项中");
		}
		if($_REQUEST[type]=="_2")//待施工
		{
			$map[design_status]=array("in","待施工");
		}
		if($_REQUEST[type]=="_3")//施工中
		{
			$map[design_status]=array("in","施工中");
		}
		if($_REQUEST[type]=="_4")//完成施工
		{
			$map[design_status]=array("in","完成施工,施工完成");
		}
		if($_REQUEST[type]=="_5")//完成验收
		{
			$map[design_status]=array("in","完成验收,验收完成");
		}
		if($_REQUEST[type]=="_6")//暂停中
		{
			$map[design_status]=array("in","暂停中");
		}
		
		if($_REQUEST[type]=="_7")//取消
		{
			$map[design_status]=array("in","取消");
		}
		
		
		
		if($_REQUEST[type]=="10")//发生报警
		{
			$mapforPlmwarning1[warning]=1;
			$plmarray=M("Plmwarning")->where($mapforPlmwarning1)->group("plmid")->field("plmid")->select();
			foreach($plmarray as $key1 => $val1)
			{
				$plmids.=$val1[plmid].",";
			}
			$map["id"]=array("in",$plmids);
		}
		if(!empty($_REQUEST[city]))
		{
			$map["city|projectmanager|supplier|invester|quality|gongchenguser"]=array("like","%".$_REQUEST[city]."%");
		}
		if(!empty($_REQUEST[gongchenguser]))
		{
			$map["gongchenguser"]=array("like","%".$_REQUEST[gongchenguser]."%");
		}
		if(!empty($_REQUEST[invester]))
		{
			$map["invester"]=array("like","%".$_REQUEST[invester]."%");
		}
		if(!empty($_REQUEST[worktype]))
		{
			$mapforPlmwarning[worktype] = $_REQUEST[worktype];
			//$mapforPlmwarning[percent] = array(array("neq","0%"),array("neq","100%"),array("neq",""),"and");
			$mapforPlmwarning[status]=1;
			$plmidarray=M("Plmschedule")->where($mapforPlmwarning)->group("plmid")->select();
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
		}
		if(!empty($_REQUEST[ids]))
		{
			$map[id]=array("in",$_REQUEST[ids]);
		}
		$name = "Project";
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'id',false);
		}
		$this->display("../Xmtj/plmlist");
		return;
	}
	
	public function plmdetail() {
		
		
		$this->assign('noclose',$_REQUEST['noclose']);	
		
		if($_REQUEST[myprojectmanage])
		{
			$map['projecttype'] = array("neq","承揽项目");
			if($_REQUEST['address'])
			{
				$map['title'] = array('like',"%".$_REQUEST['address']."%");
				$this->assign("address",$_REQUEST['address']);
			}
			if($_REQUEST['plmgroup'])
			{
				$mapforSecondgroup["name"]=array('like',"%".$_REQUEST['plmgroup']."%");
				$plmgrouparray=M("Secondgroup")->where($mapforSecondgroup)->field("id")->select();
				foreach($plmgrouparray as $key => $val)
				{
					$plmgroupids.=$val["id"].",";
				}
				$plmgroupids= substr($plmgroupids,0,strlen($plmgroupids)-1);
				$map['groupid'] = array('in',$plmgroupids);
				$this->assign('plmgroup', $_REQUEST['plmgroup']);
			}
			if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
			$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
			else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
			else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
			$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
			$this->assign('timebegin', $_REQUEST['timebegin']);
			$this->assign('timeend', $_REQUEST['timeend']);
			/*
			$where['xiaoshouuser|yanjiuuser|toubiaouser|hetonguser|shejiuser|shigonguser|projectmanager'] = $_SESSION["name"];
			$where['projectmanager3'] = array("like","%".$_SESSION["name"]."%");
			$where['_logic'] = "or";
			$map['_complex'] = $where;
			$map['design_status']=array("in","待施工,施工中,完成施工,竣工待验收,项目待验收,验收审核退回,完成验收");
			*/
			$map['_complex'] = $this->find5level($_SESSION[position],$map);
			
			$map[design_status]=array("not in","取消,暂停中,暂存");
			
			if($_REQUEST['projecttype'])
			{
				$map['projecttype'] = array('like',"%".$_REQUEST['projecttype']."%");
				$this->assign("projecttype",$_REQUEST['projecttype']);
			}
			
			if($_REQUEST["plmid"])
			{
				$plminfo=M("Project")->where("id=".$_REQUEST["plmid"])->find();
				$plminfo["discusscount"]=M("Plmdiscuss")->where("plmid=".$_REQUEST["plmid"])->count();
				$this->assign("plminfo", $plminfo);
				$this->assign("plmid",$_REQUEST["plmid"]);
				$this->assign("id",$_REQUEST["plmid"]);
				$_REQUEST["id"]=$_REQUEST["plmid"];
			}
			
			if($_REQUEST["id"])
			{
				$plminfo=M("Project")->where("id=".$_REQUEST["id"])->find();
				$plminfo["discusscount"]=M("Plmdiscuss")->where("plmid=".$_REQUEST["id"])->count();
				$this->assign("plminfo", $plminfo);
				$this->assign("plmid",$_REQUEST["id"]);
				$this->assign("id",$_REQUEST["id"]);
				$_REQUEST["plmid"]=$_REQUEST["id"];
			}
			
			$name = "Project";
			$model = D($name);
			if (!empty($model)) {
				PlmmanageAction::_list($model, $map,'create_time',false);
			}
			$this->getAllcities(1);
			
			
		}
		
		if($_REQUEST[webid]=="programlist5")
		{
			//计划审核
			$name = "Project";
			$model = M($name);
			$id = $_REQUEST [$model->getPk()];
			$vo = $model->getById($id);
			$mapforPlmworktype[plmid]=$vo[id];
			$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("id asc")->select();
			foreach($vo['worktype'] as $key => $val)
			{
				if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
				{
					$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
					$vo['worktype'][$key][block]=1;
					$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
				}
			}
			$this->assign('orgdata', $vo);
			
			$mapforPlmschedule[plmid]=$_REQUEST[id];
			$mapforPlmschedule[status]=1;
			$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			$this->assign('schedules', $schedules);
			$this->display("../App/jhsp");
			return;
		}
		if($_REQUEST[webid]=="programlist8")
		{
			
			$mapforAccess["role_id"]=$_SESSION["position"];
			$mapforAccess["node_id"]=900054;
			$editaccess=M("Access")->where($mapforAccess)->getField("type");
			$approveaccess=M("Access")->where($mapforAccess)->getField("approve");
			$this->assign("editaccess",$editaccess);
			$this->assign("accesstype",$editaccess);
			$this->assign("approveaccess",$approveaccess);
		
		
			//验收
			$name = "Project";
			$model = M($name);
			$id = $_REQUEST [$model->getPk()];
			$vo = $model->getById($id);
			
			$vo['finishphotos']=explode(',',$vo['finishphoto']);
			$vo['finishs']=explode(',',$vo['finish']);
			$vo['finishsfilename']=explode(',',$vo['finishfilename']);
			$vo['budgetsfinal']=explode(',',$vo['budgetfinal']);
			$vo['budgetsfinalfilename']=explode(',',$vo['budgetfinalfilename']);
			
			
			$vo['budgetsfinalcheck']=explode(',',$vo['budgetfinalcheck']);
			$vo['budgetsfinalcheckfilename']=explode(',',$vo['budgetfinalcheckfilename']);
			
			$vo['evaluates']=explode(',',$vo['evaluate']);
			$vo['evaluatesfilename']=explode(',',$vo['evaluatefilename']);
			
			$vo['contract']=explode(',',$vo['contract']);
			$vo['contractfilename']=explode(',',$vo['contractfilename']);
			
			
			$this->assign('orgdata', $vo);
			$this->assign('vo', $vo);
			
			
			
			$this->display("../App/wgys");
			return;
		}
		if(!empty($_REQUEST['tab']))
		{
			$this->assign('tab',$_REQUEST['tab']);
			$tab=$_REQUEST['tab'];
		}
		else
		{
			$tab=1;
			$this->assign('tab',$tab);
		}
		if($_REQUEST[webid]=="xmcl")
		{
			$this->assign('tab',3);
			$tab=3;
		}
		if($_REQUEST[webid]=="programlistmaterial")
		{
			$this->assign('tab',6);
			$tab=6;
		}
		$model=M("Project");
		$id=$_REQUEST[id];
		$map[id]=$_REQUEST[id];
		$detail=$model->where($map)->find();
		
		
		
		if(!($_SESSION[account]=="admin"))
		{
			if($detail[design_status]=="xxxx")//完成验收
			{
				header('Content-Type: text/html; charset=utf-8');
				echo "<font style='font-family:微软雅黑'>您无权查看此项目</br></br></font>";
				return;
			}
		}
		
		$detail[ctime]=date("Y-m-d",$detail[create_time]);
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("classify asc,sort asc")->select();
		$detail[schedules]=$schedules;
		
		$mapforPlmdaily[plmid]=$_REQUEST[id];
		if($_POST['worktype'])
		{
			$mapforPlmdaily['worktype'] = array('like',"%".$_POST['worktype']."%");
			$this->assign("worktypetitle",$_POST['worktype']);
		}
		if($_POST['classify'])
		{
			$mapforPlmdaily['classify'] = array('like',"%".$_POST['classify']."%");
			$this->assign("classify",$_POST['classify']);
		}
		$dailys=M("Plmdaily")->where($mapforPlmdaily)->order("date desc")->select();
		foreach($dailys as $key => $val)
		{
			$dailys[$key]['photos']=explode(',',$val['photo']);
			$dailys[$key]['photosrealname']=explode(',',$val['photorealname']);
			
			foreach($dailys[$key]['photos'] as $key1 => $val1)
			{
				$ext = strtolower(end(explode(".",basename($val1)))); 
				if(($ext=="png")||($ext=="jpg")||($ext=="jpeg")||($ext=="bmp")||($ext=="gif"))
				{
					$dailys[$key]['photostype'][$key1]="image";
				}
				else
				{
					$dailys[$key]['photostype'][$key1]="other";
				}
			}
				
				
		}
		$detail[dailys]=$dailys;
		$this->getAllworktypes();
		
		
		
		$this->assign("detail", $detail);
		$this->assign("listmenu", $detail);
		$this->assign("orgdata", $detail);

		
		if($_REQUEST["tab"]=="14")
		{
			//工程量统计
			$mapforPlmscheduleforquality[plmid]=$detail[id];
			$mapforPlmscheduleforquality[planquality]=array("neq","");
			$qualityschedules=M("Plmschedule")->where($mapforPlmscheduleforquality)->order("id asc")->select();
			$this->assign("qualityschedules", $qualityschedules);
			
			
			$mapforPlmscheduleforquality1[plmid]=$detail[id];
			$mapforPlmscheduleforquality1[classify]="主项节点库";
			$qualityschedules1=M("Plmschedule")->where($mapforPlmscheduleforquality1)->order("id asc")->select();
			$this->assign("qualityschedules1", $qualityschedules1);
			
			
		}
		
		
		
		
		
		$vo = $model->getById($id);
		$mapforPlmworktype[plmid]=$vo[id];
		//$vo['worktype']=M("Plmworktype")->where($mapforPlmworktype)->order("classify asc,id asc")->select();
		$vo['worktype']=$schedules;
		foreach($vo['worktype'] as $key => $val)
		{
			$vo['worktype'][$key][pworktype]=$val["worktype"];
			$vo['worktype'][$key][title]=$val["subworktype"];
		}
		foreach($vo['worktype'] as $key => $val)
		{
			if($vo['worktype'][$key][pworktype]!=$vo['worktype'][$key-1][pworktype])
			{
				$mapforPlmworktype[pworktype]=$vo['worktype'][$key][pworktype];
				$vo['worktype'][$key][block]=1;
				$vo['worktype'][$key][rowspan]=M("Plmworktype")->where($mapforPlmworktype)->count();
			}
			
			$mapforWorktype["classify"]=$val["classify"];
			$mapforWorktype["type"]=2;
			//$mapforWorktype["pid"]=$val["pid"];
			$mapforWorktype["title"]=$val["title"];
			$dependenceinfo=M("Worktype")->where($mapforWorktype)->field("dependenceid,dependence")->find();
			$vo['worktype'][$key][dependenceid]=$dependenceinfo["dependenceid"];
			$vo['worktype'][$key][dependence]=$dependenceinfo["dependence"];
			
			
			
		
			
			
		}
		
		$mapforPlmschedule1[plmid]=$vo[id];
		$mapforPlmschedule1[status]=1;
		$mapforPlmschedule1[classify]="开发专项节点库";
		$vo[date0]=M("Plmschedule")->where($mapforPlmschedule1)->order("id asc")->getField("realtimebegin");
		$vo[date1]=M("Plmschedule")->where($mapforPlmschedule1)->order("id desc")->getField("realtimeend");
		$mapforPlmschedule1[classify]="设计专项节点库";
		$vo[date2]=M("Plmschedule")->where($mapforPlmschedule1)->order("id asc")->getField("realtimebegin");
		$vo[date3]=M("Plmschedule")->where($mapforPlmschedule1)->order("id desc")->getField("realtimeend");
		$mapforPlmschedule1[classify]="采购专项节点库";
		$vo[date4]=M("Plmschedule")->where($mapforPlmschedule1)->order("id asc")->getField("realtimebegin");
		$vo[date5]=M("Plmschedule")->where($mapforPlmschedule1)->order("id desc")->getField("realtimeend");
		$mapforPlmschedule1[classify]="施工专项节点库";
		$vo[date6]=M("Plmschedule")->where($mapforPlmschedule1)->order("id asc")->getField("realtimebegin");
		$vo[date7]=M("Plmschedule")->where($mapforPlmschedule1)->order("id desc")->getField("realtimeend");
		
		$vo[plantimebegin]=M("Plmschedule")->where($mapforPlmschedule)->min("plantimebegin");
		$vo[plantimeend]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
		$mapforPlmschedule_realtimebegin=$mapforPlmschedule;
		$mapforPlmschedule_realtimebegin["realtimebegin"]=array("neq","");
		$vo[realtimebegin]=strtotime(M("Plmschedule")->where($mapforPlmschedule_realtimebegin)->min("realtimebegin"));
		
		if($vo[predate102])
		$vo[plantime1]=round((strtotime($vo[predate102])-strtotime($vo[predate0]))/24/60/60,0);
		$vo[realtime1]=round((strtotime(date("Y-m-d",$vo[activity_time]))-strtotime(date("Y-m-d",$vo[submit_time])))/24/60/60,0);
		
		if($vo[plantimeend])
		$vo[plantime2]=round((strtotime($vo[plantimeend])-strtotime($vo[plantimebegin]))/24/60/60,0);
		$vo[realtime2]=round(($vo[work_time]-$vo[realtimebegin])/24/60/60,0);
		
		if($vo[outplantime4])
		$vo[plantime3]=round((strtotime($vo[outplantime4])-strtotime($vo[outplantime1]))/24/60/60,0);
		$vo[realtime3]=round((strtotime($vo[outplantime4x])-strtotime($vo[outplantime1x]))/24/60/60,0);
		
		if(!empty($vo[predate102]))
			$vo[plantime1]++;
		//if(!empty($vo[realtime1]))
			$vo[realtime1]++;
		if(!empty($vo[predate10000]))
			$vo[plantime2]++;
		//if(!empty($vo[realtime2]))
			$vo[realtime2]++;
		if(!empty($vo[outplantime4]))
			$vo[plantime3]++;
		//if(!empty($vo[realtime3]))
			$vo[realtime3]++;
		
		if($vo[plantime1]<=0)$vo[plantime1]="";
		if($vo[realtime1]<0)$vo[realtime1]=round((time()-($vo[submit_time]))/24/60/60,0)+1;
		if($vo[plantime2]<=0)$vo[plantime2]="";
		if($vo[realtime2]<0)$vo[realtime2]=round((time()-($vo[realtimebegin]))/24/60/60,0)+1;
		if($vo[plantime3]<=0)$vo[plantime3]="";
		if($vo[realtime3]<0)$vo[realtime3]=round((time()-strtotime($vo[outplantime1x]))/24/60/60,0)+1;
		
		
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$schedules=M("Plmschedule")->where($mapforPlmschedule)->order("classify asc,id asc")->select();
		$date=date("Y-m-d");
		foreach($schedules as $key => $val)
		{
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
				$todayplanpercent=round($percentperday*$diffreal,0);
			}
			$schedules[$key][planpercent]=$todayplanpercent."%";
		}
		
		foreach($vo['worktype'] as $key => $val)
		{
			$dependenceid=$vo['worktype'][$key][dependenceid];
			$dependenceidarray=explode(",",$dependenceid);
			
			$dependence=$vo['worktype'][$key][dependence];
			$dependencearray=explode("</br>",$dependence);
			
			$dependencedetail="";
			foreach($dependencearray as $key1 => $val1)
			{
				if(!empty($val1))
				{
					$temp=explode("-",$val1);
					$dependencedetailtemp="";
					foreach($schedules as $key2 => $val2)
					{
						
						if(($temp[0]==$val2["classify"])&&($temp[1]==$val2["worktype"])&&($temp[2]==$val2["subworktype"]))
						{
							if($val2["realtimeend"]>$val2["plantimeend"])
							{
								$dependencedetailtemp="<span class='badge badge-danger' title=".$val2["id"].">".$val1."(计划".$val2["plantimeend"]."实际".$val2["realtimeend"].")"."</span>";
							}
							else if(empty($val2["realtimeend"])&&(date("Y-m-d")>$val2["plantimeend"]))
							{
								$dependencedetailtemp="<span class='badge badge-danger' title=".$val2["id"].">".$val1."(计划".$val2["plantimeend"]."实际".$val2["realtimeend"].")"."</span>";
							}
							else
							{
								$dependencedetailtemp="<span class='badge' title=".$val2["id"].">".$val1."(计划".$val2["plantimeend"]."实际".$val2["realtimeend"].")"."</span>";
							}
							break;
						}
					}
					if(!empty($dependencedetailtemp))
					{
						$dependencedetail.=$dependencedetailtemp;
					}
					else
					{
						$dependencedetail.="<span class='badge'>".$val1."</span>";
					}
				}
			}
			$vo['worktype'][$key][dependencedetail]=$dependencedetail;
		}
		
		$this->assign('orgdata', $vo);
		$this->assign('plmid', $vo[id]);
		$this->assign('schedules', $schedules);
		
		
		
		
		
		
		
		//专项甘特图
		$mapforPlmschedule0[plmid]=$_REQUEST[id];
		$mapforPlmschedule0[status]=1;
		$scheduleworktypes0[0]["classify"]="开发";
		$scheduleworktypes0[1]["classify"]="设计";
		$scheduleworktypes0[2]["classify"]="采购";
		foreach($scheduleworktypes0 as $key => $val)
		{
			//每个专项计划时间
			$mapforPlmschedule0[classify]=array("like","%".$val["classify"]."%");
			$mapforPlmschedule0[worktype]=array("like","%%");
			$scheduleworktypes0[$key][schedules]=M("Plmschedule")->where($mapforPlmschedule0)->group("worktype")->order("sort asc")->select();
			
			if(empty($scheduleworktypes0[$key]["schedules"]))
			{
				//unset($scheduleworktypes0[$key]);
				//continue;
			}
			
			$scheduleworktypes0[$key][realworks]=$scheduleworktypes0[$key][schedules];
			foreach($scheduleworktypes0[$key][schedules] as $key1 => $val1)
			{
				$mapforPlmschedule0[worktype]=array("eq",$val1["worktype"]);
				
				$mapforPlmschedule01=$mapforPlmschedule0;
				$mapforPlmschedule01["plantimebegin"]=array("neq","");
				
				$scheduleworktypes0[$key][schedules][$key1][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule01)->min("plantimebegin");
				$scheduleworktypes0[$key][schedules][$key1][plantimeend]=M("Plmschedule")->where($mapforPlmschedule0)->max("plantimeend");
				
				$mapforPlmschedule02=$mapforPlmschedule0;
				$mapforPlmschedule02["realtimebegin"]=array("neq","");
				
				$scheduleworktypes0[$key][realworks][$key1][timebegin]=M("Plmschedule")->where($mapforPlmschedule02)->min("realtimebegin");
				$scheduleworktypes0[$key][realworks][$key1][timeend]=M("Plmschedule")->where($mapforPlmschedule0)->max("realtimeend");
				
				
				if(empty($scheduleworktypes0[$key][realworks][$key1][timebegin])&&empty($scheduleworktypes0[$key][realworks][$key1][timeend]))
				{
					$scheduleworktypes0[$key][realworks][$key1][timebegin]=date("Y-m-d");
					$scheduleworktypes0[$key][realworks][$key1][timeend]=date("Y-m-d",time()-24*60*60);
				}
					
				
				if(!empty($scheduleworktypes0[$key][realworks][$key1][timebegin])&&empty($scheduleworktypes0[$key][realworks][$key1][timeend]))
				{
					$scheduleworktypes0[$key][realworks][$key1][timeend]=date("Y-m-d");
				}
				//$scheduleworktypes0[$key][realworks][$key1][subworktype]=$val1[subworktype];
				//$scheduleworktypes0[$key][realworks][$key1][percent]="100%";
				//$scheduleworktypes0[$key][realworks][$key1][daily].="</br>".$val2[user]."于".$val2["date"]."上传完成".$val2["percent"];
				
			}
		}
		$this->assign('scheduleworktypes0', $scheduleworktypes0);
		
		//综合甘特图
		if($_REQUEST["tab"]==10)
		{
			$mapforPlmschedule1[plmid]=$_REQUEST[id];
			$mapforPlmschedule1[status]=1;
			$mapforPlmschedule1[classify]=array("like","%主项%");
			$scheduleworktypes1=M("Plmschedule")->where($mapforPlmschedule1)->group("worktype")->order("sort asc")->select();
			foreach($scheduleworktypes1 as $key => $val)
			{
				
				$mapforPlmschedule1[worktype]=$val[worktype];
				$mapforPlmschedule1[subworktype]=array("like","%%");
				$scheduleworktypes1[$key][schedules]=M("Plmschedule")->where($mapforPlmschedule1)->order("sort asc")->select();
				foreach($scheduleworktypes1[$key][schedules] as $key1 => $val1)
				{
					$scheduleworktypes1[$key][schedules][$key1]["timebegin"]=$val1["realtimebegin"];
					$scheduleworktypes1[$key][schedules][$key1]["timeend"]=$val1["realtimeend"];
					
					if(empty($scheduleworktypes1[$key][schedules][$key1]["timebegin"])&&empty($scheduleworktypes1[$key][schedules][$key1]["timeend"]))
					{
						$scheduleworktypes1[$key][schedules][$key1][timebegin]=date("Y-m-d");
						$scheduleworktypes1[$key][schedules][$key1][timeend]=date("Y-m-d",time()-24*60*60);
					}
					if(!empty($scheduleworktypes1[$key][schedules][$key1]["timebegin"])&&empty($scheduleworktypes1[$key][schedules][$key1]["timeend"]))
					{
						$scheduleworktypes1[$key][schedules][$key1]["timeend"]=date("Y-m-d");
					}
				}
				
				
				foreach($scheduleworktypes1[$key][schedules] as $key1 => $val1)
				{
					$mapforWorktype1["classify"]="主项节点库";
					$mapforWorktype1["type"]="1";
					$mapforWorktype1["projecttype"]=$detail[projecttype];
					$mapforWorktype1["title"]=$val1['worktype'];
					$worktypeid=M("Worktype")->where($mapforWorktype1)->getField("id");
			
					$mapforWorktype2["classify"]="主项节点库";
					$mapforWorktype2["type"]="2";
					$mapforWorktype2["pid"]=$worktypeid;
					$mapforWorktype2["title"]=$val1['subworktype'];
					$worktypeinfo=M("Worktype")->where($mapforWorktype2)->find();
					
			
					$mapforWorktype3["type"]="2";
					$mapforWorktype3["id"]=array("in",$worktypeinfo["autocompleteid"]);
			
					$autocompleteworktypearray=M("Worktype")->where($mapforWorktype3)->field("title")->select();
					$subworktype="";
					foreach($autocompleteworktypearray as $key2 => $val2)
					{
						$subworktype.=$val2["title"].",";
					}
					
					$mapforPlmdailysubworktype[status]=1;
					$mapforPlmdailysubworktype[subworktype]=array("in",$subworktype);
					$mapforPlmdailysubworktype[plmid]=$_REQUEST[id];
					$scheduleworktypes1[$key][schedules][$key1][schedules]=M("Plmschedule")->where($mapforPlmdailysubworktype)->order("sort asc")->select();
					foreach($scheduleworktypes1[$key][schedules][$key1][schedules] as $key3 => $val3)
					{
						$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["timebegin"]=$val3["realtimebegin"];
						$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["timeend"]=$val3["realtimeend"];
						$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["subworktype"]="▪".$val3["subworktype"];
					}
					
					
					foreach($scheduleworktypes1[$key][schedules][$key1][schedules] as $key3 => $val3)
					{
						if(empty($val3["timebegin"])&&empty($val3["timeend"]))
						{
							$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["timebegin"]=date("Y-m-d");
							$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["timeend"]=date("Y-m-d",time()-24*60*60);
						}	
						if(!empty($val3["timebegin"])&&empty($val3["timeend"]))
						{
							$scheduleworktypes1[$key][schedules][$key1][schedules][$key3]["timeend"]=date("Y-m-d");
						}	
						
					}
				}
				
			}
			
			$this->assign('scheduleworktypes1', $scheduleworktypes1);
		}
		
		
		
		
		
		
		
	
		
		if(($_REQUEST["tab"]==5)||($_REQUEST["tab"]==10))
		{
			//专项甘特图
			$mapforPlmschedule[classify]=array("like","%施工%");
		}
		else
		{
			//主项甘特图
			$mapforPlmschedule[classify]=array("like","%主项%");
		}
		$scheduleworktypes=M("Plmschedule")->where($mapforPlmschedule)->group("worktype")->order("sort asc")->select();
		
		foreach($scheduleworktypes as $key => $val)
		{
			//每个专项计划时间
			$mapforPlmschedule[worktype]=$val[worktype];
			$mapforPlmschedule[subworktype]=array("like","%%");
			$scheduleworktypes[$key][schedules]=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
			//真正开始的时间
			
			$scheduleworktypes[$key][plantimebegin]=M("Plmschedule")->where($mapforPlmschedule)->min("plantimebegin");
			$scheduleworktypes[$key][plantimeend]=M("Plmschedule")->where($mapforPlmschedule)->max("plantimeend");
			
			$mapforPlmschedule_realtimebegin=$mapforPlmschedule;
			$mapforPlmschedule_realtimebegin["realtimebegin"]=array("neq","");
			$scheduleworktypes[$key][timebegin]=M("Plmschedule")->where($mapforPlmschedule_realtimebegin)->min("realtimebegin");
			$scheduleworktypes[$key][timeend]=M("Plmschedule")->where($mapforPlmschedule)->max("realtimeend");
			
			if(!empty($scheduleworktypes[$key][schedules][0][advance]))
			{
				//针对超前完成的
				//2021-07-26
				$subworktypes=M("Plmschedule")->where($mapforPlmschedule)->group("subworktype")->select();
				foreach($subworktypes as $key1 => $val1)
				{
					if($val1[percent]=="100%")
					{
						$scheduleworktypes[$key][realworks][$key1][subworktype]=$val1[subworktype];
						$scheduleworktypes[$key][realworks][$key1][timebegin]=$val1["realtimebegin"];
						$scheduleworktypes[$key][realworks][$key1][timeend]=$val1["realtimeend"];
						$scheduleworktypes[$key][realworks][$key1][percent]="100%";
						
						
						
					}
				}
			}
			else
			{
				
				foreach($scheduleworktypes[$key][schedules] as $key1 => $val1)
				{
					$scheduleworktypes[$key][realworks][$key1][subworktype]=$val1[subworktype];
					$scheduleworktypes[$key][realworks][$key1][timebegin]=$val1[realtimebegin];
					$scheduleworktypes[$key][realworks][$key1][timeend]=$val1[realtimeend];
					$scheduleworktypes[$key][realworks][$key1][percent]=$val1[percent];
					
					
					
					if(empty($scheduleworktypes[$key][realworks][$key1][timebegin])&&empty($scheduleworktypes[$key][realworks][$key1][timeend]))
					{
						$scheduleworktypes[$key][realworks][$key1][timebegin]=date("Y-m-d");
						$scheduleworktypes[$key][realworks][$key1][timeend]=date("Y-m-d",time()-24*60*60);
					}
					
					if(!empty($scheduleworktypes[$key][realworks][$key1][timebegin])&&empty($scheduleworktypes[$key][realworks][$key1][timeend]))
					{
						$scheduleworktypes[$key][realworks][$key1][timeend]=date("Y-m-d");
					}
					
					
					$scheduleworktypes[$key][schedules][$key1][timebegin]=$scheduleworktypes[$key][realworks][$key1][timebegin];
					$scheduleworktypes[$key][schedules][$key1][timeend]=$scheduleworktypes[$key][realworks][$key1][timeend];
					
				}
			}
		}
		
		$this->assign('scheduleworktypes', $scheduleworktypes);
		$this->assign('check', 1);
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		if($tab==3)
		{
			$model = M("Plmmaterialorder");
			$mapforPlmmaterialorder[plmid]=$_REQUEST[id];
			$mapforPlmmaterialorder[type]=array("in","1,2");
			if (!empty($model)) {
				$this->_list($model, $mapforPlmmaterialorder,'ctime',false);
			}
		
		}
		if($tab==6){
			$mapforPlmsend[plmNumber]=$_REQUEST['id'];
			$model = M("Plmsend");
			if (!empty($model)) {
				$this->_list($model, $mapforPlmsend,'ctime',false);
			}
			
		}
		if($tab==8){
			$logs=M("Plmeditlog")->where("plmid=$id")->order("id desc")->select();
			$this->assign('volist', $logs);
			$this->assign('check',$_REQUEST[check]);
			$this->assign('approve',$_REQUEST[approve]);
		}

		
		if($_REQUEST[myprojectmanage])
		{
			if($_SESSION["app"])
			{
				$this->display("../Plmmanage/indexapp");
			}
			else
			{
				$this->display("../Plmmanage/index");
			}
			
		}
		else if($_REQUEST[app])
		{
			if($_REQUEST[tab]=="5")
			{
				$this->display("../App/gante");
			}
			else
			{
				$this->display("../App/plmdetail");
			}
		}
		else
		{
			$this->display("../Xmtj/plmdetail");
		}
		
	}
	
	
	public function subworkganteiframe() {
		
		$id=$_REQUEST[id];
		$worktype=$_REQUEST[worktype];
		$app=$_REQUEST[app];
		$this->assign('id', $id);
		$this->assign('worktype', $worktype);
		$this->assign('app', $app);
		$this->display("../Index/subworkganteiframe");
		//Header("Location:http://49.4.69.189:8083/jinjiniao/Rbac/index.php/Xmtj/subworkgante/id/".$_REQUEST['id']."/worktype/".$_REQUEST['worktype']);
       // return;
	}
	public function subworkgante() {
		
		$model=M("Project");
		$id=$_REQUEST[id];
		$app=$_REQUEST[app];
		$map[id]=$_REQUEST[id];
		$detail=$model->where($map)->find();
		$this->assign('detail', $detail);
		$mapforPlmschedule[plmid]=$_REQUEST[id];
		$mapforPlmschedule[status]=1;
		$mapforPlmschedule["worktype|subworktype"]=str_replace("▪","",$_REQUEST[worktype]);

		$scheduleworktypes=M("Plmschedule")->where($mapforPlmschedule)->order("sort asc")->select();
		foreach($scheduleworktypes as $key => $val)
		{
			//modify by zcy on 20210626 on project
			$scheduleworktypes[$key][timebegin]=$val["realtimebegin"];
			$scheduleworktypes[$key][timeend]=$val["realtimeend"];
			
			if(!empty($scheduleworktypes[$key][timebegin])&&empty($scheduleworktypes[$key][timeend]))
			{
				$scheduleworktypes[$key][timeend]=date("Y-m-d");
			}
			$scheduleworktypes[$key][percent]=$val["percent"];
			if(empty($scheduleworktypes[$key][percent]))
			{
				$scheduleworktypes[$key][percent]="0%";
			}
			
				
		}
		$this->assign('scheduleworktypes', $scheduleworktypes);
		
		
		if($_REQUEST[app])
		{
			$this->display("../App/subworkgante");
		}
		else
		{
			$this->display("../Index/subworkgante");
		}
		
	}
	
	function diffBetweenTwoDays ($day1, $day2)
	{
	  $second1 = strtotime($day1);
	  $second2 = strtotime($day2);
		
	  if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	  }
	  return 1+(($second1 - $second2) / 86400);
	}
	
	function setuser() {
		$name = "Project";
		$model = M($name);
		$this->findRelativePersons();
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		$this->assign('orgdata', $vo);
		$this->assign('vo', $vo);
		$this->assign('type', $_REQUEST[type]);
		$this->display();
	}
	function setusersubmit() {

		$name = "Project";
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$list = $model->save();
		if(!empty($_SESSION[app]))
		{
			$this->redirect('../App/xmff');
		}
		else
		{
			$this->redirect('../Xmff/index');
		}
		
		// 更新数据
		/*
		$taskid=$model->id;
		$model->secondcreate_time=time();
		$model->last_time=time();
		$date=date('Y-m-d H:i:s');
		$info = M("Project")->where("id='" . $model->id . "'")->find();
		$model->handlehistory=$info['handlehistory'].$_SESSION['loginUserName']."于".$date."修改了项目立项</br>------------------</br>"; 
		if($_REQUEST[waysub]!="")
		{
			$model->waysub=$_REQUEST[waysub];
		}
		if($_REQUEST[activity]!=""){
			$model->activity=$_REQUEST["activity"];
		}

		$address=$model->title;
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			
			$date=date('m-d H:i');
			$data['content']=$_SESSION['loginUserName']."于".$date."修改了《".$address."》项目立项，请您审核。";
			$data['href'] ="index.php?s=Jypg/index";
			$data['taskid'] =$taskid;
			$data['type'] ="Jypg";
			//$userschedule=$this->findUserByRole("营销部经理");
			//英达热再生这里不会走到
			$userschedule=$this->findUserByAccount("zhourong");
			$data['user']=$userschedule['nickname'].$userschedule['number'];
	    	$this->Addschedule($data);
			
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('项目立项成功!');
		} else {
			//错误提示
			$this->error('项目立项失败!');
		}
		*/
	}
	
	function array_unique_fb($array2D){
	 foreach ($array2D as $v){
	  $v=join(',',$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
	  $temp[]=$v;
	 }
	 $temp=array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
	 foreach ($temp as $k => $v){
	  $temp[$k]=explode(',',$v); //再将拆开的数组重新组装
	 }
	 return $temp;
	}
	
	public function ajaxgroup()
	{
		// $map[design_status]=array('neq','完成验收');
		$map['plmid']=$_POST[plmid];
		$groups=M("Plmgroup")->where($map)->select();
	    echo json_encode($groups);
	}
	public function ajaxstep()
	{
		// $map[design_status]=array('neq','完成验收');
		$map['plmid']=$_POST[plmid];
		$steps=M("Plmworktype")->where($map)->group("pworktype")->select();
	    echo json_encode($steps);
	}
	function printDates($start,$end){
		
		$dt_start = strtotime($start);
		$dt_end = strtotime($end);
		$i=0;
		while ($dt_start<=$dt_end){
			$datearray[$i]=date('Y-m-d',$dt_start);
			$i++;
			$dt_start = strtotime('+1 day',$dt_start);
		}
		return $datearray;
	}
	
	function getBegintime($begintime,$currentfinishtime)
	{
		if($begintime!=0)
		{
			if($begintime==$currentfinishtime)
			{
				echo date('Y-m-d',$begintime);
			}
			else
			{
				echo date('Y-m-d',$begintime+24*60*60);
			}
		}
	}
	
	public function deleteunion($modelname,$para) {
		
		$map[$para]=$_REQUEST["id"];
		$idarray=M($modelname)->where($map)->field("id")->select();
		$ids="";
		foreach($idarray as $key => $val)
		{
			$ids.=$val["id"].",";
		}
		$map["id"]=array("in",$ids);
		M($modelname)->where($map)->delete();
		
	}
	
	
	/**
	* 求取从某日起经过一定天数后的日期,
	* 排除周六周日和节假日加上调休日
	* @param $start    int|string   开始日期
	* @param $offset   int   经过天数
	* @param $exception string|array 节假日
	* @param $allow   string|array    调休日
	* @return string
	*  examples:输入(2021-01-09,36,''),得到2021-03-09
	*/
	function testgetendday( $start='now', $offset=0, $exception='', $allow='' )
	{
		echo $this->getendday("2021-08-30",10);
	
	}
	function getendday( $start='now', $offset=0, $exception='', $allow='' ,$weekend)
	{
		date_default_timezone_set('prc');

		//先计算不排除周六周日及节假日的结果
		if (is_numeric($start)){
			$starttime = $start;
		}else{
			$starttime = strtotime($start);
		}

		$endtime = $starttime + $offset * 24 * 3600;
		$end = date('Y-m-d', $endtime);
		//然后计算周六周日引起的偏移
		if(!empty($weekend))
		{
			return $end;
		}
		
		
		$weekday = date('w', $starttime);//得到星期值：0-6
		if ($weekday == 0){
			//0是星期天
			$weekday = 7;
		}

		$remain = $offset % 7;
		$newoffset = 2 * ($offset - $remain) / 7;//每一周需重新计算两天

		if ($remain > 0) {//周余凑整
			$tmp = $weekday + $remain;
			if ($tmp >= 7) {
				$newoffset += 2;
			} else if ($tmp == 6) {
				$newoffset += 1;
			}

			//考虑当前为周六周日的情况
			if ($weekday == 6) {
				$newoffset -= 1;
			} else if ($weekday == 7) {
				$newoffset -= 2;
			}
		}

		//再计算节假日引起的偏移
		if (is_array($exception)) {//多个节假日
			foreach ($exception as $day) {
				$tmp_time = strtotime($day);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {//在范围(a,b]内
					$weekday_t = date('w', $tmp_time);
					if ($weekday_t <= 5 && $weekday_t != 0) {//防止节假日与周末重复
						$newoffset += 1;
					}
				}
			}
		}else {//单个节假日
			if (!empty($exception)) {
				$tmp_time = strtotime($exception);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {
					$weekday_t = date('w', $tmp_time);
					if ($weekday_t <= 5 && $weekday_t != 0) {
						$newoffset += 1;
					}
				}
			}

		}

		 //再计算调休日引起的偏移
		if (is_array($allow)) {//多个调休日

			foreach ($allow as $day) {
				$tmp_time = strtotime($day);

				if ($tmp_time > $starttime && $tmp_time <= $endtime) {//在范围(a,b]内
					$weekday_t =  date('w', $tmp_time);//得到星期值：0-6

					if ($weekday_t == 6 || $weekday_t == 0) {
						//若调休日是星期六、星期日
						$newoffset -= 1;
					}
				}
			}

		}else {//单个调休日
			if (!empty($allow)) {
				$tmp_time = strtotime($allow);
				if ($tmp_time > $starttime && $tmp_time <= $endtime) {
					$weekday_t = date('w', $tmp_time);

					if ($weekday_t == 6 || $weekday_t == 0) {
						//若调休日是星期六、星期日
						$newoffset -= 1;
					}
				}
			}

		}

		//根据偏移天数，递归做等价运算
		if($newoffset > 0){
			#echo "[{$start} -> {$offset}] = [{$end} -> {$newoffset}]"."<br />n";
			return $this->getendday($end,$newoffset,$exception,$allow);
		}else{
			return $end;
		}
	}
	
	function strFilter($str){
		$str = str_replace('%', '', $str);
		$str = str_replace('&', '', $str);
		$str = str_replace('=', '', $str);
		$str = str_replace('\\', '', $str);
		$str = str_replace('\'', '', $str);
		$str = str_replace('"', '', $str);
		$str = str_replace('“', '', $str);
		$str = str_replace('”', '', $str);
		$str = str_replace('<', '', $str);
		$str = str_replace('>', '', $str);
		$str = str_replace('/', '', $str);
		$str = str_replace('?', '', $str);
		return trim($str);
	}
	
	function sanitize($dat) {
	   return $dat;
	}
	
	function strFilter_1($str){
		$str = str_replace('%', '', $str);
		$str = str_replace('\\', '', $str);
		$str = str_replace('\'', '', $str);
		$str = str_replace('"', '', $str);
		$str = str_replace('“', '', $str);
		$str = str_replace('”', '', $str);
		$str = str_replace('<', '', $str);
		$str = str_replace('>', '', $str);
		return trim($str);
	}
	
	function SendMailOut($address,$title,$message)
	{
		vendor('PHPMailer.class#phpmailer');
		$mail=new PHPMailer();
		// 设置PHPMailer使用SMTP服务器发送Email
		$mail->IsSMTP();
		// 设置邮件的字符编码，若不指定，则为'UTF-8'
		$mail->CharSet='UTF-8';
		// 添加收件人地址，可以多次使用来添加多个收件人
		$mail->AddAddress($address);
		// 设置邮件正文
		$mail->Body=$message;
		// 设置邮件头的From字段。
		$mail->From=C('MAIL_ADDRESS');
		// 设置发件人名字
		$mail->FromName='会e家';
		// 设置邮件标题
		$mail->Subject=$title;
		// 设置SMTP服务器。
		$mail->Host=C('MAIL_SMTP');
		// 设置为“需要验证”
		$mail->SMTPAuth=true;
		// 设置用户名和密码。
		$mail->Username=C('MAIL_LOGINNAME');
		$mail->Password=C('MAIL_PASSWORD');
		// 发送邮件。
		return($mail->Send());
	}
}

?>