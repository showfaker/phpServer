<?php
class PictureAction extends CommonAction
{
	public function index()
	{
		
		$this->assign('homepage',$_REQUEST["homepage"]);
		/*
		if(empty($_REQUEST["homepage"]))
		{
			$map1["projecttype"]="充电建设";
			$map2["projecttype"]="充电建设";
			$map3["projecttype"]="充电建设";
			$this->assign('projecttype',"充电建设");
		}
		if(($_REQUEST["homepage"]==1))
		{
			$map1["projecttype"]="换电建设";
			$map2["projecttype"]="换电建设";
			$map3["projecttype"]="换电建设";
			$this->assign('projecttype',"换电建设");
		}
		if(($_REQUEST["homepage"])==2)
		{
			$map1["projecttype"]="低速车建设";
			$map2["projecttype"]="低速车建设";
			$map3["projecttype"]="低速车建设";
			$this->assign('projecttype',"低速车建设");
		}
		*/
			
		$map1[design_status]=array("in","立项中");
		$count1 = M('Project')->where($map1)->count();
		$map2[design_status]=array("in","待施工");
		$count2 = M('Project')->where($map2)->count();
		$map3['design_status'] = array('in',"施工中");
		$count3 = M('Project')->where($map3)->count();
		$map4['design_status'] = array('in',"施工完成,完成施工");
		$count4 = M('Project')->where($map4)->count();
		$map5['design_status'] = array('in',"验收完成,完成验收");
		$count5 = M('Project')->where($map5)->count();
		$map6['design_status'] = array('eq',"暂停中");
		$count6 = M('Project')->where($map6)->count();
		$map7['design_status'] = array('eq',"取消");
		$count7 = M('Project')->where($map7)->count();
		
		$mapforprojectfortopforwarning[warning]=array("eq","1");
		$mapforprojectfortopforwarning[status]=array("eq","1");
		$plmwarnings=M("Plmwarning")->where($mapforprojectfortopforwarning)->group("plmid")->select();
		foreach($plmwarnings as $key => $val)
		{
			$plmwarningids.=$val["plmid"].",";
		}
		$mapforprojectfortopforwarningapprove[status]=array("eq","1");
		$plmwarnings=M("Plmwarningapprove")->where($mapforprojectfortopforwarningapprove)->group("plmid")->select();
		foreach($plmwarnings as $key => $val)
		{
			$plmwarningids.=$val["plmid"].",";
		}
		$map6['id'] = array('in',$plmwarningids);
		$count6=M("Project")->where($map6)->count();
		if(empty($count6))$count6=0;
		
		$this->assign('count1', $count1);
		$this->assign('count2', $count2);
		$this->assign('count3', $count3);
		$this->assign('count4', $count4);
		$this->assign('count5', $count5);
		$this->assign('count6', $count6);
		$this->assign('count7', $count7);
		
		$taketypearray=array("分布式光伏发电","集中式光伏发电","风力发电");
		
	
		foreach($taketypearray as $key => $val)
		{
			$map1['projecttype'] = array('eq',$val);
			$map2['projecttype'] = array('eq',$val);
			$map3['projecttype'] = array('eq',$val);
			$map4['projecttype'] = array('eq',$val);
			$map5['projecttype'] = array('eq',$val);
			$map6['projecttype'] = array('eq',$val);
			$map7['projecttype'] = array('eq',$val);
			$count1_1 = M('Project')->where($map1)->count();
			$count2_1 = M('Project')->where($map2)->count();
			$count3_1 = M('Project')->where($map3)->count();
			$count4_1 = M('Project')->where($map4)->count();
			$count5_1 = M('Project')->where($map5)->count();
			$count6_1 = M('Project')->where($map6)->count();
			$count7_1 = M('Project')->where($map7)->count();
			$data1=array($count1_1,$count3_1,$count4_1,$count5_1,$count6_1,$count7_1);
			$this->assign('data'.($key+1), json_encode($data1));
		}
		
		
		$this->display();
	}

	function prDates($start, $end)  //开始结束时间算天数
	{
		$array = array();
		$dt_start = strtotime($start);
		$dt_end = strtotime($end);
		while ($dt_start <= $dt_end) {
			$array[] = date('Y-m-d', $dt_start);
			$dt_start = strtotime('+1 day', $dt_start);
		}
		return $array;
	}

	function prDate($start, $end)
	{
		$where["siteid"]=$_SESSION["siteid"];
		$whe["siteid"]=$_SESSION["siteid"];
		$a["siteid"]=$_SESSION["siteid"];
		
		$array = array();
		$dt_start = strtotime($start); //开始时间
		$dt_end = strtotime($end); //结束时间
		while ($dt_start <= $dt_end) {
			$t = $dt_start;
			$dt_start = strtotime('+1 day', $dt_start);
			$where['ctime'] = array('between', array($t, $dt_start));
			$name = 'para1';
			$d = M('data2')->where($where)->field($name)->find();
			if ($d) {
				$array[] = $d['para1'];
			} else {
				$array[] = 0;
			}
		}
		return $array;
	}
	function prDateb($start, $end)
	{
		$where["siteid"]=$_SESSION["siteid"];
		$whe["siteid"]=$_SESSION["siteid"];
		$a["siteid"]=$_SESSION["siteid"];
		
		$array = array();
		$dt_start = strtotime($start); //开始时间
		$dt_end = strtotime($end); //结束时间
		while ($dt_start <= $dt_end) {
			$t = $dt_start;
			$dt_start = strtotime('+1 day', $dt_start);
			$where['ctime'] = array('between', array($t, $dt_start));
			$name = 'para1';
			$d = M('data1')->where($where)->field($name)->find();
			if ($d) {
				$array[] = $d['para1'];
			} else {
				$array[] = 0;
			}
		}
		return $array;
	}


	function getTime()
	{
		$where["siteid"]=$_SESSION["siteid"];
		$whe["siteid"]=$_SESSION["siteid"];
		$d["siteid"]=$_SESSION["siteid"];
		
		$name1 = 'para3';
		$name2 = 'para5';
		$name3 = 'para7';
		$time = date('Y-m-d H:i:59');
		$time = strtotime($time);
		for ($i = 0; $i < 25; $i++) {
			$a_time = $time;
			$b_time = $time - 60;
			// 1606891595
			$where['create_time'] = array('between', array($b_time, $a_time));
			$d = M('data3')->where($where)->find();
			if ($d) {
				$date['one'][] = $d[$name1];
				$date['two'][] = $d[$name2];
				$date['three'][] = $d[$name3];
			} else {
				$date['one'][] = 0;
				$date['two'][] = 0;
				$date['three'][] = 0;
			}
			$date['time'][] = date("H:i", $a_time);
			$time = $b_time;
		}
		$date['one'] = array_reverse($date['one']);
		$date['two'] = array_reverse($date['two']);
		$date['three'] = array_reverse($date['three']);
		$date['time'] = array_reverse($date['time']);
		return $date;
	}
	function getTimes()
	{
		$where["siteid"]=$_SESSION["siteid"];
		$whe["siteid"]=$_SESSION["siteid"];
		$d["siteid"]=$_SESSION["siteid"];
		
		$name1 = $_REQUEST['name1'];
		$name2 = $_REQUEST['name2'];
		$name3 = $_REQUEST['name3'];
		$time = date('Y-m-d H:i:59');
		$time = strtotime($time);
		for ($i = 0; $i < 25; $i++) {
			$a_time = $time;
			$b_time = $time - 60;
			$where['create_time'] = array('between', array($b_time, $a_time));
			$d = M('data3')->where($where)->find();
			if ($d) {
				$date['one'][] = $d[$name1];
				$date['two'][] = $d[$name2];
				$date['three'][] = $d[$name3];
			} else {
				$date['one'][] = 0;
				$date['two'][] = 0;
				$date['three'][] = 0;
			}
			$date['time'][] = date("H:i", $a_time);
			$time = $b_time;
		}
		$date['one'] = array_reverse($date['one']);
		$date['two'] = array_reverse($date['two']);
		$date['three'] = array_reverse($date['three']);
		$date['time'] = array_reverse($date['time']);
		if(!$name2){
			unset($date['two']);
		}
		if(!$name3){
			unset($date['three']);
		}
		echo(json_encode($date));
	}
}
