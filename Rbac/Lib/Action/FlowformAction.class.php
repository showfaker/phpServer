<?php
class FlowformAction extends CommonAction {
	//过滤查询字段
	function _filter(&$map){
		$map['serial'] = array('like',"%".$_POST['keyword']."%");
		$this->assign("keyword",$_POST['keyword']);
	}

	public function search_list()
	{	    
        $this->display();
	}
	
	public function index() {
		//列表过滤器，生成查询Map对象
		//$this->getnumber();
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if(!empty($_REQUEST['approvestatus']))
		{
			$map['approvestatus'] = $_REQUEST['approvestatus'];
			$this->assign('approvestatus',$_REQUEST['approvestatus']);	
		}
		if(!empty($_REQUEST['user']))
		{
			$map['user'] = array("like","%".$_REQUEST['user']."%");
			$this->assign('user',$_REQUEST['user']);
		}
		if($_REQUEST['status']=="正常")
		{
			$map['status'] = 1;
			$this->assign('status',$_REQUEST['status']);	
		}
		if($_REQUEST['status']=="禁用")
		{
			$map['status'] = array("neq",1);
			$this->assign('status',$_REQUEST['status']);	
		}
		if(!empty($_REQUEST['para']))
		{
			$para=str_replace("~!@","/",$_REQUEST['para']);
			$para=str_replace("@!~","&",$para);
			$tablename["name"] = $_REQUEST["nodename"];
			$tabletitle=M("Node")->where($tablename)->getField("title");
			$mapforsetting[title]=array("eq",$tabletitle);
			$mapforsetting[status]=1;
			$setting=M("Settingform")->where($mapforsetting)->find();
			$parakey=0;
			
			for($parakey=0;$parakey<=30;$parakey++)
			{
				$str="para".$parakey;
				if($setting[$str]==$para)
				{
					break;
				}
			}
			if($parakey!=30)
			{
				$map[$str] = array("like","%".$_REQUEST['searchtitle']."%");
				$this->assign('searchtitle',$_REQUEST['searchtitle']);
			}
			$_SESSION[searchstr]=$str;
		}
		if(!empty($_REQUEST['searchtitle']))
		{
			$map[$_SESSION[searchstr]] = array("like","%".$_REQUEST['searchtitle']."%");
			$this->assign('searchtitle',$_REQUEST['searchtitle']);
		}
		
		if(empty($setting))
		{
			$tablename["name"] = $_REQUEST["nodename"];
			$tabletitle=M("Node")->where($tablename)->getField("title");
			$mapforsetting[title]=array("eq",$tabletitle);
			$mapforsetting[status]=1;
			$setting=M("Settingform")->where($mapforsetting)->find();
		}
		for($parakey=0;$parakey<=30;$parakey++)
		{
			if(!empty($_REQUEST['parasearch'.$parakey]))
			{
				$map[$setting["tableparaname".$parakey]] = array("like","%".$_REQUEST['parasearch'.$parakey]."%");
				$parasearch[$parakey]=$_REQUEST['parasearch'.$parakey];
			}
			if(!empty($_REQUEST['timebegin'.$parakey]))
			{
				if((!empty($_REQUEST['timebegin'.$parakey]))&&(empty($_REQUEST['timeend'.$parakey])))
				$map[$setting["tableparaname".$parakey]] = array('egt',($_REQUEST['timebegin'.$parakey]));
				else if((empty($_REQUEST['timebegin'.$parakey]))&&(!empty($_REQUEST['timeend'.$parakey])))
				$map[$setting["tableparaname".$parakey]] = array('elt',($_REQUEST['timeend'.$parakey]));
				else if((!empty($_REQUEST['timebegin'.$parakey]))&&(!empty($_REQUEST['timeend'.$parakey])))
				$map[$setting["tableparaname".$parakey]] = array(array('egt',($_REQUEST['timebegin'.$parakey])),array('elt',($_REQUEST['timeend'.$parakey])),'and');
			
				$timebeginsearch[$parakey]=$_REQUEST['timebegin'.$parakey];
				$timeendsearch[$parakey]=$_REQUEST['timeend'.$parakey];
			}
		}
		$this->assign('parasearch',$parasearch);
		$this->assign('timebeginsearch',$timebeginsearch);
		$this->assign('timeendsearch',$timeendsearch);
		
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);

		
		if(($_SESSION[name]!="管理员"))
		{
			$where[user_id]=array("eq",$_SESSION[number]);//提交人
			$where[copy]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//自动抄送人和手动抄送人
			$where[current]=array("like","%".$_SESSION[name].$_SESSION[number]."%");//当前审批人
			$where[history]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//历史审批人
			$where[relativeexceptlast]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//relativeexceptlast//需要执行人
			$where['_logic'] = 'OR';
			$map['_complex'] = $where;
		}
		
		
		$map[type]=array("eq",$_REQUEST["nodename"]);
		

		$tablename["name"] = $_REQUEST["nodename"];
		$tabletitle=M("Node")->where($tablename)->getField("title");
		
		$mapforsetting[title]=array("eq",$tabletitle);
		$mapforsetting[status]=1;
		$setting=M("Settingform")->where($mapforsetting)->find();
		$this->assign("setting",$setting);
		$this->assign("nodename",$_REQUEST["nodename"]);
		
		
		$this->assign("check",$_REQUEST["check"]);

		$name = "Flow_".$setting["tablename"];
		$model = D($name);
		if (!empty($model)) {
			$this->_list($model, $map,'create_time',false);
		}

		
		$this->display("../Flowform/index");
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
				if(false !== strpos($voList[$vokey][execute_user], $_SESSION[name].$_SESSION[number].","))
				{
					$voList[$vokey][alreadyexecute]=1;
				}
				if(false !== strpos($voList[$vokey][relativeexceptlast], $_SESSION[name].$_SESSION[number].","))
				{
					$voList[$vokey][needexecute]=1;
				}
            }
			
			if($_REQUEST["nodename"]=="Plm")
			{
				foreach ($voList as $vokey => $voval) {
					//$mapforFlow_finance["type"]="投资";
					//$mapforFlow_finance["plmname"]=$voval["name"];
					//$mapforFlow_finance["status"]="1";
					//$voList[$vokey]["dateline"]=M("Flow_finance")->where($mapforFlow_finance)->order("id desc")->getField("dateline");
					
					$mapforFlow_finance1["accounttype"]=array("like","%%");
					$mapforFlow_finance1["type"]="投资";
					$mapforFlow_finance1["plmname"]=$voval["name"];
					$mapforFlow_finance1["status"]="1";
					if((!empty($_REQUEST['timebegin1']))&&(empty($_REQUEST['timeend1'])))
					$mapforFlow_finance1['time'] = array('egt',($_REQUEST['timebegin1']));
					else if((empty($_REQUEST['timebegin1']))&&(!empty($_REQUEST['timeend1'])))
					$mapforFlow_finance1['time'] = array('elt',($_REQUEST['timeend1'])+24*60*60);
					else if((!empty($_REQUEST['timebegin1']))&&(!empty($_REQUEST['timeend1'])))
					$mapforFlow_finance1['time'] = array(array('egt',($_REQUEST['timebegin1'])),array('elt',($_REQUEST['timeend1'])),'and');
					$this->assign('timebegin1', $_REQUEST['timebegin1']);
					$this->assign('timeend1', $_REQUEST['timeend1']);
					
					
					
					//收益和支出不影响净值
					$mapforFlow_finance1["accounttype"]=array("in","损益,转入");
					$income1=M("Flow_finance")->where($mapforFlow_finance1)->sum("money");
					$mapforFlow_finance1["accounttype"]=array("in","转出");
					$income2=M("Flow_finance")->where($mapforFlow_finance1)->sum("money");
					$voList[$vokey]["income"]=$income1-$income2;
					
					
					$mapforFlow_finance2["accounttype"]=array("like","%%");
					$mapforFlow_finance2["payplm"]=array("eq","投资");
					$mapforFlow_finance2["type"]="税筹合同";
					$mapforFlow_finance2["plmname"]=$voval["name"];
					$mapforFlow_finance2["status"]="0";
					$income3=M("Flow_fax")->where($mapforFlow_finance2)->sum("money");
					
					$voList[$vokey]["income"]+=$income3;
				}
			}
			if($_REQUEST["nodename"]=="税筹合同")
			{
				foreach ($voList as $vokey => $voval) {
					
					$data["tax"]=$voval["contractmoney"];//票
					$data["contractmoney"]=$voval["contractmoney"];//公户
					$data["returnmoney"]=$voval["contractmoney"]-$voval["money"]-$voval["taxmoney"];//私户
					
					$mapforFlow_finance['agreement'] = $voval["name"];
					$mapforFlow_finance["status"]=1;
					$taxpay=M("Flow_finance")->where($mapforFlow_finance)->sum("taxpay");
					$contractmoneypay=M("Flow_finance")->where($mapforFlow_finance)->sum("contractmoneypay");
					$returnmoneypay=M("Flow_finance")->where($mapforFlow_finance)->sum("returnmoneypay");
					
					$voList[$vokey]["taxleft"]=$data["tax"]-$taxpay;
					$voList[$vokey]["contractmoneyleft"]=$data["contractmoney"]-$contractmoneypay;
					$voList[$vokey]["returnmoneyleft"]=$data["returnmoney"]-$returnmoneypay;
					
				}
			}
			if($_REQUEST["nodename"]=="借贷设置")
			{
				foreach ($voList as $vokey => $voval) 
				{
					if($voval["loantype"]=="出借")
					{
						//借 贷 收 还 授信
						$mapforFlow_finance3['type'] = "借贷";
						$mapforFlow_finance3["status"]=1;
						$mapforFlow_finance3['accounttype'] = "借";
						//$mapforFlow_finance3['borrower'] = $voval["borrower"];
						$mapforFlow_finance3['borrower'] = $voval["lender-mainbody"];
						$money1=M("Flow_finance")->where($mapforFlow_finance3)->sum("money");
						$mapforFlow_finance3['accounttype'] = "收";
						$money2=M("Flow_finance")->where($mapforFlow_finance3)->sum("money");
						
						$voList[$vokey]["borrowedmoney"]=$money1-$money2;
						$voList[$vokey]["creditmoney"]="";
					}
					if($voval["loantype"]=="贷款")
					{
						//借 贷 收 还 授信
						$mapforFlow_finance4['type'] = "借贷";
						$mapforFlow_finance4["status"]=1;
						$mapforFlow_finance4['accounttype'] = "贷";
						$mapforFlow_finance4['loaner'] = $voval["lender-mainbody"];
						$money1=M("Flow_finance")->where($mapforFlow_finance4)->sum("money");
						$mapforFlow_finance4['accounttype'] = "还";
						$money2=M("Flow_finance")->where($mapforFlow_finance4)->sum("money");
						$mapforFlow_finance4['accounttype'] = "授信";
						$money3=M("Flow_finance")->where($mapforFlow_finance4)->sum("money");
						
						$voList[$vokey]["borrowedmoney"]=$money1-$money2;
						$voList[$vokey]["creditmoney"]=$money3;
					}
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
	
	function find()
	{
		$tablename["name"] = $_REQUEST["nodename"];
		$this->assign("nodename",$_REQUEST["nodename"]);
		$tabletitle=M("Node")->where($tablename)->getField("title");
		$mapforsetting[title]=array("eq",$tabletitle);
		$mapforsetting[status]=1;
		$setting=M("Settingform")->where($mapforsetting)->find();
		$this->assign("setting",$setting);
		$this->display("../Flowform/find");
	}
	function draft() {
	
		
		$this->assign("orgserial",$_REQUEST[orgserial]);
		$this->assign("nodename",$_REQUEST[nodename]);
		
		$tablename["name"] = $_REQUEST[nodename];
		$tabletitle=M("Node")->where($tablename)->getField("title");
		$mapforsetting[title]=array("eq",$tabletitle);
		$mapforsetting[status]=1;

		$setting=M("Settingform")->where($mapforsetting)->find();
		//现在就确定使用哪个模板
		$this->assign("setting",$setting);
		
		for($row=0;$row<=30;$row++)
		{
			if(($setting["pararow".$row])||($setting["pararow".$row]=="0"))
			{
				if(!$line[$setting["pararow".$row]][0])
				{
					$line[$setting["pararow".$row]][0]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire0"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para0"]="para".$row;
					$line[$setting["pararow".$row]]["para0"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["paratype0"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselectarray0"]=explode(",",$setting["paraselect".$row]);
					$line[$setting["pararow".$row]]["paraselect0"]=explode(",",$setting["paraselect".$row]);
					if(($line[$setting["pararow".$row]]["paratype0"]=="100")||($line[$setting["pararow".$row]]["paratype0"]=="102"))
					{
						$line[$setting["pararow".$row]]["paraselect0"]=null;
						$paraselect=explode(",",$setting["paraselect".$row]);
						if($paraselect[2])
						{
							$mapforParaselect[$row][0]['_string'] = $paraselect[2];
						}
						$paraselectarray=M($paraselect[0])->where($mapforParaselect[$row][0])->field($paraselect[1])->select();
						foreach($paraselectarray as $key => $val)
						{
							if($val[$paraselect[1]])
							{
								$line[$setting["pararow".$row]]["paraselect0"][$key]=$val[$paraselect[1]];
							}
						}
					}
					$line[$setting["pararow".$row]]["effectdisplay0"]=$setting["effectdisplay".$row];
				}
				else
				{
					$line[$setting["pararow".$row]][1]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire1"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para1"]="para".$row;
					$line[$setting["pararow".$row]]["para1"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["paratype1"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselectarray1"]=explode(",",$setting["paraselect".$row]);
					$line[$setting["pararow".$row]]["paraselect1"]=explode(",",$setting["paraselect".$row]);
					if(($line[$setting["pararow".$row]]["paratype1"]=="100")||($line[$setting["pararow".$row]]["paratype1"]=="102"))
					{
						$line[$setting["pararow".$row]]["paraselect1"]=null;
						$paraselect=explode(",",$setting["paraselect".$row]);
						if($paraselect[2])
						{
							$mapforParaselect[$row][1]['_string'] = $paraselect[2];
						}
						$paraselectarray=M($paraselect[0])->where($mapforParaselect[$row][1])->field($paraselect[1])->select();
						foreach($paraselectarray as $key => $val)
						{
							if($val[$paraselect[1]])
							{
								$line[$setting["pararow".$row]]["paraselect1"][$key]=$val[$paraselect[1]];
							}
						}
					}
					$line[$setting["pararow".$row]]["effectdisplay1"]=$setting["effectdisplay".$row];
				}
				
			}
		}
		$this->assign('line',$line);
		
		$projects=M("Plm")->select();
		$this->assign('projects',$projects);
		$agreements=M("agreement")->select();
		$this->assign('agreements',$agreements);
		$mapforSb["status"]=3;
		$buys=M("Sb")->where($mapforSb)->select();
		$this->assign('buys',$buys);
		/*
		$mapforserial[type]=$nodename;
		$mapforserial[year]=date("Y",time());
		$serial=strval(M("Flow_".$setting["tablename"])->where($mapforserial)->max("serial"))+1;
		if($serial<10)$serial="00".$serial;
		else if($serial<100)$serial="0".$serial;
		else $serial=$serial;
		
		$this->assign('serial',$serial);
		$this->assign('year',date("Y",time()));
		*/
		
		$this->assign('tablename',$_REQUEST[nodename]);
	
		$roles=M("Role")->where("status=1")->select();
		$this->assign('roles',$roles);
		$this->assign('roles1',$roles);
		$levels=M("Level")->where("status=1")->select();
		$this->assign('levels',$levels);
		$this->assign('levels1',$levels);
		$companys=M("Company")->select();
		$this->assign('companys',$companys);
		$this->assign('companys1',$companys);
		$depts=M("Dept")->select();
		$this->assign('depts',$depts);
		$this->assign('depts1',$depts);
		
		$this->display("../Flowform/draft");
	}
	
	function draftsubmit() {
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		
		$tablename["name"] = $nodename;
		$tabletitle=M("Node")->where($tablename)->getField("title");
		//$mapforsetting[title]=array("eq",$tabletitle);
		for($row=0;$row<=30;$row++)
		{
			if($setting["pararequire".$row])
			{
				if(!$_REQUEST["para".$row])
					$this->error("必须填写".$setting["para".$row]."！");
			}
		}
		
		if(($setting["title"]=="投资项目")||($setting["title"]=="税筹合同"))
		{
			$mapforRepeat['name']=$_POST['name'];
			$repeatinfo=M("Flow_".$setting["tablename"])->where($mapforRepeat)->getField("id");
			if(!empty($repeatinfo))
			{
				$this->error("名称已经存在，请重新输入！");
			}
		}
		
		
		/*特殊处理*/
		if(($setting["title"]=="税筹")&&($_REQUEST["financetype"]!="票"))
		{
			$mapForMainbody["name"]=$_REQUEST["inmainbody"];
			$mainbody=M("Mainbody")->where($mapForMainbody)->find();
			if(!empty($mainbody))
			{
				$mapForAccount["name"]=$_REQUEST["inaccount"];
				$mapForAccount["type"]=array("neq","承兑汇票");
				$account=M("Account")->where($mapForAccount)->find();
				if(empty($account))
				{
					$this->error("入-主体是公司主体，入-账号不是公司账号");
				}
			}
			
			$mapForMainbody["name"]=$_REQUEST["outmainbody"];
			$mainbody=M("Mainbody")->where($mapForMainbody)->find();
			if(!empty($mainbody))
			{
				$mapForAccount["name"]=$_REQUEST["outaccount"];
				$mapForAccount["type"]=array("neq","承兑汇票");
				$account=M("Account")->where($mapForAccount)->find();
				if(empty($account))
				{
					$this->error("出-主体是公司主体，出-账号不是公司账号");
				}
			}
		}
		if(($setting["title"]=="支出")||($setting["title"]=="转账"))
		{
			if($_REQUEST["outaccounttype"]=="承兑汇票")
			{
				$mapForAccount_bill["name"]=$_REQUEST["outaccountbill"];
				$account_bill=M("Account")->where($mapForAccount_bill)->find();
				if($account_bill["money"]!=$_REQUEST["money"])
				{
					$this->error("支出金额必须与承兑汇票的金额一致");
				}
			}
		}
		
		if(($setting["title"]=="投资"))
		{
			$mapforFlow_plm["name"]=$_REQUEST["plmname"];
			$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
			if(!empty($plminfo["currency"]))
			{
				if($plminfo["currency"]!=$_REQUEST["currency"])
				{
					$this->error("币种不一致!");
				}
			}
				
			$mapforFlow_finance["type"]="投资";
			$mapforFlow_finance["plmname"]=$_REQUEST["plmname"];
			$ifexist=M("Flow_finance")->where($mapforFlow_finance)->find();
			if(!empty($ifexist))
			{
				$mapforFlow_plm["name"]=$_REQUEST["plmname"];
				$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
				if($plminfo["currency"]!=$_REQUEST["currency"])
				{
					$this->error("币种不一致");
				}
			}
		}
		
		if(($setting["title"]=="投资"))
		{
			$mapforFlow_plm["name"]=$_REQUEST["plmname"];
			$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
			if(empty($plminfo["currency"]))
			{
				M("Flow_plm")->where("id=".$plminfo["id"])->setField("currency",$_REQUEST["currency"]);
			}
		}
		
		
		
		$tablename = $nodename;
		$name = $nodename;
		$mapfornode[name]=$name;
		$nodetitle=M("Node")->where($mapfornode)->getField("title");
		
		$model = D("Flow_".$setting["tablename"]);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->user=$_SESSION['loginUserName'];
		$model->user_id=$_SESSION['number'];
		$model->create_time=time();
		$model->type=$name;
		$model->templateid=$_REQUEST[templateid];
		$date=date('m-d H:i');
		//$title=$model->title;
		$model->handlehistory=$_SESSION['loginUserName']."(".$_SESSION['ip'].")"."于".$date."创建了《".$nodetitle."》"."</br>------------------</br>"; 
		
		//$approver=explode("-",$_REQUEST["approver"]);
		//$model->current=$approver[2];
		/*
		if(false!==strstr($_REQUEST["approver"],"|"))
		{
			$approverarray=explode("|",$_REQUEST["approver"]);
			foreach($approverarray as $key => $val)
			{
				$approver=explode("-",$val);
				$current_approver.=$approver[2]."|";
			}
		}
		else if(false!==strstr($_REQUEST["approver"],"&"))
		{
			$approverarray=explode("&",$_REQUEST["approver"]);
			foreach($approverarray as $key => $val)
			{
				$approver=explode("-",$val);
				$current_approver.=$approver[2]."&";
			}
		}
		else
		{
			$approver=explode("-",$_REQUEST["approver"]);
			$current_approver=$approver[2]."|";
		}
		*/
		$current_approver=$_REQUEST["approver"];
		$model->current=$current_approver;
		
		$mapforserial[type]=$nodename;
		$mapforserial[year]=date("Y",time());
		$serial=strval(M("Flow_".$setting["tablename"])->where($mapforserial)->max("serial"))+1;
		if($serial<10)$serial="000".$serial;
		else if($serial<100)$serial="00".$serial;
		else if($serial<1000)$serial="0".$serial;
		else $serial=$serial;
		$model->serial=$serial;
		$model->year=date("Y",time());

		for($i=0;$i<=14;$i++)
		{
			if((isset($_FILES['file']['name'][$i]))&&($_FILES['file']['name'][$i]==""))
			{
				$errormsg='请上传第'.($i+1).'行的图片';
				$this->error($errormsg);
			}
			if(!empty($_FILES['file']['name'][$i]))
			{
				$savePath = '../Public/Uploads/';
				$filename=$_FILES['file']['name'][$i];
				$size = $_FILES['file']['size'][$i]; //文件大小
				$ext = strtolower(end(explode(".",basename($filename))));
				$uuid=uniqid(rand(), false);
				$newname = $uuid.'.'.$ext;
				$upload_file = $savePath.$newname;
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				move_uploaded_file($_FILES['file']['tmp_name'][$i],$upload_file);
				$tempname="file".$i;
				$model->$tempname=$newname;
				$tempname="filerealname".$i;
				$model->$tempname=$filename;
				$model->annexhistory.=$_SESSION['loginUserName']."(".$_SESSION['ip'].")"."于".$date."添加了文档《".$filename."》"."</br>------------------</br>"; 
			}
		}
		
		
		/*上传图片*/
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		header('Content-Type:text/html;charset=UTF-8');
		$img = $_POST['base64'];
		if (!empty($img)){
			$savePath = '../Public/Uploads/';
			$uuid=uniqid(rand(), false);
			$target = $savePath.$uuid.'.jpg';
			if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
				$img = base64_decode($matches[2]);
				file_put_contents($target, $img);
			}
			$filename=$uuid.'.jpg';
			$newname=$filename;
			$model->file=$newname;
		}
		for($i=1;$i<=5;$i++)
		{
			$img = $_POST['base64_'.$i];
			if (!empty($img)){
				$savePath = '../Public/Uploads/';
				$uuid=uniqid(rand(), false);
				$target = $savePath.$uuid.'.jpg';
				if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
					$img = base64_decode($matches[2]);
					file_put_contents($target, $img);
				}
				$filename=$uuid.'.jpg';
				$newname=$filename;
				$tablefilename="file".$i;
				$model->$tablefilename=$newname;
				$dataAccount[$tablefilename]=$newname;
			}
		}
		
		
		
		
		$model->status=1;
		//$model->copy="";
		//$model->submitcopy=$_REQUEST[copy];
		$list = $model->add();
		
		/*
		$mapfordept[name]=array("eq",$approver[0]);
		$deptid=M("Dept")->where($mapfordept)->getField("id");
		$mapforrole[name]=array("eq",$approver[1]);
		$roleid=M("Role")->where($mapforrole)->getField("id");
		
		$mapforuser["nickname"]=array("eq",$approver[2]);
		$mapforuser["department"]=array("eq",$deptid);
		$mapforuser["position"]=array("eq",$roleid);
		$userschedule=M("User")->where($mapforuser)->find();
		*/
		if($_REQUEST[$tablename."onlysave"]!="1")//如果不是保存
		{
			
			
		}
		else
		{
			$info=M("Flow_".$setting["tablename"])->where("id=".$list)->find();
			//M("Flow_".$setting["tablename"])->where("id=".$list)->setField("approvestatus","待提交");
			//M("Flow_".$setting["tablename"])->where("id=".$list)->setField("current","");
		}
		
		if(!empty($_REQUEST["inaccountbill"]))
		{
			$mapforAccount["name"]=$_REQUEST["inaccountbill"];
			$ifexist=M("Account")->where($mapforAccount)->find();
			if(empty($ifexist))
			{
				$dataAccount["name"]=$_REQUEST["inaccountbill"];
				$dataAccount["bank"]=$_REQUEST["inaccountbillbank"];
				$dataAccount["date"]=$_REQUEST["inaccountbilldate"];
				$dataAccount["status"]=1;
				$dataAccount["type"]="承兑汇票";
				$dataAccount["status"]=1;
				$dataAccount["currentstatus"]=1;
				$dataAccount["money"]=$_REQUEST["money"];
				$dataAccount["createperson"]=$_SESSION["name"];
				$dataAccount["ctime"]=time();
				M("Account")->add($dataAccount);
			}
		}
		if(!empty($_REQUEST["outaccountbill"]))
		{
			$mapforAccount["name"]=$_REQUEST["outaccountbill"];
			$ifexist=M("Account")->where($mapforAccount)->find();
			if(!empty($ifexist))
			{
				$dataAccount1["id"]=$ifexist["id"];
				$dataAccount1["currentstatus"]="-1";
				$dataAccount1["usetime"]=time();
				M("Account")->save($dataAccount1);
			}
		}
		
		if($_REQUEST["loantype"]=="贷款")
		{
			$lendermainbody=$_REQUEST["lender"]."-".$_REQUEST["mainbody"];
			M("Flow_loan")->where("id=".$list)->setField("lender-mainbody",$lendermainbody);
		}
		if($_REQUEST["loantype"]=="出借")
		{
			$lendermainbody=$_REQUEST["borrower"]."-".$_REQUEST["mainbody"];
			M("Flow_loan")->where("id=".$list)->setField("lender-mainbody",$lendermainbody);
		}
		
		
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('新增成功!');
		} else {
			//失败提示
			$this->error('新增失败!');
		}
	}
	
	
	
	function modify() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('vo', $vo);
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		$this->assign('tablename',$nodename);
		//$tablename["name"] = $nodename;
		//$tabletitle=M("Node")->where($tablename)->getField("title");
		//$mapforsetting[title]=array("eq",$tabletitle);
		
		$this->assign("setting",$setting);
		for($row=0;$row<=30;$row++)
		{
			if(($setting["pararow".$row])||($setting["pararow".$row]=="0"))
			{
				if(!$line[$setting["pararow".$row]][0])
				{
					$line[$setting["pararow".$row]][0]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire0"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para0"]="para".$row;
					//$line[$setting["pararow".$row]]["value0"]=$vo["para".$row];
					$line[$setting["pararow".$row]]["para0"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["value0"]=$vo[$setting["tableparaname".$row]];
					$line[$setting["pararow".$row]]["paratype0"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselectarray0"]=explode(",",$setting["paraselect".$row]);
					$line[$setting["pararow".$row]]["paraselect0"]=explode(",",$setting["paraselect".$row]);
					if(($line[$setting["pararow".$row]]["paratype0"]=="100")||($line[$setting["pararow".$row]]["paratype0"]=="102"))
					{
						$line[$setting["pararow".$row]]["paraselect0"]=null;
						$paraselect=explode(",",$setting["paraselect".$row]);
						if($paraselect[2])
						{
							$mapforParaselect[$row][0]['_string'] = $paraselect[2];
						}
						$paraselectarray=M($paraselect[0])->where($mapforParaselect[$row][0])->field($paraselect[1])->select();
						foreach($paraselectarray as $key => $val)
						{
							if($val[$paraselect[1]])
							{
								$line[$setting["pararow".$row]]["paraselect0"][$key]=$val[$paraselect[1]];
							}
						}
					}
					
					$line[$setting["pararow".$row]]["effectdisplay0"]=$setting["effectdisplay".$row];
				}
				else
				{
					$line[$setting["pararow".$row]][1]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire1"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para1"]="para".$row;
					//$line[$setting["pararow".$row]]["value1"]=$vo["para".$row];
					$line[$setting["pararow".$row]]["para1"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["value1"]=$vo[$setting["tableparaname".$row]];
					$line[$setting["pararow".$row]]["paratype1"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselectarray1"]=explode(",",$setting["paraselect".$row]);
					$line[$setting["pararow".$row]]["paraselect1"]=explode(",",$setting["paraselect".$row]);
					if(($line[$setting["pararow".$row]]["paratype1"]=="100")||($line[$setting["pararow".$row]]["paratype1"]=="102"))
					{
						$line[$setting["pararow".$row]]["paraselect1"]=null;
						$paraselect=explode(",",$setting["paraselect".$row]);
						if($paraselect[2])
						{
							$mapforParaselect[$row][1]['_string'] = $paraselect[2];
						}
						$paraselectarray=M($paraselect[0])->where($mapforParaselect[$row][1])->field($paraselect[1])->select();
						foreach($paraselectarray as $key => $val)
						{
							if($val[$paraselect[1]])
							{
								$line[$setting["pararow".$row]]["paraselect1"][$key]=$val[$paraselect[1]];
							}
						}
					}
					
					$line[$setting["pararow".$row]]["effectdisplay1"]=$setting["effectdisplay".$row];
				}
				
			}
		}
		
		$projects=M("Plm")->select();
		$this->assign('projects',$projects);
		$agreements=M("agreement")->select();
		$this->assign('agreements',$agreements);
		$mapforSb["status"]=3;
		$buys=M("Sb")->where($mapforSb)->select();
		$this->assign('buys',$buys);
		
		$this->assign('line',$line);
		$companys=M("Company")->select();
		$this->assign('companys',$companys);
		$this->assign('companys1',$companys);
		$depts=M("Dept")->select();
		$this->assign('depts',$depts);
		$this->assign('depts1',$depts);
		$roles=M("Role")->select();
		$this->assign('roles',$roles);
		$this->assign('roles1',$roles);
		$levels=M("Level")->where("status=1")->select();
		$this->assign('levels',$levels);
		$this->assign('levels1',$levels);
		
		$this->display("../Flowform/modify");
	}
	
	function modifysubmit() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		//$tablename["name"] = $nodename;
		//$tabletitle=M("Node")->where($tablename)->getField("title");
		//$mapforsetting[title]=array("eq",$tabletitle);
		$templateid=M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->getField("templateid");
		for($row=0;$row<=30;$row++)
		{
			if($setting["pararequire".$row])
			{
				if(!$_REQUEST["para".$row])
					$this->error("必须填写".$setting["para".$row]."！");
			}
		}
		
		if(($setting["title"]=="投资项目")||($setting["title"]=="税筹合同"))
		{
			$mapforRepeat['id']=array("neq",$_REQUEST["id"]);
			$mapforRepeat['name']=$_POST['name'];
			$repeatinfo=M("Flow_".$setting["tablename"])->where($mapforRepeat)->getField("id");
			if(!empty($repeatinfo))
			{
				$this->error("名称已经存在，请重新输入！");
			}
		}
		
		/*特殊处理*/
		if(($setting["title"]=="税筹")&&($_REQUEST["financetype"]!="票"))
		{
			$mapForMainbody["name"]=$_REQUEST["inmainbody"];
			$mainbody=M("Mainbody")->where($mapForMainbody)->find();
			if(!empty($mainbody))
			{
				$mapForAccount["name"]=$_REQUEST["inaccount"];
				$mapForAccount["type"]=array("neq","承兑汇票");
				$account=M("Account")->where($mapForAccount)->find();
				if(empty($account))
				{
					$this->error("入-主体是公司主体，入-账号不是公司账号");
				}
			}
			
			$mapForMainbody["name"]=$_REQUEST["outmainbody"];
			$mainbody=M("Mainbody")->where($mapForMainbody)->find();
			if(!empty($mainbody))
			{
				$mapForAccount["name"]=$_REQUEST["outaccount"];
				$mapForAccount["type"]=array("neq","承兑汇票");
				$account=M("Account")->where($mapForAccount)->find();
				if(empty($account))
				{
					$this->error("出-主体是公司主体，出-账号不是公司账号");
				}
			}
		}
		if(($setting["title"]=="支出")||($setting["title"]=="转账"))
		{
			if($_REQUEST["outaccounttype"]=="承兑汇票")
			{
				$mapForAccount_bill["name"]=$_REQUEST["outaccountbill"];
				$account_bill=M("Account")->where($mapForAccount_bill)->find();
				if($account_bill["money"]!=$_REQUEST["money"])
				{
					$this->error("支出金额必须与承兑汇票的金额一致");
				}
			}
		}
		
		if(($setting["title"]=="投资"))
		{
			$mapforFlow_plm["name"]=$_REQUEST["plmname"];
			$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
			if(!empty($plminfo["currency"]))
			{
				if($plminfo["currency"]!=$_REQUEST["currency"])
				{
					$this->error("币种不一致!");
				}
			}
				
			$mapforFlow_finance["type"]="投资";
			$mapforFlow_finance["plmname"]=$_REQUEST["plmname"];
			$ifexist=M("Flow_finance")->where($mapforFlow_finance)->find();
			if(!empty($ifexist))
			{
				$mapforFlow_plm["name"]=$_REQUEST["plmname"];
				$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
				if($plminfo["currency"]!=$_REQUEST["currency"])
				{
					$this->error("币种不一致");
				}
			}
		}
		
		if(($setting["title"]=="投资"))
		{
			$mapforFlow_plm["name"]=$_REQUEST["plmname"];
			$plminfo=M("Flow_plm")->where($mapforFlow_plm)->find();
			if(empty($plminfo["currency"]))
			{
				M("Flow_plm")->where("id=".$plminfo["id"])->setField("currency",$_REQUEST["currency"]);
			}
		}
		
		$tablename = $nodename;
		if($_REQUEST[$tablename."onlysave"]!="1")//如果不是保存
		{
			
		}
		
		
		$name = $nodename;
		$mapfornode[name]=$name;
		$nodetitle=M("Node")->where($mapfornode)->getField("title");
		
		$model = D("Flow_".$setting["tablename"]);
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		$model->update_time=time();
		
		$date=date('m-d H:i');
		//$title=$model->title;
		$info=M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->find();
		if($_REQUEST[$tablename."onlysave"]!="1")//如果不是保存
		{
			$model->handlehistory=$info[handlehistory].$_SESSION['loginUserName']."(".$_SESSION['ip'].")"."于".$date."修改了《".$nodetitle."》"."</br>------------------</br>"; 
		}
		
		$current_approver=$_REQUEST["approver"];
		$model->current=$current_approver;
		
		$model->annexhistory=$info[annexhistory];
		
		for($i=0;$i<=14;$i++)
		{
			$tempname="file".$i;
			$temprealname="filerealname".$i;
			$model->$tempname="";
			$model->$temprealname="";
		}
		$k=0;
		for($i=0;$i<=14;$i++)
		{
			//先把老的、还未删除的重新排序
			$tempname="file".$k;
			$temprealname="filerealname".$k;
			if((isset($_REQUEST['record'.$i]))&&($_REQUEST['record'.$i]!=""))
			{
				$model->$tempname=$_REQUEST['record'.$i];
				$model->$temprealname=$_REQUEST['recordrealname'.$i];
				$k++;
			}
		}
		for($i=0;$i<=14;$i++)
		{
			if((isset($_FILES['file']['name'][$i]))&&($_FILES['file']['name'][$i]==""))
			{
				$errormsg='请上传第'.($i+1).'行的图片';
				$this->error($errormsg);
			}
			if(!empty($_FILES['file']['name'][$i]))
			{
				$savePath = '../Public/Uploads/';
				$filename=$_FILES['file']['name'][$i];
				$size = $_FILES['file']['size'][$i]; //文件大小
				$ext = strtolower(end(explode(".",basename($filename))));
				$uuid=uniqid(rand(), false);
				$newname = $uuid.'.'.$ext;
				$upload_file = $savePath.$newname;	
				
				if((false!=strpos($filename,"/"))||(false!=strpos($filename,"\\")))
				{
					$this->error("文件名不能含有特殊字符！");
				}
				if(!in_array(strtolower($ext),array('jpg','jpeg','bmp','png','pdf','zip','rar','7z','doc','docx','xls','xlsx')))
				{
					$this->error("非法文件类型！");
				}
				move_uploaded_file($_FILES['file']['tmp_name'][$i],$upload_file);
				$tempname="file".$i;
				$model->$tempname=$newname;
				$tempname="filerealname".$i;
				$model->$tempname=$filename;
				$model->annexhistory.=$_SESSION['loginUserName']."(".$_SESSION['ip'].")"."于".$date."上传了文档《".$filename."》"."</br>------------------</br>"; 
			}
		}
		
		/*上传图片*/
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		header('Content-Type:text/html;charset=UTF-8');
		$img = $_POST['base64'];
		if (!empty($img)){
			$savePath = '../Public/Uploads/';
			$uuid=uniqid(rand(), false);
			$target = $savePath.$uuid.'.jpg';
			if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
				$img = base64_decode($matches[2]);
				file_put_contents($target, $img);
			}
			$filename=$uuid.'.jpg';
			$newname=$filename;
			$model->file=$newname;
		}
		for($i=1;$i<=5;$i++)
		{
			$tablefilename="file".$i;
			if(!empty($_POST['base64_1']))
			{
				$model->$tablefilename="";
				$dataAccount[$tablefilename]="";
			}
			$img = $_POST['base64_'.$i];
			if (!empty($img)){
				$savePath = '../Public/Uploads/';
				$uuid=uniqid(rand(), false);
				$target = $savePath.$uuid.'.jpg';
				if (preg_match('/data:([^;]*);base64,(.*)/', $img, $matches)) {
					$img = base64_decode($matches[2]);
					file_put_contents($target, $img);
				}
				$filename=$uuid.'.jpg';
				$newname=$filename;
				$model->$tablefilename=$newname;
				$dataAccount[$tablefilename]=$newname;
			}
		}
		
		$model->parahistory=$info[parahistory];
		for($row=0;$row<=30;$row++)
		{
			if($info["para".$row]!=$_REQUEST["para".$row])
			{
				$model->parahistory.=$_SESSION['loginUserName']."(".$_SESSION['ip'].")"."于".$date."将参数【".$setting["para".$row]."】从 <font style='color:blue'>".$info["para".$row]."</font>修改至<font style='color:red'>".$_REQUEST["para".$row]."</font></br>------------------</br>"; 	
					
			}
			
		}
		$model->copy="";
		$model->submitcopy=$_REQUEST[copy];
		$list = $model->save();
		
		/*
		$mapfordept[name]=array("eq",$approver[0]);
		$deptid=M("Dept")->where($mapfordept)->getField("id");
		$mapforrole[name]=array("eq",$approver[1]);
		$roleid=M("Role")->where($mapforrole)->getField("id");
		
		$mapforuser["nickname"]=array("eq",$approver[2]);
		$mapforuser["department"]=array("eq",$deptid);
		$mapforuser["position"]=array("eq",$roleid);
		$userschedule=M("User")->where($mapforuser)->find();
		*/
		if($_REQUEST["loantype"]=="贷款")
		{
			$lendermainbody=$_REQUEST["lender"]."-".$_REQUEST["mainbody"];
			M("Flow_loan")->where("id=".$_REQUEST[id])->setField("lender-mainbody",$lendermainbody);
		}
		if($_REQUEST["loantype"]=="出借")
		{
			$lendermainbody=$_REQUEST["borrower"]."-".$_REQUEST["mainbody"];
			M("Flow_loan")->where("id=".$_REQUEST[id])->setField("lender-mainbody",$lendermainbody);
		}
		
		if($_REQUEST[$tablename."onlysave"]!="1")//如果不是保存
		{
			
		}
		else
		{
			$info=M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->find();
			//M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->setField("approvestatus","待提交");
			//M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->setField("current","");
		}
		
		
		
		if(!empty($_REQUEST["inaccountbill"]))
		{
			$mapforAccount["name"]=$_REQUEST["inaccountbill"];
			$ifexist=M("Account")->where($mapforAccount)->find();
			if(!empty($ifexist))
			{
				$dataAccount["id"]=$ifexist["id"];
				$dataAccount["name"]=$_REQUEST["inaccountbill"];
				$dataAccount["bank"]=$_REQUEST["inaccountbillbank"];
				$dataAccount["date"]=$_REQUEST["inaccountbilldate"];
				M("Account")->save($dataAccount);
			}
		}
		
		if ($list !== false) { //保存成功
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('修改成功!');
		} else {
			//失败提示
			$this->error('修改失败!');
		}
	}
	
	function check() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		$this->assign('vo', $vo);
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		
		//$tablename["name"] = $nodename;
		//$tabletitle=M("Node")->where($tablename)->getField("title");
		//$mapforsetting[title]=array("eq",$tabletitle);
		
		$this->assign("setting",$setting);
		for($row=0;$row<=30;$row++)
		{
			if(($setting["pararow".$row])||($setting["pararow".$row]=="0"))
			{
				if(!$line[$setting["pararow".$row]][0])
				{
					$line[$setting["pararow".$row]][0]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire0"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para0"]="para".$row;
					//$line[$setting["pararow".$row]]["value0"]=$vo["para".$row];
					
					$line[$setting["pararow".$row]]["para0"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["value0"]=$vo[$setting["tableparaname".$row]];
					
					$line[$setting["pararow".$row]]["paratype0"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselect0"]=explode(",",$setting["paraselect".$row]);
				}
				else
				{
					$line[$setting["pararow".$row]][1]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire1"]=$setting["pararequire".$row];
					//$line[$setting["pararow".$row]]["para1"]="para".$row;
					//$line[$setting["pararow".$row]]["value1"]=$vo["para".$row];
					
					$line[$setting["pararow".$row]]["para1"]=$setting["tableparaname".$row];
					$line[$setting["pararow".$row]]["value1"]=$vo[$setting["tableparaname".$row]];
					
					$line[$setting["pararow".$row]]["paratype1"]=$setting["paratype".$row];
					$line[$setting["pararow".$row]]["paraselect1"]=explode(",",$setting["paraselect".$row]);
				}
				
			}
		}
		
		$projects=M("Plm")->select();
		$this->assign('projects',$projects);
		$agreements=M("agreement")->select();
		$this->assign('agreements',$agreements);
		$mapforSb["status"]=3;
		$buys=M("Sb")->where($mapforSb)->select();
		$this->assign('buys',$buys);
		
		$this->assign('line',$line);
		$this->display("../Flowform/check");
	}
	
	
	public function forbid() {
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
        //删除指定记录
        $name = "Flow_".$setting["tablename"];
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
                if (false !== $model->where($condition)->setField("status",0))
				{
                    $this->success('禁用成功！');
                } else {
                    $this->error('禁用失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }
	
	public function resume() {
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
        //删除指定记录
        $name = "Flow_".$setting["tablename"];
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
                if (false !== $model->where($condition)->setField("status",1))
				{
                    $this->success('禁用成功！');
                } else {
                    $this->error('禁用失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }
	
	public function flowforeverdelete() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
        //删除指定记录
        $name = "Flow_".$setting["tablename"];
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
                    $mapforschedule[taskid]=$id;
					$mapforschedule[type]=$nodename;
					M("Schedule")->where($mapforschedule)->setField("status",0);
                    $this->success('永久删除成功！');
                } else {
                    $this->error('永久删除失败！');
                }
            } else {
                $this->error('非法操作');
            }
        }
    }
	
	
	function annexhistory() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$mapforflow[id]=array("eq",$_REQUEST[id]);
		$vo = $model->where($mapforflow)->find();
		$this->assign('vo', $vo);
		$this->display("../Flowform/annexhistory");
	}
	function parahistory() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$mapforflow[id]=array("eq",$_REQUEST[id]);
		$vo = $model->where($mapforflow)->find();
		$this->assign('vo', $vo);
		$this->display("../Flowform/parahistory");
	}
	function handlehistory() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$mapforflow[id]=array("eq",$_REQUEST[id]);
		$vo = $model->where($mapforflow)->find();
		$this->assign('vo', $vo);
		$this->display("../Flowform/handlehistory");
	}
	
	function printme() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$name = "Flow_".$setting["tablename"];
		$model = M($name);
		$id = $_REQUEST [$model->getPk()];
		$vo = $model->getById($id);
		
		$mapforshenfenzheng[title]=$vo[title];
		$shenfenzheng=M("Userfiles")->where($mapforshenfenzheng)->getField("identification");
		$this->assign('shenfenzheng', $shenfenzheng);
		
		$this->assign('vo', $vo);
		$_SESSION['curpage']=$_REQUEST['currentPage'];
		
		
		//$tablename["name"] = $nodename;
		//$tabletitle=M("Node")->where($tablename)->getField("title");
		//$mapforsetting[title]=array("eq",$tabletitle);
		$templateid=M("Flow_".$setting["tablename"])->where("id=".$_REQUEST[id])->getField("templateid");
		$mapforsetting[id]=$templateid;
		$setting=M("Settingform")->where($mapforsetting)->find();
		$this->assign("setting",$setting);
		for($row=0;$row<=30;$row++)
		{
			if(($setting["pararow".$row])||($setting["pararow".$row]=="0"))
			{
				if(!$line[$setting["pararow".$row]][0])
				{
					$line[$setting["pararow".$row]][0]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire0"]=$setting["pararequire".$row];
					$line[$setting["pararow".$row]]["para0"]="para".$row;
					$line[$setting["pararow".$row]]["value0"]=$vo["para".$row];
					$line[$setting["pararow".$row]]["paraselect0"]=explode(",",$setting["paraselect".$row]);
				}
				else
				{
					$line[$setting["pararow".$row]][1]=$setting["para".$row];
					$line[$setting["pararow".$row]]["pararequire1"]=$setting["pararequire".$row];
					$line[$setting["pararow".$row]]["para1"]="para".$row;
					$line[$setting["pararow".$row]]["value1"]=$vo["para".$row];
					$line[$setting["pararow".$row]]["paraselect1"]=explode(",",$setting["paraselect".$row]);
				}
				
			}
		}
		$this->assign('line',$line);
		$this->display("../Flowform/printme");
		
	}
	
	function getSource() {
		if($_REQUEST["condition"])
		{
			$mapforParaselect['_string'] = $_REQUEST["condition"];
		}
		$mapforParaselect[$_REQUEST["bindingpara"]]=$_REQUEST["value"];
		$subways=M($_REQUEST["bindingtable"])->where($mapforParaselect)->field($_REQUEST["para"])->select();
		foreach($subways as $key => $val)
		{
			$subways[$key]["name"] = $val[$_REQUEST["para"]];
		}
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	
	function setdefaultvalue() {
		
		$mapforsetting[id]=$_REQUEST[templateid];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		$nodename=$_REQUEST[nodename];
		$this->assign("nodename",$_REQUEST[nodename]);
		$data=M("Settingform")->select();
		$time=time();
		foreach($data as $key => $val)
		{
			M("Settingform")->where("id=".$val[id])->setField("create_time",$time);
			M("Settingform")->where("id=".$val[id])->setField("status",1);
		}
		
		
		$flowdata=M("Flow_".$setting["tablename"])->select();
		foreach($flowdata as $key => $val)
		{
			$mapfornode["name"]=$val[type];
			$tabletitle=M("Node")->where($mapfornode)->getField("title");
			$mapforsetting[title]=array("eq",$tabletitle);
			$mapforsetting[status]=1;
			$templateid=M("Settingform")->where($mapforsetting)->getField("id");
			M("Flow_".$setting["tablename"])->where("id=".$val[id])->setField("templateid",$templateid);
		}
	}
	
	function filedown()
	{
		$file_name=filter_var(htmlspecialchars($_REQUEST[file]), FILTER_CALLBACK, array("options"=>"convertSpace"));
		$file_downname=filter_var(htmlspecialchars($_REQUEST[filerealname]), FILTER_CALLBACK, array("options"=>"convertSpace"));
		$file_dir = '../Public/Uploads/';
        if (!file_exists($file_dir . $file_name))
        { 
            $this->error('文件不存在');
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
            //exit;
		} 
	}
	
	
	
	public function toexcel(){
		
		$mapforsetting[title]=$_REQUEST[nodename];
		$setting=M("Settingform")->where($mapforsetting)->find();
		
		//处理数据，得到$data数组
		$map = $this->_search();
		if (method_exists($this, '_filter')) {
			$this->_filter($map);
		}

		if(!empty($_REQUEST['approvestatus']))
		{
			$map['approvestatus'] = $_REQUEST['approvestatus'];
			$this->assign('approvestatus',$_REQUEST['approvestatus']);	
		}
		if(!empty($_REQUEST['user']))
		{
			$map['user'] = array("like","%".$_REQUEST['user']."%");
			$this->assign('user',$_REQUEST['user']);
		}
		if(!empty($_REQUEST['para']))
		{
			$para=str_replace("~!@","/",$_REQUEST['para']);
			$para=str_replace("@!~","&",$para);
			$tablename["name"] = $_REQUEST["nodename"];
			$tabletitle=M("Node")->where($tablename)->getField("title");
			$mapforsetting[title]=array("eq",$tabletitle);
			$mapforsetting[status]=1;
			$setting=M("Settingform")->where($mapforsetting)->find();
			$parakey=0;
			
			for($parakey=0;$parakey<=30;$parakey++)
			{
				$str="para".$parakey;
				if($setting[$str]==$para)
				{
					break;
				}
			}
			if($parakey!=30)
			{
				$map[$str] = array("like","%".$_REQUEST['searchtitle']."%");
				$this->assign('searchtitle',$_REQUEST['searchtitle']);
			}
			$_SESSION[searchstr]=$str;
		}
		if(!empty($_REQUEST['searchtitle']))
		{
			$map[$_SESSION[searchstr]] = array("like","%".$_REQUEST['searchtitle']."%");
			$this->assign('searchtitle',$_REQUEST['searchtitle']);
		}
		
		if((!empty($_REQUEST['timebegin']))&&(empty($_REQUEST['timeend'])))
		$map['create_time'] = array('egt',strtotime($_REQUEST['timebegin']));
		else if((empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array('elt',strtotime($_REQUEST['timeend'])+24*60*60);
		else if((!empty($_REQUEST['timebegin']))&&(!empty($_REQUEST['timeend'])))
		$map['create_time'] = array(array('egt',strtotime($_REQUEST['timebegin'])),array('elt',strtotime($_REQUEST['timeend'])+24*60*60),'and');
		$this->assign('timebegin', $_REQUEST['timebegin']);
		$this->assign('timeend', $_REQUEST['timeend']);

		
		if(($_SESSION[name]!="管理员"))
		{
			$where[user_id]=array("eq",$_SESSION[number]);//提交人
			$where[copy]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//自动抄送人和手动抄送人
			$where[current]=array("like","%".$_SESSION[name].$_SESSION[number]."%");//当前审批人
			$where[history]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//历史审批人
			$where[relativeexceptlast]=array("like","%".$_SESSION[name].$_SESSION[number].",%");//relativeexceptlast//需要执行人
			$where['_logic'] = 'OR';
			$map['_complex'] = $where;
		}
		
		
		$map[type]=array("eq",$_REQUEST["nodename"]);
		

		$tablename["name"] = $_REQUEST["nodename"];
		$tabletitle=M("Node")->where($tablename)->getField("title");
		
		$mapforsetting[title]=array("eq",$tabletitle);
		$mapforsetting[status]=1;
		$setting=M("Settingform")->where($mapforsetting)->find();
		$this->assign("setting",$setting);
		$this->assign("nodename",$_REQUEST["nodename"]);
		
		
		$this->assign("check",$_REQUEST["check"]);

		$name = "Flow_".$setting["tablename"];
		$model = D($name);
		$list=$model->where($map)->order("create_time desc")->select();
		
		$th_array=array('序号');
		
		for($i=0;$i<30;$i++)
		{
			if(!empty($setting["para".$i]))
			{
				array_push($th_array,$setting["para".$i]);
			}
		}
		foreach($list as $k=>$v){
			$data[$k]['xuhao'] = ($k+1);
			for($i=0;$i<30;$i++)
			{
				if(!empty($setting["para".$i]))
				{
					$data[$k][$setting["para".$i]] = $v[$setting["tableparaname".$i]];
				}
			}
		}
		$file=$setting["title"];
		$title=$setting["title"];
		$subtitle=$setting["title"];
		$excelname=$setting["title"];
		$this->createExel($file,$title,$subtitle,$th_array,$data,$excelname);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*特殊处理*/
	function getSource1() {
		
		$mapforParaselect['financetype'] = $_REQUEST["accounttype"];
		$mapforParaselect['plmtype'] = $_REQUEST["plmtype"];
		$mapforParaselect['plmsubtype'] = $_REQUEST["plmsubtype"];
		$subways=M("Flow_plmfinancetype")->where($mapforParaselect)->field("name")->select();
		foreach($subways as $key => $val)
		{
			$subways[$key]["name"] = $val["name"];
		}
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	function getSource2() {
		
		$mapforParaselect['name'] = $_REQUEST["account"];
		$subways=M("Account")->where($mapforParaselect)->field("currency")->select();
		foreach($subways as $key => $val)
		{
			$subways[$key]["name"] = $val["currency"];
			if(empty($val["currency"]))
			{
				$subways[$key]["name"] = "人民币";
			}
		}
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	function getSource3() {
		
		$mapforParaselect['name'] = $_REQUEST["agreement"];
		$data=M("Flow_fax")->where($mapforParaselect)->find();
		
		
		$data["tax"]=$data["contractmoney"];//票
		$data["contractmoney"]=$data["contractmoney"];//公户
		$data["returnmoney"]=$data["contractmoney"]-$data["money"]-$data["taxmoney"];//私户
		
		$mapforFlow_finance['agreement'] = $_REQUEST["agreement"];
		$mapforFlow_finance["status"]=1;
		$taxpay=M("Flow_finance")->where($mapforFlow_finance)->sum("taxpay");
		$contractmoneypay=M("Flow_finance")->where($mapforFlow_finance)->sum("contractmoneypay");
		$returnmoneypay=M("Flow_finance")->where($mapforFlow_finance)->sum("returnmoneypay");
		
		$data["taxleft"]=$data["tax"]-$taxpay;
		$data["contractmoneyleft"]=$data["contractmoney"]-$contractmoneypay;
		$data["returnmoneyleft"]=$data["returnmoney"]-$returnmoneypay;
		
		
	
		
		
		
		$this->assign('data',$data);
		echo json_encode($data);
	}
	
	
	function getSource4() {
		/*
		$mapforParaselect['name'] = $_REQUEST["plmname"];
		$subways=M("Flow_plm")->where($mapforParaselect)->getField("currency");
		$this->assign('subways',$subways);
		*/
		$mapforParaselect['name'] = $_REQUEST["name"];
		$subways=M("Account")->where($mapforParaselect)->getField("currency");
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	function getSource4_1() {
		$mapforParaselect['name'] = $_REQUEST["name"];
		$subways=M("Flow_plm")->where($mapforParaselect)->getField("currency");
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	
	function getSource5() {
		
		$mapforParaselect['name'] = $_REQUEST["account"];
		$subways=M("Account")->where($mapforParaselect)->getField("money");
		$this->assign('subways',$subways);
		echo json_encode($subways);
	}
	
	function getSource6() {
		
		$mapforParaselect['loaner'] = $_REQUEST["loaner"];
		$mapforParaselect['accounttype'] = "授信";
		$money0=M("Flow_finance")->where($mapforParaselect)->sum("money");
		
		$mapforParaselect['loaner'] = $_REQUEST["loaner"];
		$mapforParaselect['accounttype'] = "贷";
		$money1=M("Flow_finance")->where($mapforParaselect)->sum("money");
		
		$mapforParaselect['loaner'] = $_REQUEST["loaner"];
		$mapforParaselect['accounttype'] = "还";
		$money2=M("Flow_finance")->where($mapforParaselect)->sum("money");
		
		
		$data[1]=$money1-$money2;
		$data[0]=$money0-$data[1];
		
		$this->assign('data',$data);
		echo json_encode($data);
	}
}
?>