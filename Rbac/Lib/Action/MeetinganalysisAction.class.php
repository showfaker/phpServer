<?php
class MeetinganalysisAction extends CommonAction
{
	public function index(){

		$count1 = M('meetingassignment')->count();

		$map2["status"] = "0";
		$count2 = M('meetingassignment')->where($map2)->count();

		$map3["status"] = "1";
		$count3 = M('meetingassignment')->where($map3)->count();

		$map4["status"] = "2";
		$count4 = M('meetingassignment')->where($map4)->count();

		$map5["status"] = "3";
		$count5 = M('meetingassignment')->where($map5)->count();

		$count6 = M('result')->count();

		$map7['status']= "0";
		$count7 = M('result')->where($map7)->count();

		$map8['status']= "1";
		$count8 = M('result')->where($map8)->count();

		$map9['status']= "2";
		$count9 = M('result')->where($map9)->count();

		$map10['status']= "3";
		$count10 = M('result')->where($map10)->count();

		$this->assign('count1', $count1);
		$this->assign('count2', $count2);
		$this->assign('count3', $count3);
		$this->assign('count4', $count4);
		$this->assign('count5', $count5);
		$this->assign('count6', $count6);
		$this->assign('count7', $count7);
		$this->assign('count8', $count8);
		$this->assign('count9', $count9);
		$this->assign('count10', $count10);

		$meetingplan = M("meetingplan")->select();
		foreach($meetingplan as $k => $v){
			$meetingassignment = M('meetingassignment')->where("plan_id=".$v['id'])->select();
			$all = count($meetingassignment);
			$allmap1['status'] = "0";
			$allmap1['plan_id'] = $v['id'];
			$all1 =  M('meetingassignment')->where($allmap1)->count();
			$allmap2['status'] = "1";
			$allmap2['plan_id'] = $v['id'];
			$all2 =  M('meetingassignment')->where($allmap2)->count();
			$allmap3['status'] = "2";
			$allmap3['plan_id'] = $v['id'];
			$all3 =  M('meetingassignment')->where($allmap3)->count();
			$allmap4['status'] = "3";
			$allmap4['plan_id'] = $v['id'];
			$all4 =  M('meetingassignment')->where($allmap4)->count();
			$data[] =array($all,$all1,$all2,$all3,$all4);
			$ids = array();
			foreach($meetingassignment as $key => $val){
				$ids[] = $val['id'];
			}
			$allmap5['meet_id'] = array("in",$ids);
			$all5 = M('result')->where($allmap5)->count();
			$allmap6['meet_id'] = array("in",$ids);
			$allmap6['status'] = "0";
			$all6 = M('result')->where($allmap6)->count();
			$allmap7['meet_id'] = array("in",$ids);
			$allmap7['status'] = "1";
			$all7 = M('result')->where($allmap7)->count();
			$allmap8['meet_id'] = array("in",$ids);
			$allmap8['status'] = "2";
			$all8 = M('result')->where($allmap8)->count();
			$allmap9['meet_id'] = array("in",$ids);
			$allmap9['status'] = "3";
			$all9 = M('result')->where($allmap9)->count();
			
			$data1[] =array($all5,$all6,$all7,$all8,$all9);

		}
		$this->assign("data",json_encode($data));
		$this->assign("data1",json_encode($data1));
		$this->assign("meetingplan",$meetingplan);
		$this->assign("count",count($meetingplan));
		// foreach($taketypearray as $key => $val){
		// 	$map1['taketype'] = array('eq',$val);
		// 	$map2['taketype'] = array('eq',$val);
		// 	$map3['taketype'] = array('eq',$val);
		// 	$map4['taketype'] = array('eq',$val);
		// 	$map5['taketype'] = array('eq',$val);
		// 	$map6['taketype'] = array('eq',$val);
		// 	$count1_1 = M('Project')->where($map1)->count();
		// 	$count2_1 = M('Project')->where($map2)->count();
		// 	$count3_1 = M('Project')->where($map3)->count();
		// 	$count4_1 = M('Project')->where($map4)->count();
		// 	$count5_1 = M('Project')->where($map5)->count();
		// 	$count6_1 = M('Project')->where($map6)->count();
		// 	$data1=array($count1_1,$count2_1,$count3_1,$count4_1,$count5_1,$count6_1);
		// 	$this->assign('data'.($key+1), json_encode($data1));
		// }
	
		if($_SESSION[app]){
			$this->display(indexapp);
		}else{
			$this->display(indexoa);
		}
		return;
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
