<?php
// 角色模块
class RoleAction extends CommonAction {
	function _filter(&$map){
		$map['name'] = array('like',"%".$_POST['name']."%");
		$this->assign('name', $_POST['name']);
	}
     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
	
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
			$this->_list($model, $map,"name");
		}
		
		
		$roles=M("Role")->order("name asc")->select();
		$this->assign('roles', $roles);
		
		if($_SESSION[skin]!=3)
		{
			$this->display(indexoa);
		}
		else
		{
			$this->display(index);
		}
		return;
	}
	public function org() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}
		$name = $this->getActionName();
		$model = D($name);
		if (!empty($model)) {
			$this->_list1($model, $map,"sort",true);
		}
		$this->display();
		return;
	}
	
	function insert() {
		//B('FilterString');
		$name = $this->getActionName();
		$model = D($name);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		//保存当前数据对象
		
		$access=D("Access");
		$node=D("Node");
		$model->status=1;
		$list = $model->add();
		if ($list !== false) { //保存成功		

			$accessdata[role_id]=$list;
			$nodedata=$node->getField("id,pid,level");
			foreach ($nodedata as $key=>$vo)
			{
				$accessdata[node_id]=$key;
				$accessdata[level]=$vo[level];
				$accessdata[pid]=$vo[pid];
				$access->add($accessdata);
			}/*这一段*/		
			
			/////$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->redirect('index');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
    public function setApp()
    {
        $id     = $_POST['groupAppId'];
		$groupId	=	$_POST['groupId'];
		$group    =   D('Role');
		$group->delGroupApp($groupId);
		$result = $group->setGroupApps($groupId,$id);

		if($result===false) {
			$this->error('项目授权失败！');
		}else {
			$this->success('项目授权成功！');
		}
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function app()
    {
        //读取系统的项目列表
        $node    =  D("Node");
        $list	=	$node->where('level=1')->field('id,title')->select();
		foreach ($list as $vo){
			$appList[$vo['id']]	=	$vo['title'];
		}

        //读取系统组列表
		$group   =  D('Role');
        $list       =  $group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        //获取当前用户组项目权限信息
        $groupId =  isset($_GET['groupId'])?$_GET['groupId']:'';
		$groupAppList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的操作权限列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$groupAppList[$vo['id']]	=	$vo['id'];
			}
		}
		$this->assign('groupAppList',$groupAppList);
        $this->assign('appList',$appList);
        if($_SESSION[skin]!=3)
        {
        	$this->display(appoa);
        }
        else
        {
        	$this->display(app);
        }

        return;
    }

     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setModule()
    {
        $id     = $_POST['groupModuleId'];
		$groupModuleIdEdit     = $_POST['groupModuleIdEdit'];
		$groupModuleIdApprove     = $_POST['groupModuleIdApprove'];
		
		$groupId	=	$_POST['groupId'];
        $appId	=	$_POST['appId'];
		$group    =   D("Role");
		$group->delGroupModule($groupId,$appId);
		$result = $group->setGroupModules($groupId,$id,$groupModuleIdEdit);
		
		$data[role_id]=$_POST['groupId'];
		$data[node_id]=999999;
		M("Access")->where($data)->delete();
		
		$moduleIdList=$id;
		
		foreach($moduleIdList as $key =>$val)
		{
			/*
			if($groupModuleIdEdit[$key]==$val)
			{
				$map1[role_id]=$_POST['groupId'];
				$map1[node_id]=$val;
				$map[id]=M("Access")->where($map1)->getField("id");
				
				M("Access")->where($map)->setField("type","2");
				
			}
			if($groupModuleIdApprove[$key]==$val)
			{
				$map1[role_id]=$_POST['groupId'];
				$map1[node_id]=$val;
				$map[id]=M("Access")->where($map1)->getField("id");
				
				M("Access")->where($map)->setField("approve","3");
				
			}
			*/
			
			foreach($groupModuleIdEdit as $key1 =>$val1)
			{
				if($groupModuleIdEdit[$key1]==$val)
				{
					$map1[role_id]=$_POST['groupId'];
					$map1[node_id]=$val;
					$map[id]=M("Access")->where($map1)->getField("id");
					
					M("Access")->where($map)->setField("type","2");
					break;
				}
			}
			foreach($groupModuleIdApprove as $key1 =>$val1)
			{
				if($groupModuleIdApprove[$key1]==$val)
				{
					$map1[role_id]=$_POST['groupId'];
					$map1[node_id]=$val;
					$map[id]=M("Access")->where($map1)->getField("id");
					
					M("Access")->where($map)->setField("approve","3");
					break;
				}
			}
		}
		//dump($moduleIdList);
		//dump($groupModuleIdEdit);
		//dump($groupModuleIdApprove);
		//return;
		M("Role")->where("id=".$_POST['groupId'])->setField("set_time",time());
		if($result===false) {
			$this->error('模块授权失败！');
		}else {
			$this->redirect('index');
		}
    }


    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function module()
    {
		
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];

		$this->assign("groupId",$groupId);
		$this->assign("appId",$appId);
		$group   =  D("Role");
		$roleinfo=M("Role")->where("id=".$groupId)->find();
		$this->assign("roleinfo",$roleinfo);
        //读取系统组列表
        $list=$group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        $node    =  D("Node");
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的模块列表
			$where['level']=2;
			$where['pid']=$appId;
			$where['status']=1;
			/*
            $nodelist=$node->order("group_id asc")->field('id,title,group_id')->where($where)->select();
			foreach ($nodelist as $vo){
				$mapgroup[id]=$vo['group_id'];
				$mapgroup[status]=1;
				if(M("Group")->where($mapgroup)->find())
					$moduleList[$vo['id']]	=	$vo['title'];
			}*/
			
			$nodelist=$node->order("sort asc")->field('id,title,group_id')->where($where)->select();
			foreach ($nodelist as $vo){
				$mapgroup[id]=$vo['group_id'];
				$mapgroup[status]=1;
				if(M("Group")->where($mapgroup)->find())
				{
					if($vo['group_id']==1031)$moduleList1[$vo['id']]	=	$vo['title'];//我的项目
					if($vo['group_id']==100)$moduleList2[$vo['id']]	=	$vo['title'];//我的工作
					if($vo['group_id']==206)$moduleList3[$vo['id']]	=	$vo['title'];//施工准备
					if($vo['group_id']==104)$moduleList4[$vo['id']]	=	$vo['title'];//项目开发
					if($vo['group_id']==108)$moduleList5[$vo['id']]	=	$vo['title'];//项目设计
					if($vo['group_id']==105)$moduleList6[$vo['id']]	=	$vo['title'];//项目采购
					if($vo['group_id']==107)$moduleList7[$vo['id']]	=	$vo['title'];//项目施工
					if($vo['group_id']==207)$moduleList8[$vo['id']]	=	$vo['title'];//项目验收
					if($vo['group_id']==199)$moduleList9[$vo['id']]	=	$vo['title'];//会议管理
					if($vo['group_id']==200)$moduleList10[$vo['id']]	=	$vo['title'];//运营分析
					if($vo['group_id']==201)$moduleList11[$vo['id']]	=	$vo['title'];//报表统计
					if($vo['group_id']==2)$moduleList12[$vo['id']]	=	$vo['title'];//配置中心
					if($vo['group_id']==210)$moduleList210[$vo['id']]	=	$vo['title'];//报表统计
					
				}
			}
			
        }

        //获取当前项目的授权模块信息
		$groupModuleList = array();
		$groupModuleListtype = array();
		$groupModuleListApprove = array();
		if(!empty($groupId) && !empty($appId)) {
            $grouplist	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($grouplist as $vo){
				$groupModuleList[$vo['id']]	=	$vo['id'];
				$groupModuleListType[$vo['id']]	=	$vo['type'];
				$groupModuleListApprove[$vo['id']]	=	$vo['approve'];
			}
		}
		//合同版块
		//项目版块
		//材料版块
		//账号管理
		//配置中心

		$this->assign('groupModuleList',$groupModuleList);
		$this->assign('groupModuleListType',$groupModuleListType);
		$this->assign('groupModuleListApprove',$groupModuleListApprove);
        $this->assign('moduleList1',$moduleList1);
		$this->assign('moduleList1_1',$moduleList1_1);
		$this->assign('moduleList1_2',$moduleList1_2);
		$this->assign('moduleList2',$moduleList2);
		$this->assign('moduleList3',$moduleList3);
		$this->assign('moduleList4',$moduleList4);
		$this->assign('moduleList5',$moduleList5);
		$this->assign('moduleList6',$moduleList6);
		$this->assign('moduleList7',$moduleList7);
		$this->assign('moduleList8',$moduleList8);
		$this->assign('moduleList9',$moduleList9);
		$this->assign('moduleList10',$moduleList10);
		$this->assign('moduleList11',$moduleList11);
		$this->assign('moduleList12',$moduleList12);
		$this->assign('moduleList13',$moduleList13);
		$this->assign('moduleList14',$moduleList14);
		$this->assign('moduleList15',$moduleList15);
		$this->assign('moduleList210',$moduleList210);

		$mapforAccessx[node_id]="999999";
		$mapforAccessx[role_id]=$_GET['groupId'];
		$ifexist=M("Access")->where($mapforAccessx)->find();
		if(!empty($ifexist))
		{
			$this->assign('hetongshenpi',"1");
		}
		
		if($_SESSION[skin]!=3)
		{
			$this->display(moduleoa);
		}
		else
		{
        	$this->display(module);
		}

        return;
    }
	
	
	
	
	

    public function modulepiliang()
    {
		
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];

		$this->assign("groupId",$groupId);
		$this->assign("appId",$appId);
		$group   =  D("Role");
        //读取系统组列表
        $list=$group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        $node    =  D("Node");
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的模块列表
			$where['level']=2;
			$where['pid']=$appId;
			$where['status']=1;
			/*
            $nodelist=$node->order("group_id asc")->field('id,title,group_id')->where($where)->select();
			foreach ($nodelist as $vo){
				$mapgroup[id]=$vo['group_id'];
				$mapgroup[status]=1;
				if(M("Group")->where($mapgroup)->find())
					$moduleList[$vo['id']]	=	$vo['title'];
			}*/
			
			$nodelist=$node->order("sort asc")->field('id,title,group_id')->where($where)->select();
			foreach ($nodelist as $vo){
				$mapgroup[id]=$vo['group_id'];
				$mapgroup[status]=1;
				if(M("Group")->where($mapgroup)->find())
				{
					if($vo['group_id']==1031)$moduleList1[$vo['id']]	=	$vo['title'];//我的项目
					if($vo['group_id']==100)$moduleList2[$vo['id']]	=	$vo['title'];//我的工作
					if($vo['group_id']==206)$moduleList3[$vo['id']]	=	$vo['title'];//施工准备
					if($vo['group_id']==104)$moduleList4[$vo['id']]	=	$vo['title'];//项目开发
					if($vo['group_id']==108)$moduleList5[$vo['id']]	=	$vo['title'];//项目设计
					if($vo['group_id']==105)$moduleList6[$vo['id']]	=	$vo['title'];//项目采购
					if($vo['group_id']==107)$moduleList7[$vo['id']]	=	$vo['title'];//项目施工
					if($vo['group_id']==207)$moduleList8[$vo['id']]	=	$vo['title'];//项目验收
					if($vo['group_id']==199)$moduleList9[$vo['id']]	=	$vo['title'];//会议管理
					if($vo['group_id']==200)$moduleList10[$vo['id']]	=	$vo['title'];//运营分析
					if($vo['group_id']==201)$moduleList11[$vo['id']]	=	$vo['title'];//报表统计
					if($vo['group_id']==2)$moduleList12[$vo['id']]	=	$vo['title'];//配置中心
				}
			}
			
			
        }

        //获取当前项目的授权模块信息
		$groupModuleList = array();
		$groupModuleListtype = array();
		$groupModuleListApprove = array();
		if(!empty($groupId) && !empty($appId)) {
            $grouplist	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($grouplist as $vo){
				$groupModuleList[$vo['id']]	=	$vo['id'];
				$groupModuleListType[$vo['id']]	=	$vo['type'];
				$groupModuleListApprove[$vo['id']]	=	$vo['approve'];
			}
		}
		//合同版块
		//项目版块
		//材料版块
		//账号管理
		//配置中心

		$this->assign('groupModuleList',$groupModuleList);
		$this->assign('groupModuleListType',$groupModuleListType);
		$this->assign('groupModuleListApprove',$groupModuleListApprove);
        $this->assign('moduleList1',$moduleList1);
		$this->assign('moduleList1_1',$moduleList1_1);
		$this->assign('moduleList2',$moduleList2);
		$this->assign('moduleList3',$moduleList3);
		$this->assign('moduleList4',$moduleList4);
		$this->assign('moduleList5',$moduleList5);
		$this->assign('moduleList6',$moduleList6);
		$this->assign('moduleList7',$moduleList7);
		$this->assign('moduleList8',$moduleList8);
		$this->assign('moduleList9',$moduleList9);
		$this->assign('moduleList10',$moduleList10);
		$this->assign('moduleList11',$moduleList11);
		$this->assign('moduleList12',$moduleList12);
		$this->assign('moduleList13',$moduleList13);
		$this->assign('moduleList14',$moduleList14);
		$this->assign('moduleList15',$moduleList15);

		
		$this->display();
		
        return;
    }
	
	
	public function setModulepiliang()
    {
        $id     = $_POST['groupModuleId'];
		$groupModuleIdEdit     = $_POST['groupModuleIdEdit'];
		$groupModuleIdApprove     = $_POST['groupModuleIdApprove'];
		
		$groupId	=	$_POST['groupId'];
        $appId	=	$_POST['appId'];
		$group    =   D("Role");
		
		
		$groupidarray=explode(",",$groupId);
		foreach($groupidarray as $key =>$val)
		{
			if(!empty($val))
			{
				$group->delGroupModule($val,$appId);
			}
		}
	
		foreach($groupidarray as $key =>$val)
		{
			if(!empty($val))
			{
				$group->setGroupModules($val,$id,$groupModuleIdEdit);
			}
		}
	
		$data[role_id]=array("in",$_POST['groupId']);
		$data[node_id]=999999;
		M("Access")->where($data)->delete();
		
		$moduleIdList=$id;
		
		foreach($moduleIdList as $key =>$val)
		{
			
			foreach($groupModuleIdEdit as $key1 =>$val1)
			{
				if($groupModuleIdEdit[$key1]==$val)
				{
					$map1[role_id]=array("in",$_POST['groupId']);
					$map1[node_id]=$val;
					$idarray=M("Access")->where($map1)->field("id")->select();
					$ids="";
					foreach($idarray as $key3 =>$val3)
					{
						$ids.=$val3["id"].",";
					}
					$map["id"]=array("in",$ids);
					M("Access")->where($map)->setField("type","2");
					break;
				}
			}
			foreach($groupModuleIdApprove as $key1 =>$val1)
			{
				if($groupModuleIdApprove[$key1]==$val)
				{
					$map1[role_id]=array("in",$_POST['groupId']);
					$map1[node_id]=$val;
					$idarray=M("Access")->where($map1)->field("id")->select();
					$ids="";
					foreach($idarray as $key3 =>$val3)
					{
						$ids.=$val3["id"].",";
					}
					$map["id"]=array("in",$ids);
					M("Access")->where($map)->setField("approve","3");
					break;
				}
			}
		}
		
		$mapforRole["id"]=array("in",$_POST['groupId']);
		M("Role")->where($mapforRole)->setField("set_time",time());
		if($result===false) {
			$this->error('模块授权失败！');
		}else {
			$this->redirect('index');
		}
    }
	
     /**
     +----------------------------------------------------------
     * 增加组操作权限
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setAction()
    {
        $id     = $_POST['groupActionId'];
		$groupId	=	$_POST['groupId'];
        $moduleId	=	$_POST['moduleId'];
		$group    =   D("Role");
		$group->delGroupAction($groupId,$moduleId);
		$result = $group->setGroupActions($groupId,$id);

		if($result===false) {
			$this->error('操作授权失败！');
		}else {
			$this->success('操作授权成功！');
		}
    }

    public function setActionlot()
    {
    	$value     = $_REQUEST['value'];
    	$value = str_replace("null","",$value);
		$access = D("Access");
    	$str=explode(";",$value);
    	
    	$len=count($str);
    	
    	for($i=0;$i<$len;$i++)
    	{
    		$str1=explode(",",$str[$i]);
    		$flag=$str1[0];
    		$map[role_id]=$str1[1];
    		$map[node_id]=$str1[2];
    		$map[level]=$str1[3];
    		$map[pid]=$str1[4];
    		if($flag=='0')
    		{
    			$result=$access->where($map)->delete();
    		}
    		else
    		{
    			$result=$access->add($map);
    		}
    	}
    	$this->success('操作授权成功！');
    }
    
    

    /**
     +----------------------------------------------------------
     * 组操作权限列表
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function action()
    {
        $groupId =  $_GET['groupId'];
        $appId  = $_GET['appId'];
        $moduleId  = $_GET['moduleId'];

		$group   =  D("Role");
        //读取系统组列表
        $grouplist=$group->field('id,name')->select();
		foreach ($grouplist as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
            //读取系统组的授权项目列表
            $list	=	$group->getGroupAppList($groupId);
			foreach ($list as $vo){
				$appList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("appList",$appList);
        }
        if(!empty($appId)) {
            $this->assign("selectAppId",$appId);
        	//读取当前项目的授权模块列表
            $list	=	$group->getGroupModuleList($groupId,$appId);
			foreach ($list as $vo){
				$moduleList[$vo['id']]	=	$vo['title'];
			}
            $this->assign("moduleList",$moduleList);
        }
        $node    =  D("Node");

        if(!empty($moduleId)) {
            $this->assign("selectModuleId",$moduleId);
        	//读取当前项目的操作列表
			$map['level']=3;
			$map['pid']=$moduleId;
            $list	=	$node->where($map)->field('id,title')->select();
			if($list) {
				foreach ($list as $vo){
					$actionList[$vo['id']]	=	$vo['title'];
				}
			}
        }


        //获取当前用户组操作权限信息
		$groupActionList = array();
		if(!empty($groupId) && !empty($moduleId)) {
			//获取当前组的操作权限列表
            $list	=	$group->getGroupActionList($groupId,$moduleId);
			if($list) {
			foreach ($list as $vo){
				$groupActionList[$vo['id']]	=	$vo['id'];
			}
			}

		}

		$this->assign('groupActionList',$groupActionList);
		//$actionList = array_diff_key($actionList,$groupActionList);
        $this->assign('actionList',$actionList);
		
        if($_SESSION[skin]!=3)
        {
        	$this->display(actionoa);
        }
        else
        {
        	$this->display(action);
        }

        return;
    }

    
    public function impower()
    {
    	$groupId =  $_GET['groupId'];
    	$map[id] = $groupId;
    	$group   =  D("Role");
        //读取系统组列表
        $groupname=$group->where($map)->getField('name');
		$this->assign("groupname",$groupname);
		

		$node    =  D("Node");
		$access  =  D("Access");
		
		$this->assign("selectAppId",1);
		//读取当前项目的授权模块列表
		//$list	=	$group->getGroupModuleList($groupId,1);
		$map1[level]=2;
		$map1[status]=1;
		//$map1[id]=array(array('neq',30),array('neq',40),'and'); 
		$list = $node->where($map1)->select();
		//dump($list);
		foreach ($list as $vo)
		{
			$moduleList[$vo['id']]['title']	=	$vo['title'];
			$moduleList[$vo['id']]['tosave']	=	$groupId.','.$vo['id'].','.'2'.','.'1';		
			$map2[role_id]=$groupId;
			$map2[node_id]=$vo['id'];
			$map2[level]=2;
			$map2[pid]=1;
			$res=$access->where($map2)->select();
			if($res==false)
			{
				$moduleList[$vo['id']][checked]="false";
			}
			else
			{
				$moduleList[$vo['id']][checked]="true";
			}
			
			$map3[pid]=$vo['id'];
			$map3[level]=3;
			
			$action=$node->where($map3)->select();
			foreach ($action as $voa)
			{
				$moduleList[$vo['id']][$voa['id']]['title']=$voa['title'];
				$moduleList[$vo['id']][$voa['id']]['tosave']	=	$groupId.','.$voa['id'].','.'3'.','.$vo['id'];
				$map4[role_id]=$groupId;
				$map4[node_id]=$voa['id'];
				$map4[level]=3;
				$map4[pid]=$vo['id'];
				$res=$access->where($map4)->select();
				if($res==false)
				{
					$moduleList[$vo['id']][$voa['id']][checked]="false";
				}
				else
				{
					$moduleList[$vo['id']][$voa['id']][checked]="true";
				}
				
			}	
			
		}
		$this->assign("moduleList",$moduleList);
		$this->display(action);
    	return;
    }
    
    
    /**
     +----------------------------------------------------------
     * 增加组操作权限
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function setUser()
    {
        $id     = $_POST['groupUserId'];
		$groupId	=	$_POST['groupId'];
		$group    =   D("Role");
		$group->delGroupUser($groupId);
		$result = $group->setGroupUsers($groupId,$id);
		if($result===false) {
			$this->error('授权失败！');
		}else {
			$this->success('授权成功！');
		}
    }

    /**
     +----------------------------------------------------------
     * 组操作权限列表
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function user()
    {
        //读取系统的用户列表
        $user    =   D("User");
		$list2=$user->field('id,account,nickname')->select();
		//echo $user->getlastsql();
		//dump(	$user);
		foreach ($list2 as $vo){
			$userList[$vo['id']]	=	$vo['account'].' '.$vo['nickname'];
		}

		$group    =   D("Role");
        $list=$group->field('id,name')->select();
		foreach ($list as $vo){
			$groupList[$vo['id']]	=	$vo['name'];
		}
		$this->assign("groupList",$groupList);

        //获取当前用户组信息
        $groupId =  isset($_GET['id'])?$_GET['id']:'';
		$groupUserList = array();
		if(!empty($groupId)) {
			$this->assign("selectGroupId",$groupId);
			//获取当前组的用户列表
            $list	=	$group->getGroupUserList($groupId);
			foreach ($list as $vo){
				$groupUserList[$vo['id']]	=	$vo['id'];
			}

		}
		$this->assign('groupUserList',$groupUserList);
        $this->assign('userList',$userList);
        if($_SESSION[skin]!=3)
        {
        	$this->display(useroa);
        }
        else
        {
        	$this->display(user);
        }

        return;
    }
	public function _before_edit(){
	   $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);

	}
	public function _before_add(){
	   $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);

	}
    public function select()
    {
    	$this->assign('arg1', $_GET[arg1]);
    	$this->assign('arg2', $_GET[arg2]);
    	
        $map = $this->_search();
        //创建数据对象
        $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);
        if($_SESSION[skin]!=3)
        {
        	$this->display(selectoa);
        }
        else {
        $this->display(select);
        }
        return;
    }
	
	public function search_list()
	{	 
		if($_SESSION[skin]!=3)
		{
			$this->display(search_listoa);
		}   
		else {
        $this->display(search_list);
		}
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
			$p = new Page($count, 120);//$listRows
			//分页查询数据
			$this->assign("totalCount", $p->totalRows);
			$this->assign("numPerPage", $p->listRows);
			$this->assign("currentPage", $p->nowPage);
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			foreach($voList as $key=>$role) 
			{
			 	if($voList[$key][pid]==0)
			 	{	
			 		$voList[$key][pidname]="无上级职位";
			 	}
			 	else 
			 	{
					$voList[$key][pidname]=M("Role")->where("id=".$voList[$key][pid])->getField("name");
			 	}
			}
			//分页显示
			$page = $p->show();
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
	protected function _list1($model, $map, $sortBy = '', $asc = false) {
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
			$p = new Page($count, 120);//$listRows
			//分页查询数据
			$this->assign("totalCount", $p->totalRows);
			$this->assign("numPerPage", $p->listRows);
			$this->assign("currentPage", $p->nowPage);
			$voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ($map as $key => $val) {
				if (!is_array($val)) {
					$p->parameter .= "$key=" . urlencode($val) . "&";
				}
			}
			//分页显示
			$page = $p->show();
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
	public function getRoleNameById($roleId)
	{
		$role=M("role");
		return $role->getFieldById($roleId,'name');
	}
	public function getRoleLevelById($roleId)
	{
		$role=M("role");
		return $role->getFieldById($roleId,'level');
	}
	
	public function add() {
		if($_SESSION[skin]!=3)
		{
			$this->display(addoa);
		}
		else
		{
		$this->display(add);
		}
	}
	
	
	function edit() {
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
		$this->display(edit);
		}
	}
	
	function ajax(){
		M("role")->where("id='".htmlspecialchars($_REQUEST[id])."'")->delete();
		echo json_encode(htmlspecialchars($_REQUEST[id]));
	}
	
}
?>
